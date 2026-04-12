<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('abandoned_carts', function (Blueprint $table) {
            $table->unsignedBigInteger('master_id')->nullable()->after('id');
            $table->unsignedBigInteger('slave_id')->nullable()->after('master_id');
            $table->string('slave_domain')->nullable()->after('slave_id');
            $table->string('forwarding_status')->nullable()->after('total');
            $table->text('forwarding_last_error')->nullable()->after('forwarding_status');
            $table->string('ip_address', 191)->nullable()->after('forwarding_last_error');
            $table->string('utm_source', 150)->nullable()->after('ip_address');
            $table->string('source', 191)->nullable()->after('utm_source');

            $table->unique(['slave_domain', 'slave_id'], 'abandoned_carts_slave_domain_slave_id_unique');
        });
    }

    public function down(): void
    {
        Schema::table('abandoned_carts', function (Blueprint $table) {
            $table->dropUnique('abandoned_carts_slave_domain_slave_id_unique');
            $table->dropColumn([
                'master_id',
                'slave_id',
                'slave_domain',
                'forwarding_status',
                'forwarding_last_error',
                'ip_address',
                'utm_source',
                'source',
            ]);
        });
    }
};
