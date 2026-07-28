<?php

namespace Tests\Unit;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Tests\TestCase;

class DeviceTelemetryServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'database.default' => 'sqlite',
            'database.connections.sqlite.database' => ':memory:',
        ]);
        DB::purge('sqlite');

        Schema::connection('sqlite')->create('telemetry', function (Blueprint $table): void {
            $table->increments('id');
            $table->dateTime('timestamp');
            $table->string('topic');
            $table->string('mcu_id');
            $table->float('tds')->nullable();
            $table->string('alert')->nullable();
        });
    }

    public function test_it_builds_a_chart_from_real_telemetry_for_the_mapped_mcu(): void
    {
        DB::table('telemetry')->insert([
            ['timestamp' => '2026-07-27 10:05:00', 'topic' => 'devices/telemetry', 'mcu_id' => 'ESP32_001', 'tds' => 253, 'alert' => ''],
            ['timestamp' => '2026-07-27 10:00:00', 'topic' => 'devices/telemetry', 'mcu_id' => 'ESP32_001', 'tds' => 233, 'alert' => 'warning'],
            ['timestamp' => '2026-07-27 10:04:00', 'topic' => 'devices/telemetry', 'mcu_id' => 'OTHER_MCU', 'tds' => 999, 'alert' => 'critical'],
        ]);

        $telemetry = app(\App\Services\DeviceTelemetryService::class)->forMcu('ESP32_001');

        $this->assertSame(['10:00', '10:05'], $telemetry['labels']);
        $this->assertSame([233.0, 253.0], $telemetry['tds']);
        $this->assertSame('warning', $telemetry['alerts'][0]['alert']);
        $this->assertSame('normal', $telemetry['alerts'][1]['alert']);
    }

    public function test_it_returns_no_chart_data_when_a_device_has_no_mcu_mapping(): void
    {
        $telemetry = app(\App\Services\DeviceTelemetryService::class)->forMcu(null);

        $this->assertSame(['labels' => [], 'tds' => [], 'alerts' => []], $telemetry);
    }

    public function test_it_paginates_telemetry_logs_for_the_mapped_mcu(): void
    {
        DB::table('telemetry')->insert([
            ['timestamp' => '2026-07-27 10:05:00', 'topic' => 'devices/telemetry', 'mcu_id' => 'ESP32_001', 'tds' => 253, 'alert' => 'normal'],
            ['timestamp' => '2026-07-27 10:00:00', 'topic' => 'devices/telemetry', 'mcu_id' => 'ESP32_001', 'tds' => 233, 'alert' => 'warning'],
        ]);

        $page = app(\App\Services\DeviceTelemetryService::class)->paginatedLogsForMcu('ESP32_001', 2, 1);

        $this->assertSame(2, $page->total());
        $this->assertSame(2, $page->currentPage());
        $this->assertSame('2026-07-27 10:00:00', $page->first()->timestamp->format('Y-m-d H:i:s'));
    }
}
