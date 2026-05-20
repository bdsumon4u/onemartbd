<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('call_automation_settings', function (Blueprint $table) {
            $table->id();
            $table->string('api_key')->nullable();
            $table->string('did')->nullable();
            $table->text('maintext')->nullable();
            $table->text('text1')->nullable();
            $table->text('text2')->nullable();
            $table->string('call_url')->nullable();
            $table->string('retry_url')->nullable();
            $table->string('check_response_url')->nullable();
            $table->timestamps();
        });

        // Seed with documented API defaults so admin UI shows initial values
        DB::table('call_automation_settings')->insert([
            'api_key' => config('services.call_automation.api_key', ''),
            'did' => '09643301133',
            'maintext' => 'Hello dear customer, this is a confirmation call regarding your recent order with us. Please press 1 to confirm your order or press 2 to cancel your order.',
            'text1' => 'Thanks For Pressing 1',
            'text2' => 'Thanks For Pressing 2',
            'call_url' => 'https://ccs.teamitqan.com/api/MakeTextCall/Call',
            'retry_url' => 'https://ccs.teamitqan.com/api/MakeTextCall/tts_a_retry',
            'check_response_url' => 'https://ccs.teamitqan.com/api/MakeTextCall/CheckResponse',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('call_automation_settings');
    }
};
