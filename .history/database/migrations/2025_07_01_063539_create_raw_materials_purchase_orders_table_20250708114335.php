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
        Schema::create('raw_materials_purchase_orders', function (Blueprint $table) {
            $table->id();
            $table  ->unsignedBigInteger('raw_material_id');
            $table->unsignedBigInteger('supplier_id');
            $table->decimal('quantity', 10, 2); // Quantity of raw material ordered
            $table->decimal('price_per_unit', 10, 2); // Price per unit of raw material // Date when the order was placed
            $table->dateTime('expected_delivery_date'); // Expected delivery date for the order
            $table->enum('status', ['pending', 'confirmed', 'delivered', 'cancelled'])->default('pending'); // Status of the order
            $table->string('notes')->nullable(); // Additional notes for the order
            $table->enum('delivery_option', ['delivery', 'pickup'])->default('standard');
            $table->decimal('total_price', 10,2); // Delivery option for the order
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade'); // User who created the order
            
            
            $table->foreign('raw_material_id')->references('id')->on('raw_materials')->onDelete('cascade');
            $table->foreign('supplier_id')->references('id')->on('users')->onDelete('cascade'); // Assuming suppliers are stored in the users table
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('raw_materials_purchase_orders');
    }
};