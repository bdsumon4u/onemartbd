<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payroll_settings', function (Blueprint $table): void {
            $table->id();
            $table->decimal('overtime_rate', 10, 2)->default(50);
            $table->integer('overtime_unit_minutes')->default(60);
            $table->decimal('latetime_rate', 10, 2)->default(0);
            $table->integer('latetime_unit_minutes')->default(60);
            $table->decimal('forgot_checkout_penalty', 10, 2)->default(100);
            $table->boolean('allow_self_checkout')->default(true);
            $table->decimal('hazira_bonus', 10, 2)->default(500);
            $table->decimal('xsell_bonus_rate', 10, 2)->default(5);
            $table->timestamps();
        });

        DB::table('payroll_settings')->insert([
            'overtime_rate' => 50,
            'overtime_unit_minutes' => 60,
            'latetime_rate' => 0,
            'latetime_unit_minutes' => 60,
            'forgot_checkout_penalty' => 100,
            'allow_self_checkout' => true,
            'hazira_bonus' => 500,
            'xsell_bonus_rate' => 5,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('payroll_settings');
    }
};
