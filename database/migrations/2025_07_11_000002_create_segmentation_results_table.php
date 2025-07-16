<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('segmentation_results', function (Blueprint $table) {
            $table->id();
            $table->string('segment_label', 100);
            $table->string('gender', 10);
            $table->string('age_group', 20);
            $table->string('shirt_category', 50);
            $table->integer('total_purchased');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('segmentation_results');
    }
};
