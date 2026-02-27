<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Drops old per-IP/Phone/UA minute/hour/day columns and adds unified
     * order_limit_per_minute, order_limit_per_hour, order_limit_per_day,
     * plus restrict-by checkboxes.
     */
    public function up(): void
    {
        $oldColumns = [
            'order_limit_per_ip_per_minute',
            'order_limit_per_ip_per_hour',
            'order_limit_per_ip_per_day',
            'order_limit_per_phone_per_minute',
            'order_limit_per_phone_per_hour',
            'order_limit_per_phone_per_day',
            'order_limit_per_user_agent_per_minute',
            'order_limit_per_user_agent_per_hour',
            'order_limit_per_user_agent_per_day',
        ];
        $colsToDrop = array_filter($oldColumns, fn ($col) => Schema::hasColumn('web_settings', $col));
        if (! empty($colsToDrop)) {
            Schema::table('web_settings', fn (Blueprint $table) => $table->dropColumn($colsToDrop));
        }

        Schema::table('web_settings', function (Blueprint $table) {
            if (! Schema::hasColumn('web_settings', 'order_limit_per_minute')) {
                $table->unsignedInteger('order_limit_per_minute')->nullable()->after('is_order_defender_enabled');
            }
            if (! Schema::hasColumn('web_settings', 'order_limit_per_hour')) {
                $table->unsignedInteger('order_limit_per_hour')->nullable()->after('order_limit_per_minute');
            }
            if (! Schema::hasColumn('web_settings', 'order_limit_per_day')) {
                $table->unsignedInteger('order_limit_per_day')->nullable()->after('order_limit_per_hour');
            }
            if (! Schema::hasColumn('web_settings', 'order_defender_restrict_by_ip')) {
                $table->boolean('order_defender_restrict_by_ip')->default(true)->after('order_limit_per_day');
            }
            if (! Schema::hasColumn('web_settings', 'order_defender_restrict_by_phone')) {
                $table->boolean('order_defender_restrict_by_phone')->default(true)->after('order_defender_restrict_by_ip');
            }
            if (! Schema::hasColumn('web_settings', 'order_defender_restrict_by_user_agent')) {
                $table->boolean('order_defender_restrict_by_user_agent')->default(true)->after('order_defender_restrict_by_phone');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('web_settings', function (Blueprint $table) {
            $dropColumns = [
                'order_limit_per_minute',
                'order_limit_per_hour',
                'order_limit_per_day',
                'order_defender_restrict_by_ip',
                'order_defender_restrict_by_phone',
                'order_defender_restrict_by_user_agent',
            ];
            foreach ($dropColumns as $col) {
                if (Schema::hasColumn('web_settings', $col)) {
                    $table->dropColumn($col);
                }
            }
        });

        Schema::table('web_settings', function (Blueprint $table) {
            $table->unsignedInteger('order_limit_per_ip_per_minute')->nullable()->after('is_order_defender_enabled');
            $table->unsignedInteger('order_limit_per_ip_per_hour')->nullable()->after('order_limit_per_ip_per_minute');
            $table->unsignedInteger('order_limit_per_ip_per_day')->nullable()->after('order_limit_per_ip_per_hour');
            $table->unsignedInteger('order_limit_per_phone_per_minute')->nullable()->after('order_limit_per_ip_per_day');
            $table->unsignedInteger('order_limit_per_phone_per_hour')->nullable()->after('order_limit_per_phone_per_minute');
            $table->unsignedInteger('order_limit_per_phone_per_day')->nullable()->after('order_limit_per_phone_per_hour');
            $table->unsignedInteger('order_limit_per_user_agent_per_minute')->nullable()->after('order_limit_per_phone_per_day');
            $table->unsignedInteger('order_limit_per_user_agent_per_hour')->nullable()->after('order_limit_per_user_agent_per_minute');
            $table->unsignedInteger('order_limit_per_user_agent_per_day')->nullable()->after('order_limit_per_user_agent_per_hour');
        });
    }
};
