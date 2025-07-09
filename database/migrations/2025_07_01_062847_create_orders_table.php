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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->enum('status', ['pending','delivered', 'cancelled']);
            $table->foreignId('created_by')->constrained('users');
            $table->enum('delivery_option', ['pickup', 'door_delivery']);
            $table->date('expected_fulfillment_date')->nullable();
            $table->string('decline_reason')->nullable();
            $table->decimal('total', 12, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
