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
            $table->string('mcu_id', 50)->unique();
            $table->string('serial_number', 100)->nullable()->unique();
            $table->string('firmware_version', 50)->nullable();
            $table->string('api_key', 64)->nullable()->unique();
            $table->enum('status', ['online', 'offline', 'error'])->default('offline');
            $table->timestamp('last_connected_at')->nullable();
            $table->enum('registration_status', ['REGISTERED', 'UNREGISTERED'])->default('REGISTERED');
            $table->enum('connection_status', ['CONNECTED', 'DISCONNECTED'])->default('DISCONNECTED');
            $table->timestamp('first_seen_at')->nullable();
            $table->timestamp('last_seen_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mcus');
    }
};
