<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('demand_prediction_results', function (Blueprint $table) {
            $table->id();
            $table->string('shirt_category', 50);
            $table->date('prediction_date');
            $table->integer('predicted_quantity');
            $table->string('time_frame', 20); // e.g., '30_days', '12_months', '5_years'
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('demand_prediction_results');
    }
};
