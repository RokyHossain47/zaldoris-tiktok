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
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'colors')) {
                $table->json('colors')->nullable()->after('images');
            }
            if (!Schema::hasColumn('products', 'sizes')) {
                $table->json('sizes')->nullable()->after('colors');
            }
            if (!Schema::hasColumn('products', 'specifications')) {
                $table->json('specifications')->nullable()->after('sizes');
            }
            if (!Schema::hasColumn('products', 'brand')) {
                $table->string('brand')->nullable()->after('title');
            }
        });

        Schema::table('order_items', function (Blueprint $table) {
            if (!Schema::hasColumn('order_items', 'selected_color')) {
                $table->string('selected_color')->nullable()->after('product_title');
            }
            if (!Schema::hasColumn('order_items', 'selected_size')) {
                $table->string('selected_size')->nullable()->after('selected_color');
            }
            if (!Schema::hasColumn('order_items', 'product_image')) {
                $table->string('product_image')->nullable()->after('selected_size');
            }
            if (!Schema::hasColumn('order_items', 'options')) {
                $table->json('options')->nullable()->after('product_image');
            }
        });

        Schema::table('reviews', function (Blueprint $table) {
            if (!Schema::hasColumn('reviews', 'product_id')) {
                $table->foreignId('product_id')->nullable()->after('order_id')->constrained('products')->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            if (Schema::hasColumn('reviews', 'product_id')) {
                $table->dropForeign(['product_id']);
                $table->dropColumn('product_id');
            }
        });

        Schema::table('order_items', function (Blueprint $table) {
            if (Schema::hasColumn('order_items', 'options')) {
                $table->dropColumn(['selected_color', 'selected_size', 'product_image', 'options']);
            }
        });

        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'colors')) {
                $table->dropColumn(['colors', 'sizes', 'specifications', 'brand']);
            }
        });
    }
};
