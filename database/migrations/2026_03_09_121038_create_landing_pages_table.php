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
        Schema::create('landing_pages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->string('subtitle')->nullable();
            $table->string('slug')->unique();
            $table->unsignedBigInteger('banner_image')->nullable();
            $table->string('about_section_head')->nullable();
            $table->longText('about_section_body')->nullable();
            $table->text('gallery_images')->nullable(); // comma-separated media IDs
            $table->string('gallery_section_head')->default('Gallery Images');
            $table->string('why_section_head')->nullable();
            $table->longText('why_section_body')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();

            $table->foreign('banner_image')->references('id')->on('media')->onDelete('set null');
            $table->index(['status', 'slug']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('landing_pages');
    }
};
