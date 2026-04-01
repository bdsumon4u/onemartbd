<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Http\Middleware\EnsureTrustedDevice;
use App\Models\Admin;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Order;
use App\Models\OrderAssign;
use App\Models\PayrollSetting;
use App\Models\SalaryAdvance;
use App\Models\UserBonus;
use App\Services\PayrollGenerationService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Date;
use Tests\TestCase;

class AttendancePayrollModuleTest extends TestCase
{
    use DatabaseTransactions;

    public function test_self_attendance_check_in_and_check_out(): void
    {
        $this->withoutMiddleware(EnsureTrustedDevice::class);

        $admin = Admin::query()->create([
            'name' => 'Admin One',
            'email' => 'admin-self@example.com',
            'password' => bcrypt('password'),
            'status' => 1,
        ]);

        $this->actingAs($admin, 'admin');

        PayrollSetting::current()->update(['allow_self_checkout' => true]);

        $inResponse = $this->postJson(route('admin.my_attendance.toggle'));
        $inResponse->assertOk()->assertJson(['status' => 'checked_in']);

        $outResponse = $this->postJson(route('admin.my_attendance.toggle'));
        $outResponse->assertOk()->assertJson(['status' => 'checked_out']);
    }

    public function test_self_checkout_disabled(): void
    {
        $this->withoutMiddleware(EnsureTrustedDevice::class);

        $admin = Admin::query()->create([
            'name' => 'Admin Two',
            'email' => 'admin-nocheckout@example.com',
            'password' => bcrypt('password'),
            'status' => 1,
        ]);

        $this->actingAs($admin, 'admin');

        PayrollSetting::current()->update(['allow_self_checkout' => false]);

        $this->postJson(route('admin.my_attendance.toggle'))->assertOk();
        $this->postJson(route('admin.my_attendance.toggle'))
            ->assertStatus(422)
            ->assertJson(['status' => 'checkout_disabled']);
    }

    public function test_manual_attendance_create_and_edit_and_extra_overtime_preserved(): void
    {
        $this->withoutMiddleware(EnsureTrustedDevice::class);

        $admin = Admin::query()->create([
            'name' => 'Admin Three',
            'email' => 'admin-manual@example.com',
            'password' => bcrypt('password'),
            'status' => 1,
        ]);

        $this->actingAs($admin, 'admin');

        $staff = Employee::query()->create([
            'name' => 'Staff One',
            'email' => 'staff-manual@example.com',
            'phone' => '01700000001',
            'password' => bcrypt('password'),
            'status' => 1,
            'monthly_salary' => 30000,
            'start_time' => '10:00:00',
            'end_time' => '20:00:00',
        ]);

        $this->post(route('admin.attendance.store'), [
            'staff_key' => 'employee:'.$staff->id,
            'date' => '2026-03-10',
            'check_in' => '10:00',
            'check_out' => '21:30',
            'note' => 'manual',
        ])->assertRedirect();

        $attendance = Attendance::query()
            ->where('staff_type', 'employee')
            ->where('staff_id', $staff->id)
            ->whereDate('date', '2026-03-10')
            ->firstOrFail();
        $this->assertSame(90, (int) $attendance->overtime_minutes);
        $this->assertSame(0, (int) $attendance->late_minutes);

        $this->post(route('admin.attendance.update', $attendance), [
            'check_in' => '10:00',
            'check_out' => '19:00',
            'extra_overtime_minutes' => 45,
            'penalty_amount' => 20,
            'note' => 'edited',
            'auto_checkout' => false,
        ])->assertRedirect();

        $attendance = $attendance->fresh();
        $this->assertSame(0, (int) $attendance->overtime_minutes);
        $this->assertSame(60, (int) $attendance->late_minutes);
        $this->assertSame(45, (int) $attendance->extra_overtime_minutes);
    }

    public function test_auto_checkout_penalty_behavior(): void
    {
        $staff = Employee::query()->create([
            'name' => 'Auto Checkout Staff',
            'email' => 'auto-checkout@example.com',
            'phone' => '01700000002',
            'password' => bcrypt('password'),
            'status' => 1,
            'start_time' => '10:00:00',
            'end_time' => '20:00:00',
        ]);

        Attendance::query()->create([
            'staff_type' => 'employee',
            'staff_id' => $staff->id,
            'date' => '2026-03-14',
            'check_in' => '2026-03-14 09:30:00',
            'status' => 'present',
        ]);

        PayrollSetting::current()->update(['forgot_checkout_penalty' => 100]);

        Date::setTestNow('2026-03-14 21:00:00');
        Artisan::call('attendance:auto-checkout');

        $attendance = Attendance::query()
            ->where('staff_type', 'employee')
            ->where('staff_id', $staff->id)
            ->whereDate('date', '2026-03-14')
            ->firstOrFail();
        $this->assertTrue((bool) $attendance->auto_checkout);
        $this->assertSame('2026-03-14 20:00:00', $attendance->check_out?->format('Y-m-d H:i:s'));
        $this->assertSame('100.00', (string) $attendance->penalty_amount);

        Date::setTestNow();
    }

    public function test_payroll_generation_with_hazira_bonus_special_bonus_advance_and_xsell_and_idempotency(): void
    {
        $employee = Employee::query()->create([
            'name' => 'Emp One',
            'email' => 'employee-payroll@example.com',
            'phone' => '01710000000',
            'password' => bcrypt('password'),
            'status' => 1,
            'start_time' => '10:00:00',
            'end_time' => '20:00:00',
        ]);

        $employee->update([
            'monthly_salary' => 30000,
            'off_days' => 'Friday',
            'start_time' => '10:00:00',
            'end_time' => '20:00:00',
        ]);

        Attendance::query()->create([
            'staff_type' => 'employee',
            'staff_id' => $employee->id,
            'date' => '2026-03-01',
            'check_in' => '2026-03-01 10:00:00',
            'check_out' => '2026-03-01 20:00:00',
            'status' => 'present',
            'late_minutes' => 0,
            'overtime_minutes' => 0,
        ]);

        UserBonus::query()->create([
            'staff_type' => 'employee',
            'staff_id' => $employee->id,
            'name' => 'Performance Bonus',
            'amount' => 500,
            'year' => 2026,
            'month' => '03',
        ]);

        SalaryAdvance::query()->create([
            'staff_type' => 'employee',
            'staff_id' => $employee->id,
            'amount' => 200,
            'date' => '2026-03-05',
        ]);

        $order = Order::query()->create([
            'invoice_id' => uniqid('INV-TEST-', true),
            'customer_name' => 'Test Customer',
            'customer_phone' => '01700000011',
            'customer_address' => 'Dhaka',
            'utm_source' => 'direct',
            'status' => OrderStatus::Delivered->value,
            'ordered_quantity' => 2,
            'delivered_quantity' => 3,
            'delivered_at' => '2026-03-06 10:00:00',
        ]);

        OrderAssign::query()->create([
            'order_id' => $order->id,
            'employee_id' => $employee->id,
        ]);

        $service = app(PayrollGenerationService::class);
        $first = $service->generateForUser($employee, 3, 2026, null);
        $second = $service->generateForUser($employee, 3, 2026, null);

        $this->assertSame($first->id, $second->id);
        $this->assertSame(1, $employee->monthlyPayrolls()->where('month', 3)->where('year', 2026)->count());
        $this->assertSame('500.00', (string) $second->occasional_bonus_amount);
        $this->assertSame('200.00', (string) $second->advance_deduction);
        $this->assertSame('5.00', (string) $second->xsell_bonus_amount);
    }
}
