<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $migratingLegacyMcu = Schema::hasTable('mcus') && !Schema::hasColumn('mcus', 'mcu_id');

        if ($migratingLegacyMcu) {
            Schema::table('mcus', fn (Blueprint $table) => $table->string('mcu_id', 50)->nullable()->after('id'));
            DB::table('mcus')->orderBy('id')->each(function (object $mcu): void {
                DB::table('mcus')->where('id', $mcu->id)->update(['mcu_id' => $mcu->mcu_code]);
            });
            Schema::table('mcus', function (Blueprint $table) {
                $table->unique('mcu_id');
                $table->dropColumn('mcu_code');
            });
        }

        if (Schema::hasTable('mcus')) {
            Schema::table('mcus', function (Blueprint $table) {
                if (!Schema::hasColumn('mcus', 'registration_status')) {
                    $table->enum('registration_status', ['REGISTERED', 'UNREGISTERED'])->default('REGISTERED');
                }
                if (!Schema::hasColumn('mcus', 'connection_status')) {
                    $table->enum('connection_status', ['CONNECTED', 'DISCONNECTED'])->default('DISCONNECTED');
                }
                if (!Schema::hasColumn('mcus', 'first_seen_at')) {
                    $table->timestamp('first_seen_at')->nullable();
                }
                if (!Schema::hasColumn('mcus', 'last_seen_at')) {
                    $table->timestamp('last_seen_at')->nullable();
                }
            });
        }

        if ($migratingLegacyMcu && Schema::hasTable('devices') && Schema::hasColumn('devices', 'mcu_id')) {
            Schema::table('devices', fn (Blueprint $table) => $table->string('mcu_id_string', 50)->nullable()->after('mcu_id'));
            DB::statement('UPDATE devices INNER JOIN mcus ON devices.mcu_id = mcus.id SET devices.mcu_id_string = mcus.mcu_id');
            Schema::table('devices', function (Blueprint $table) {
                $table->dropForeign(['mcu_id']);
                $table->dropColumn('mcu_id');
                $table->renameColumn('mcu_id_string', 'mcu_id');
                $table->foreign('mcu_id')->references('mcu_id')->on('mcus')->nullOnDelete();
            });
        }

        if (Schema::hasTable('telemetry') && Schema::hasColumn('telemetry', 'device_id')) {
            Schema::table('telemetry', fn (Blueprint $table) => $table->string('mcu_id', 50)->nullable()->after('topic'));
            DB::table('telemetry')->orderBy('id')->each(function (object $row): void {
                DB::table('telemetry')->where('id', $row->id)->update(['mcu_id' => $row->device_id]);
            });
            Schema::table('telemetry', function (Blueprint $table) {
                $table->index(['mcu_id', 'timestamp']);
                $table->dropColumn('device_id');
            });
        }

        if (!Schema::hasTable('device_logs')) {
            Schema::create('device_logs', function (Blueprint $table) {
                $table->id();
                $table->string('mcu_id', 50)->index();
                $table->string('log_type', 50);
                $table->string('sensor_name', 100)->nullable();
                $table->text('log_message');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('device_status')) {
            Schema::create('device_status', function (Blueprint $table) {
                $table->id();
                $table->string('mcu_id', 50)->unique();
                $table->string('connection_status', 20);
                $table->timestamp('last_seen_at')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('device_status');
        Schema::dropIfExists('device_logs');
    }
};
