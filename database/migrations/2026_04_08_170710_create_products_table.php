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
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->longText('description')->nullable();
            $table->string('short_description')->nullable();
            $table->enum('availability', ['available', 'rented', 'maintenance'])->default('available');
            $table->decimal('price_per_day', 10, 2)->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('order')->default(0);
            // SEO
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('meta_keywords')->nullable();
            // Google Merchant
            $table->string('gm_brand')->nullable();
            $table->string('gm_gtin')->nullable();
            $table->string('gm_mpn')->nullable();
            $table->string('gm_condition')->default('new');
            $table->string('gm_google_product_category')->nullable();
            $table->decimal('gm_price', 10, 2)->nullable();
            $table->string('gm_currency', 3)->default('BRL');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
