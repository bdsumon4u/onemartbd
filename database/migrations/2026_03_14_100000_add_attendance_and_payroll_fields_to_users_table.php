<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('users')) {
            return;
        }

        Schema::table('users', function (Blueprint $table): void {
            if (! Schema::hasColumn('users', 'role')) {
                $table->tinyInteger('role')->nullable()->after('password');
            }

            if (! Schema::hasColumn('users', 'start_time')) {
                $table->time('start_time')->nullable()->after('status');
            }

            if (! Schema::hasColumn('users', 'end_time')) {
                $table->time('end_time')->nullable()->after('start_time');
            }

            if (! Schema::hasColumn('users', 'panel_start')) {
                $table->time('panel_start')->nullable()->after('end_time');
            }

            if (! Schema::hasColumn('users', 'panel_end')) {
                $table->time('panel_end')->nullable()->after('panel_start');
            }

            if (! Schema::hasColumn('users', 'order_start')) {
                $table->time('order_start')->nullable()->after('panel_end');
            }

            if (! Schema::hasColumn('users', 'order_end')) {
                $table->time('order_end')->nullable()->after('order_start');
            }

            if (! Schema::hasColumn('users', 'monthly_salary')) {
                $table->decimal('monthly_salary', 10, 2)->default(0)->after('order_end');
            }

            if (! Schema::hasColumn('users', 'off_days')) {
                $table->text('off_days')->nullable()->after('monthly_salary');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('users')) {
            return;
        }

        Schema::table('users', function (Blueprint $table): void {
            $columns = [
                'role',
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
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
