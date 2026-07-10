<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('customer_code', 20)->unique();
            $table->string('customer_name', 150);
            $table->string('phone', 20);
            $table->string('email', 100)->unique()->nullable();
            $table->text('address')->nullable();
            $table->string('avatar_path')->nullable();
            $table->enum('type', ['individual', 'company'])->default('individual');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->date('joined_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
