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
        Schema::table('orders', function (Blueprint $table) {
            if (! Schema::hasColumn('orders', 'call_campaign_id')) {
                $table->string('call_campaign_id')->nullable()->after('forwarding_last_error');
            }

            if (! Schema::hasColumn('orders', 'ai_confirmation_status')) {
                $table->string('ai_confirmation_status')->nullable()->after('call_campaign_id');
            }

            if (! Schema::hasColumn('orders', 'ai_confirmation_checked_at')) {
                $table->timestamp('ai_confirmation_checked_at')->nullable()->after('ai_confirmation_status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'ai_confirmation_checked_at')) {
                $table->dropColumn('ai_confirmation_checked_at');
            }

            if (Schema::hasColumn('orders', 'ai_confirmation_status')) {
                $table->dropColumn('ai_confirmation_status');
            }

            if (Schema::hasColumn('orders', 'call_campaign_id')) {
                $table->dropColumn('call_campaign_id');
            }
        });
    }
};
