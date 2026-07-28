<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('telemetry', function (Blueprint $table) {
            $table->id();
            $table->timestamp('timestamp')->index();
            $table->string('topic', 255)->default('devices/telemetry');
            $table->string('mcu_id', 50)->index();
            $table->decimal('tds', 8, 2)->nullable();
            $table->string('alert', 255)->nullable();
            $table->timestamps();

            $table->index(['mcu_id', 'timestamp']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('telemetry');
    }
};
