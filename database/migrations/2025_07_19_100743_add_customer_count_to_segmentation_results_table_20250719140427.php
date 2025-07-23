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
        Schema::table('segmentation_results', function (Blueprint $table) {
            $table->integer('customer_count')->default(0)->after('total_purchased');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('segmentation_results', function (Blueprint $table) {
            $table->dropColumn('customer_count');
        });
    }
};
