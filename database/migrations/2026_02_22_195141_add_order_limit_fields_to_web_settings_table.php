<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('web_settings', function (Blueprint $table) {
            $table->boolean('is_order_defender_enabled')->default(false)->after('wp_access_token');
            $table->unsignedInteger('order_limit_per_ip_per_minute')->nullable()->after('is_order_defender_enabled');
            $table->unsignedInteger('order_limit_per_ip_per_hour')->nullable()->after('order_limit_per_ip_per_minute');
            $table->unsignedInteger('order_limit_per_ip_per_day')->nullable()->after('order_limit_per_ip_per_hour');
            $table->unsignedInteger('order_limit_per_phone_per_minute')->nullable()->after('order_limit_per_ip_per_day');
            $table->unsignedInteger('order_limit_per_phone_per_hour')->nullable()->after('order_limit_per_phone_per_minute');
            $table->unsignedInteger('order_limit_per_phone_per_day')->nullable()->after('order_limit_per_phone_per_hour');
            $table->boolean('auto_block_ip_on_limit')->default(false)->after('order_limit_per_phone_per_day');
            $table->boolean('auto_flag_fake_on_limit')->default(true)->after('auto_block_ip_on_limit');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('web_settings', function (Blueprint $table) {
            $table->dropColumn([
                'is_order_defender_enabled',
                'order_limit_per_ip_per_minute',
                'order_limit_per_ip_per_hour',
                'order_limit_per_ip_per_day',
                'order_limit_per_phone_per_minute',
                'order_limit_per_phone_per_hour',
                'order_limit_per_phone_per_day',
                'auto_block_ip_on_limit',
                'auto_flag_fake_on_limit',
            ]);
        });
    }
};
