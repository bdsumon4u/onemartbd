<?php

declare(strict_types=1);

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
        Schema::table('carry_bee_apis', function (Blueprint $table): void {
            if (! Schema::hasColumn('carry_bee_apis', 'client_id')) {
                $table->string('client_id')->nullable()->after('store_id');
            }

            if (! Schema::hasColumn('carry_bee_apis', 'client_secret')) {
                $table->string('client_secret')->nullable()->after('client_id');
            }

            if (! Schema::hasColumn('carry_bee_apis', 'client_context')) {
                $table->string('client_context')->nullable()->after('client_secret');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('carry_bee_apis', function (Blueprint $table): void {
            $columns = array_filter([
                'client_id',
                'client_secret',
                'client_context',
            ], static fn (string $column): bool => Schema::hasColumn('carry_bee_apis', $column));

            if ($columns === []) {
                return;
            }

            $table->dropColumn($columns);
        });
    }
};
