<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maintenance_records', function (Blueprint $table) {
            $table->id();
            $table->string('maintenance_code', 50)->unique();
            $table->foreignId('device_id')->constrained();
            $table->foreignId('employee_id')->constrained();
            $table->date('maintenance_date');
            $table->enum('maintenance_type', ['routine', 'repair', 'replace'])->default('routine');
            $table->text('description');
            $table->text('parts_used')->nullable();
            $table->unsignedBigInteger('cost')->nullable();
            $table->enum('status', ['completed', 'pending'])->default('pending');
            $table->timestamps();
            $table->index('maintenance_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenance_records');
    }
};
