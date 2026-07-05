<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contracts', function (Blueprint $table) {
            $table->id();
            $table->string('contract_code', 50)->unique();
            $table->foreignId('customer_id')->constrained();
            $table->enum('contract_type', ['install', 'maintenance', 'replace'])->default('install');
            $table->date('start_date');
            $table->date('install_date')->nullable();
            $table->date('end_date');
            $table->unsignedSmallInteger('maintenance_cycle_months')->nullable();
            $table->unsignedBigInteger('amount')->nullable();
            $table->enum('status', ['active', 'expired', 'cancelled'])->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contracts');
    }
};
