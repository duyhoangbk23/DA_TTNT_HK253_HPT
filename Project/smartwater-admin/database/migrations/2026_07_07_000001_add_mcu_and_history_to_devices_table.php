<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('devices', function (Blueprint $table) {
            $table->foreignId('mcu_id')->nullable()->after('product_id')->constrained('mcus');
            $table->timestamp('replaced_at')->nullable()->after('status');
            $table->foreignId('replaced_by_device_id')->nullable()->after('replaced_at')->constrained('devices');
        });

        // Modify status enum to include 'replaced'
        Schema::table('devices', function (Blueprint $table) {
            $table->enum('status', ['active', 'maintenance', 'error', 'pending', 'inactive', 'replaced'])
                ->change();
        });
    }

    public function down(): void
    {
        Schema::table('devices', function (Blueprint $table) {
            $table->dropForeignKeyIfExists(['mcu_id']);
            $table->dropForeignKeyIfExists(['replaced_by_device_id']);
            $table->dropColumn(['mcu_id', 'replaced_at', 'replaced_by_device_id']);
        });

        Schema::table('devices', function (Blueprint $table) {
            $table->enum('status', ['active', 'maintenance', 'error', 'pending', 'inactive'])
                ->change();
        });
    }
};
