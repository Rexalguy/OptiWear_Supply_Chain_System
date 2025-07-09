<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Rename table
        Schema::rename('raw_material_category', 'raw_material_categories');

        // Add `is_available` to workforces
        Schema::table('workforces', function (Blueprint $table) {
            $table->boolean('is_available')->default(true)->after('job');
        });

        // Make `stage` an ENUM in production_stages with default 'printing'
        Schema::table('production_stages', function (Blueprint $table) {
            $table->enum('stage', ['printing', 'packaging', 'delivery'])->default('printing')->change();
        });

        // Add `alert_threshold` to raw_materials
        Schema::table('raw_materials', function (Blueprint $table) {
            $table->integer('alert_threshold')->default(0)->after('reorder_level');
        });

        // Add `size` and `unit_price` to order_items (size default null)
        Schema::table('order_items', function (Blueprint $table) {
            $table->enum('size', ['S', 'M', 'L', 'XL'])->nullable()->default(null)->after('quantity');
            $table->decimal('unit_price', 10, 2)->after('size');
        });

        // Add `size` and `unit_price` to vendor_order_items (size default null)
        Schema::table('vendor_order_items', function (Blueprint $table) {
            $table->enum('size', ['S', 'M', 'L', 'XL'])->nullable()->default(null)->after('quantity');
            $table->decimal('unit_price', 10, 2)->after('size');
        });

        // Add default values to existing enums in other tables:
        // production_orders.status default 'pending'
        Schema::table('production_orders', function (Blueprint $table) {
            $table->enum('status', ['pending','in_progress','completed','cancelled'])->default('pending')->change();
        });

        // orders.status default 'pending'
        Schema::table('orders', function (Blueprint $table) {
            $table->enum('status', ['pending','confirmed','delivered','cancelled'])->default('pending')->change();
            $table->enum('delivery_option', ['delivery','pickup'])->default('pickup')->change();
        });

        // vendor_orders.status default 'pending'
        Schema::table('vendor_orders', function (Blueprint $table) {
            $table->enum('status', ['pending','confirmed','delivered','cancelled'])->default('pending')->change();
            $table->enum('delivery_option', ['delivery','pickup'])->default('pickup')->change();
        });

        // raw_materials_purchase_orders.status default 'pending'
        Schema::table('raw_materials_purchase_orders', function (Blueprint $table) {
            $table->enum('status', ['pending','confirmed','delivered','cancelled'])->default('pending')->change();
            $table->enum('delivery_option', ['delivery','pickup'])->default('pickup')->change();
        });

        // vendor_validations.is_valid default false (tinyint 1)
        Schema::table('vendor_validations', function (Blueprint $table) {
            $table->boolean('is_valid')->default(false)->change();
        });

        // production_stages.status default 'pending'
        Schema::table('production_stages', function (Blueprint $table) {
            $table->enum('status', ['pending','in_progress','completed'])->default('pending')->change();
        });

        // users.role default 'customer'
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['customer','vendor','supplier','admin','manufacturer'])->default('customer')->change();
        });

        // users.gender default 'other'
        Schema::table('users', function (Blueprint $table) {
            $table->enum('gender', ['male','female','other'])->default('other')->nullable()->change();
        });
    }

    public function down(): void
    {
        // Rename table back
        Schema::rename('raw_material_categories', 'raw_material_category');

        Schema::table('workforces', function (Blueprint $table) {
            $table->dropColumn('is_available');
        });

        Schema::table('production_stages', function (Blueprint $table) {
            $table->string('stage')->change();
            $table->enum('status', ['pending','in_progress','completed'])->default('pending')->change();
        });

        Schema::table('raw_materials', function (Blueprint $table) {
            $table->dropColumn('alert_threshold');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn(['size', 'unit_price']);
        });

        Schema::table('vendor_order_items', function (Blueprint $table) {
            $table->dropColumn(['size', 'unit_price']);
        });

        Schema::table('production_orders', function (Blueprint $table) {
            $table->enum('status', ['pending','in_progress','completed','cancelled'])->change();
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->enum('status', ['pending','confirmed','delivered','cancelled'])->change();
            $table->enum('delivery_option', ['delivery','pickup'])->change();
        });

        Schema::table('vendor_orders', function (Blueprint $table) {
            $table->enum('status', ['pending','confirmed','delivered','cancelled'])->change();
            $table->enum('delivery_option', ['delivery','pickup'])->change();
        });

        Schema::table('raw_materials_purchase_orders', function (Blueprint $table) {
            $table->enum('status', ['pending','confirmed','delivered','cancelled'])->change();
            $table->enum('delivery_option', ['delivery','pickup'])->change();
        });

        Schema::table('vendor_validations', function (Blueprint $table) {
            $table->boolean('is_valid')->change();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['customer','vendor','supplier','admin','manufacturer'])->change();
            $table->enum('gender', ['male','female','other'])->nullable()->change();
        });
    }
};
