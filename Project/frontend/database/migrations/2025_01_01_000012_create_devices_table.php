<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('devices', function (Blueprint $table) {
            $table->id();
            $table->string('device_code', 50)->unique();
            $table->string('serial_number', 100)->unique();
            $table->foreignId('product_id')->constrained();
            $table->foreignId('customer_id')->nullable()->constrained();
            $table->foreignId('contract_id')->nullable()->constrained();
            $table->foreignId('batch_id')->nullable()->constrained();
            $table->date('import_date')->nullable();
            $table->date('install_date')->nullable();
            $table->string('firmware_version', 50)->nullable();
            $table->text('location')->nullable();
            $table->enum('status', ['active', 'maintenance', 'error', 'pending', 'inactive'])->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('devices');
    }
};
