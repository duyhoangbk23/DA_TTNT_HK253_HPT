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
            $table->string('product_code', 50)->unique();
            $table->string('product_name', 150);
            $table->foreignId('category_id')->constrained();
            $table->string('model', 100);
            $table->string('capacity', 50)->nullable();
            $table->string('unit', 20)->default('Chiếc');
            $table->unsignedBigInteger('price')->nullable();
            $table->string('image_path')->nullable();
            $table->enum('status', ['active', 'maintenance', 'inactive'])->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
