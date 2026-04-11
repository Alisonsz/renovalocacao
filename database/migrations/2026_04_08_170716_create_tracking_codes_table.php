<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tracking_codes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->longText('code');
            $table->enum('position', ['head', 'body_start', 'body_end'])->default('head');
            $table->boolean('is_active')->default(true);
            $table->json('pages')->nullable(); // null = all pages, or array: ['home','product',...]
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tracking_codes');
    }
};
