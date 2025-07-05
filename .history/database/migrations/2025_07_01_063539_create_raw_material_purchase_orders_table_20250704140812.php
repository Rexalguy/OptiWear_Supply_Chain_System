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
        Schema::create('raw_material_purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('raw_material_id')->constrained('raw_materials');
            $table->foreignId('supplier_id')->constrained('users');
            $table->integer('quantity');
            $table->decimal('unit_price', 10, 2);
            $table->dateTime('order_date');
            $table->dateTime('expected_delivery');
            $table->enum('status', ['pending', 'approved', 'received', 'cancelled']);
            $table->text('notes')->nullable();
            $table->enum('delivery_option', ['pickup', 'door_delivery']);
            $table->decimal('total_price', 12, 2);
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('raw_material_purchase_orders');
    }
};
