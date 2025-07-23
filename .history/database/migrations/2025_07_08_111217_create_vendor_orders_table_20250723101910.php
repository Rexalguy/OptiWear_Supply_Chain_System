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
        Schema::create('vendor_orders', function (Blueprint $table) {
            $table->id();
            $table->enum('status',['pending','confirmed','delivered','cancelled',])->default('pending');
            
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->enum('delivery_option',['delivery','pickup','express'])->default('pickup');
            $table->decimal('total', 10, 2);
            $table->dateTime('order_date')->nullable();
            $table->dateTime('expected_fulfillment')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendor_orders');
    }
};