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
        Schema::create('qr_codes', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('code', 5)->unique(); // 5-digit unique code
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            
            // QR Design customization
            $table->string('logo_path')->nullable();
            $table->string('foreground_color')->default('#000000');
            $table->string('background_color')->default('#ffffff');
            $table->enum('style', ['square', 'dot', 'rounded'])->default('square');
            $table->integer('size')->default(300);
            $table->string('error_correction', 1)->default('M'); // L, M, Q, H
            
            // QR Image storage
            $table->string('qr_image_path')->nullable();
            
            // Status and metadata
            $table->enum('status', ['active', 'inactive', 'expired'])->default('active');
            $table->integer('scan_count')->default(0);
            $table->timestamp('last_scanned_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            
            // User association
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('uuid');
            $table->index('code');
            $table->index('status');
            $table->index('created_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('qr_codes');
    }
};