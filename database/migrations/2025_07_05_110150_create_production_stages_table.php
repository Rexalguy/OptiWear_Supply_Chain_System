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
        Schema::create('production_stages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('production_order_id')->constrained()->onDelete('cascade');
            $table->string('stage'); // printing, packaging, delivery
            $table->foreignId('workforce_id')->nullable()->constrained()->onDelete('set null');
            $table->enum('status', ['pending', 'in_progress', 'completed'])->default('pending');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('production_stages');
    }
};
