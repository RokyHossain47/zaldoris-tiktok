<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seller_id')->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->decimal('compare_price', 10, 2)->nullable();
            $table->integer('stock')->default(0);
            $table->integer('locked_stock')->default(0); // pre-session locked stock to prevent overselling
            $table->json('images')->nullable();
            $table->string('category')->default('fashion');
            $table->string('sourcing_country')->nullable(); // Mandatory sourcing declaration (SRS #4)
            $table->string('supplier_proof_url')->nullable();
            $table->string('dimensions')->nullable(); // Specific dimensions (SRS #12)
            $table->boolean('is_natural_lighting_declared')->default(true); // Natural lighting disclosure (SRS #12)
            $table->string('status')->default('active'); // active, draft, archived, suspended
            $table->timestamps();
        });

        Schema::create('live_stream_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stream_id')->constrained('streams')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->boolean('is_pinned')->default(false); // Showcase during stream
            $table->integer('session_stock_cap')->default(0);
            $table->integer('session_units_sold')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('live_stream_products');
        Schema::dropIfExists('products');
    }
};
