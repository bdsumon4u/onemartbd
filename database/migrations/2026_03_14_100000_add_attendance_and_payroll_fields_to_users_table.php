<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['admins', 'managers', 'employees'] as $tableName) {
            if (! Schema::hasTable($tableName)) {
                continue;
            }

            Schema::table($tableName, function (Blueprint $table) use ($tableName): void {
                if (! Schema::hasColumn($tableName, 'start_time')) {
                    $table->time('start_time')->nullable()->after('status');
                }

                if (! Schema::hasColumn($tableName, 'end_time')) {
                    $table->time('end_time')->nullable()->after('start_time');
                }

                if (! Schema::hasColumn($tableName, 'panel_start')) {
                    $table->time('panel_start')->nullable()->after('end_time');
                }

                if (! Schema::hasColumn($tableName, 'panel_end')) {
                    $table->time('panel_end')->nullable()->after('panel_start');
                }

                if (! Schema::hasColumn($tableName, 'order_start')) {
                    $table->time('order_start')->nullable()->after('panel_end');
                }

                if (! Schema::hasColumn($tableName, 'order_end')) {
                    $table->time('order_end')->nullable()->after('order_start');
                }

                if (! Schema::hasColumn($tableName, 'monthly_salary')) {
                    $table->decimal('monthly_salary', 10, 2)->default(0)->after('order_end');
                }

                if (! Schema::hasColumn($tableName, 'off_days')) {
                    $table->text('off_days')->nullable()->after('monthly_salary');
                }
            });
        }
    }

    public function down(): void
    {
        foreach (['admins', 'managers', 'employees'] as $tableName) {
            if (! Schema::hasTable($tableName)) {
                continue;
            }

            Schema::table($tableName, function (Blueprint $table) use ($tableName): void {
                $columns = [
                    'start_time',
                    'end_time',
                    'panel_start',
                    'panel_end',
                    'order_start',
                    'order_end',
                    'monthly_salary',
                    'off_days',
                ];

                foreach ($columns as $column) {
                    if (Schema::hasColumn($tableName, $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }
    }
};
