<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maintenance_work_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('device_id')->constrained();
            $table->foreignId('contract_id')->nullable()->constrained();
            $table->foreignId('employee_id')->nullable()->constrained();
            $table->enum('type', ['scheduled', 'alert']);
            $table->string('source_alert', 255)->nullable();
            $table->enum('priority', ['normal', 'high', 'critical'])->default('normal');
            $table->enum('status', ['new', 'assigned', 'in_progress', 'awaiting_parts', 'completed', 'cancelled'])->default('new');
            $table->date('scheduled_for')->nullable();
            $table->timestamp('triggered_at')->nullable();
            $table->json('telemetry_snapshot')->nullable();
            $table->string('open_key', 320)->nullable()->unique();
            $table->text('description')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index(['device_id', 'status']);
            $table->index(['contract_id', 'scheduled_for']);
            $table->index(['type', 'triggered_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenance_work_orders');
    }
};
