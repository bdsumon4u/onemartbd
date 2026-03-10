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
            $table->boolean('is_sms_enabled')->default(true)->after('order_custom_sms');
            $table->time('sms_start_time')->nullable()->after('is_sms_enabled');
            $table->time('sms_end_time')->nullable()->after('sms_start_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('web_settings', function (Blueprint $table) {
            $table->dropColumn(['is_sms_enabled', 'sms_start_time', 'sms_end_time']);
        });
    }
};
