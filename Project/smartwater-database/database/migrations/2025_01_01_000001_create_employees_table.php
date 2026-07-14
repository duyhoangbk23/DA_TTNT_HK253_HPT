<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('employee_code', 20)->unique();
            $table->string('full_name', 100);
            $table->string('position', 100);
            $table->string('phone', 20)->nullable();
            $table->string('email', 100)->unique();
            $table->text('address')->nullable();
            $table->string('avatar_path')->nullable();
            $table->date('hire_date')->nullable();
            $table->foreignId('role_id')->constrained();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
