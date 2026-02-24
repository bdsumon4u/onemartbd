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
        Schema::table('web_settings', function (Blueprint $table): void {
            $table->unsignedInteger('extra_special_discount_amount')
                ->nullable()
                ->after('auto_flag_fake_on_limit');

            $table->unsignedTinyInteger('extra_special_discount_chance')
                ->nullable()
                ->after('extra_special_discount_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('web_settings', function (Blueprint $table): void {
            $table->dropColumn([
                'extra_special_discount_amount',
                'extra_special_discount_chance',
            ]);
        });
    }
};

