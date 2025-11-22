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
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            
            // Basic Information
            $table->string('name');
            $table->integer('age');
            $table->date('date_of_birth')->nullable();
            $table->string('email')->unique();
            $table->string('password');
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            
            // Physical Information
            $table->decimal('height', 5, 2)->nullable()->comment('in cm');
            $table->decimal('weight', 5, 2)->nullable()->comment('in kg');
            $table->enum('blood_type', ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'])->nullable();
            
            // Medical Information
            $table->text('allergies')->nullable();
            $table->json('emergency_contacts')->nullable(); // Array of contacts
            $table->json('chronic_conditions')->nullable(); // Array of conditions
            $table->json('current_medications')->nullable(); // Array of medications
            $table->text('medical_history')->nullable();
            $table->json('medical_files')->nullable(); // Array of file paths
            $table->text('important_note')->nullable(); // The comment field
            
            // Profile Image
            $table->string('profile_image')->nullable();
            
            // Status
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active');
            $table->timestamp('last_login_at')->nullable();
            $table->rememberToken();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('email');
            $table->index('uuid');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};