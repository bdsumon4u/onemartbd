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
        if (Schema::hasTable('carry_bee_apis')) {
            Schema::table('carry_bee_apis', function (Blueprint $table): void {
                if (! Schema::hasColumn('carry_bee_apis', 'is_active')) {
                    $table->boolean('is_active')->default(false)->after('id');
                }

                if (! Schema::hasColumn('carry_bee_apis', 'store_id')) {
                    $table->string('store_id')->nullable()->after('is_active');
                }

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

            return;
        }

        Schema::create('carry_bee_apis', function (Blueprint $table): void {
            $table->id();
            $table->boolean('is_active')->default(false);
            $table->string('store_id')->nullable();
            $table->string('client_id')->nullable();
            $table->string('client_secret')->nullable();
            $table->string('client_context')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('carry_bee_apis')) {
            return;
        }

        Schema::dropIfExists('carry_bee_apis');
    }
};
