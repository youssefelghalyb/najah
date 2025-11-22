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
        Schema::create('profile_qr_code', function (Blueprint $table) {
            $table->id();
            $table->foreignId('profile_id')->constrained('profiles')->onDelete('cascade');
            $table->foreignId('qr_code_id')->constrained('qr_codes')->onDelete('cascade');
            $table->timestamp('linked_at');
            $table->foreignId('linked_by')->nullable()->constrained('users')->onDelete('set null');
            $table->text('notes')->nullable();
            $table->timestamps();
            
            // Unique constraint - one QR can only be linked to one profile at a time
            $table->unique('qr_code_id');
            
            // Index for faster lookups
            $table->index('profile_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profile_qr_code');
    }
};