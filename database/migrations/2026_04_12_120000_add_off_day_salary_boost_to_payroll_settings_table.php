<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payroll_settings', function (Blueprint $table): void {
            if (! Schema::hasColumn('payroll_settings', 'off_day_salary_boost')) {
                $table->decimal('off_day_salary_boost', 4, 2)->default(1.5)->after('latetime_unit_minutes');
            }
        });
    }

    public function down(): void
    {
        Schema::table('payroll_settings', function (Blueprint $table): void {
            if (Schema::hasColumn('payroll_settings', 'off_day_salary_boost')) {
                $table->dropColumn('off_day_salary_boost');
            }
        });
    }
};
