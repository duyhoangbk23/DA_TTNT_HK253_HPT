<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('device_dashboard_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('device_id')->constrained();
            $table->timestamp('recorded_at');
            $table->decimal('tds', 8, 2)->nullable();
            $table->decimal('temperature', 8, 2)->nullable();
            $table->decimal('water_flow', 8, 2)->nullable();
            $table->decimal('ph', 8, 2)->nullable();
            $table->enum('status', ['good', 'warning', 'bad'])->nullable();
            $table->index(['device_id', 'recorded_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('device_dashboard_data');
    }
};
