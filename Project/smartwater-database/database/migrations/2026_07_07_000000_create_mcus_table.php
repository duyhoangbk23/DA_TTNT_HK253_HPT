<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mcus', function (Blueprint $table) {
            $table->id();
            $table->string('mcu_code', 50)->unique();
            $table->string('serial_number', 100)->unique();
            $table->string('firmware_version', 50)->nullable();
            $table->string('api_key', 64)->unique();
            $table->enum('status', ['online', 'offline', 'error'])->default('offline');
            $table->timestamp('last_connected_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mcus');
    }
};
