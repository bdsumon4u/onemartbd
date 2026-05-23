<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('call_automation_settings', function (Blueprint $table): void {
            $table->boolean('enabled')->default(true)->after('api_key');
        });

        DB::table('call_automation_settings')->update(['enabled' => true]);
    }

    public function down(): void
    {
        Schema::table('call_automation_settings', function (Blueprint $table): void {
            $table->dropColumn('enabled');
        });
    }
};
