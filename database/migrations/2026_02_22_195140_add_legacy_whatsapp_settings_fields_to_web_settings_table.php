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
            if (! Schema::hasColumn('web_settings', 'wp_phone_number_id')) {
                $table->string('wp_phone_number_id')->nullable()->after('button_hover_color');
            }
        });

        Schema::table('web_settings', function (Blueprint $table) {
            if (! Schema::hasColumn('web_settings', 'wp_access_token')) {
                $table->text('wp_access_token')->nullable()->after('wp_phone_number_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $columns = array_filter([
            'wp_phone_number_id',
            'wp_access_token',
        ], static fn (string $column): bool => Schema::hasColumn('web_settings', $column));

        if ($columns === []) {
            return;
        }

        Schema::table('web_settings', function (Blueprint $table) use ($columns): void {
            $table->dropColumn($columns);
        });
    }
};
