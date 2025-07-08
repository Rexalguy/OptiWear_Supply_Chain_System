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
        Schema::create('raw_materials', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->foreignId('supplier_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('category_id')->constrained('raw_material_category')->onDelete('cascade');
            $table->integer('current_stock')->default(0);
            $table->integer('reorder_level')->default(0);
            $table->string('unit_of_measure');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('raw_material');
    }
};