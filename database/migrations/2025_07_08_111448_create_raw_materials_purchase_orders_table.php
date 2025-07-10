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
            $table->foreignId('raw_materials_id')->constrained('raw_materials')->onDelete('cascade');
            $table->foreignId('supplier_id')->constrained('users')->onDelete('cascade');
            $table->integer('quantity');
            $table->decimal('price_per_unit', 10, 2);
            $table->timestamp('expected_delivery_date')->nullable();
            $table->enum('status',['pending', 'confirmed', 'delivered', 'cancelled'])
            ->default('pending');// e.g., pending, confirmed, delivered, cancelled
            $table->text('notes')->nullable();
            $table->enum('delivery_option',['delivery','pickup'])->default('pickup'); // e.g., delivery, pickup
            $table->decimal('total_price', 10, 2);
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
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
