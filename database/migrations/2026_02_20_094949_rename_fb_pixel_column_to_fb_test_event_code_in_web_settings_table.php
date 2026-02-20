<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('web_settings', function (Blueprint $table) {
            $table->renameColumn('fb_pixel', 'fb_test_event_code');
        });

        DB::table('web_settings')->update(['fb_test_event_code' => '']);

        Schema::table('web_settings', function (Blueprint $table) {
            $table->string('fb_test_event_code')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('web_settings', function (Blueprint $table) {
            $table->renameColumn('fb_test_event_code', 'fb_pixel');
        });

        DB::table('web_settings')->update(['fb_pixel' => '']);

        Schema::table('web_settings', function (Blueprint $table) {
            $table->text('fb_pixel')->nullable()->change();
        });
    }
};
