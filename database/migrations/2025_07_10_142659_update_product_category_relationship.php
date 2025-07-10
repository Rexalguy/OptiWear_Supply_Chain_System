<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up(): void
{
    // Remove incorrect foreign key from shirt_categories
    Schema::table('shirt_categories', function (Blueprint $table) {
        $table->dropForeign(['product_id']); // only if it exists
        $table->dropColumn('product_id');
    });

    // Add correct foreign key to products
    Schema::table('products', function (Blueprint $table) {
        $table->foreignId('shirt_category_id')->constrained()->onDelete('cascade');
    });
}

public function down(): void
{
    // Rollback changes
    Schema::table('products', function (Blueprint $table) {
        $table->dropForeign(['shirt_category_id']);
        $table->dropColumn('shirt_category_id');
    });

    Schema::table('shirt_categories', function (Blueprint $table) {
        $table->foreignId('product_id')->constrained()->onDelete('cascade');
    });
}

};
