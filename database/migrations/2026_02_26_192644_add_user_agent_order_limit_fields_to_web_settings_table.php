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
            $table->unsignedInteger('order_limit_per_user_agent_per_minute')
                ->nullable()
                ->after('order_limit_per_phone_per_day');

            $table->unsignedInteger('order_limit_per_user_agent_per_hour')
                ->nullable()
                ->after('order_limit_per_user_agent_per_minute');

            $table->unsignedInteger('order_limit_per_user_agent_per_day')
                ->nullable()
                ->after('order_limit_per_user_agent_per_hour');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('web_settings', function (Blueprint $table) {
            $table->dropColumn([
                'order_limit_per_user_agent_per_minute',
                'order_limit_per_user_agent_per_hour',
                'order_limit_per_user_agent_per_day',
            ]);
        });
    }
};
