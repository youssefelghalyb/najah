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
        Schema::create('bundles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            
            // Discount Settings (Applied to sum of current product prices)
            $table->decimal('discount_amount', 10, 2)->default(0)->comment('Fixed amount or percentage value');
            $table->enum('discount_type', ['fixed', 'percentage'])->default('fixed');
            
            // Status
            $table->enum('status', ['active', 'inactive', 'archived'])->default('active');
            $table->boolean('is_featured')->default(false);
            
            // Images
            $table->string('image_path')->nullable();
            $table->json('gallery_images')->nullable();
            
            // SEO & Meta
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            
            // Tracking
            $table->integer('views_count')->default(0);
            $table->integer('orders_count')->default(0);
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('status');
            $table->index('is_featured');
            $table->index('slug');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bundles');
    }
};