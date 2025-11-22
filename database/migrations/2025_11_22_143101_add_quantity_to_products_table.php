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
        Schema::table('bundles', function (Blueprint $table) {
            $table->integer('stock_quantity')->after('status')->default(0);
            $table->integer('stock_quantity_alert')->after('stock_quantity')->default(0);

        });

        Schema::table('orders', function (Blueprint $table) {
            $table->enum('return_status' , ['pending','approved', 'rejected'  , 'completed'])->default('pending');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bundles', function (Blueprint $table) {
            $table->dropColumn('stock_quantity');
            $table->dropColumn('stock_quantity_alert');
        });


        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('return_status');
        });
    }
};
