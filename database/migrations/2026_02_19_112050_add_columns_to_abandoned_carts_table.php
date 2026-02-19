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
        Schema::table('abandoned_carts', function (Blueprint $table) {
            $table->unsignedBigInteger('employee_id')->nullable()->after('shipping_id');
            $table->tinyInteger('status')->default(0)->after('employee_id')->comment('0=Active, 1=Cancelled');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('abandoned_carts', function (Blueprint $table) {
            $table->dropColumn(['employee_id', 'status', 'cancellation_reason']);
        });
    }
};
