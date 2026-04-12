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
        if (Schema::hasColumn('web_settings', 'order_defender_blocked_utm_sources')) {
            return;
        }

        Schema::table('web_settings', function (Blueprint $table) {
            $table->text('order_defender_blocked_utm_sources')
                ->nullable()
                ->after('order_defender_restrict_by_user_agent');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasColumn('web_settings', 'order_defender_blocked_utm_sources')) {
            return;
        }

        Schema::table('web_settings', function (Blueprint $table) {
            $table->dropColumn('order_defender_blocked_utm_sources');
        });
    }
};
