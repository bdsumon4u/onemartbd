<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add columns only if they don't already exist
        if (! Schema::hasColumn('products', 'master_id')) {
            Schema::table('products', function (Blueprint $table): void {
                $table->unsignedBigInteger('master_id')->nullable()->after('id');
            });
        }

        if (! Schema::hasColumn('products', 'slave_id')) {
            Schema::table('products', function (Blueprint $table): void {
                $table->unsignedBigInteger('slave_id')->nullable()->after('master_id');
            });
        }

        if (! Schema::hasColumn('products', 'slave_domain')) {
            Schema::table('products', function (Blueprint $table): void {
                $table->string('slave_domain')->nullable()->after('slave_id');
            });
        }

        if (! Schema::hasColumn('products', 'forwarding_status')) {
            Schema::table('products', function (Blueprint $table): void {
                $table->string('forwarding_status')->nullable();
            });
        }

        if (! Schema::hasColumn('products', 'forwarding_last_error')) {
            Schema::table('products', function (Blueprint $table): void {
                $table->text('forwarding_last_error')->nullable();
            });
        }

        // Create unique index if it doesn't exist
        $indexExists = (bool) DB::selectOne("SELECT COUNT(1) AS c FROM information_schema.statistics WHERE table_schema = DATABASE() AND table_name = 'products' AND index_name = 'products_slave_domain_slave_id_unique'")->c;
        if (! $indexExists) {
            Schema::table('products', function (Blueprint $table): void {
                $table->unique(['slave_domain', 'slave_id'], 'products_slave_domain_slave_id_unique');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop unique index if exists
        try {
            $indexExists = (bool) DB::selectOne("SELECT COUNT(1) AS c FROM information_schema.statistics WHERE table_schema = DATABASE() AND table_name = 'products' AND index_name = 'products_slave_domain_slave_id_unique'")->c;
            if ($indexExists) {
                Schema::table('products', function (Blueprint $table): void {
                    $table->dropUnique('products_slave_domain_slave_id_unique');
                });
            }
        } catch (\Throwable $e) {
            // ignore
        }

        // Drop columns if they exist
        $cols = ['master_id', 'slave_id', 'slave_domain', 'forwarding_status', 'forwarding_last_error'];
        foreach ($cols as $col) {
            if (Schema::hasColumn('products', $col)) {
                Schema::table('products', function (Blueprint $table) use ($col): void {
                    $table->dropColumn($col);
                });
            }
        }
    }
};
