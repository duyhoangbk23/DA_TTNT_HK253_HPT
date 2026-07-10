<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('batch_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('batch_id')->constrained();
            $table->foreignId('product_id')->constrained();
            $table->unsignedInteger('quantity')->default(0);
            $table->unsignedBigInteger('unit_cost')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('batch_details');
    }
};
