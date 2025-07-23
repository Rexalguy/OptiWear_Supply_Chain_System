<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE vendor_orders MODIFY COLUMN delivery_option ENUM('delivery', 'pickup', 'express') NOT NULL DEFAULT 'pickup'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE vendor_orders MODIFY COLUMN delivery_option ENUM('delivery', 'pickup') NOT NULL DEFAULT 'pickup'");
    }
};
