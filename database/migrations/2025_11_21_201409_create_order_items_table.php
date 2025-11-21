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
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            
            // Item Type (product or bundle)
            $table->enum('item_type', ['product', 'bundle']);
            $table->unsignedBigInteger('item_id'); // product_id or bundle_id
            
            // Item Details (snapshot at time of order)
            $table->string('item_name');
            $table->text('item_description')->nullable();
            $table->decimal('unit_price', 10, 2); // Price per item at time of order
            $table->integer('quantity')->default(1);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('total', 10, 2); // (unit_price * quantity) - discount
            
            // QR Code Assignment (single QR per order item)
            $table->foreignId('qr_code_id')->nullable()->constrained('qr_codes')->onDelete('set null');
            $table->timestamp('qr_assigned_at')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index(['item_type', 'item_id']);
            $table->index('order_id');
            $table->index('qr_code_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};