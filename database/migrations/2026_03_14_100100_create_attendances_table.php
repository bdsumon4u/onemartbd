<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->timestamp('check_in')->nullable();
            $table->timestamp('check_out')->nullable();
            $table->boolean('is_off_day')->default(false);
            $table->integer('overtime_minutes')->default(0);
            $table->unsignedInteger('extra_overtime_minutes')->default(0);
            $table->integer('late_minutes')->default(0);
            $table->decimal('penalty_amount', 10, 2)->default(0);
            $table->boolean('auto_checkout')->default(false);
            $table->string('status')->default('present');
            $table->text('note')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
