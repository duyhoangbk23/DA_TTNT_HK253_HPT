<?php

namespace Tests\Unit;

use App\Services\DashboardService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class DashboardKpiDatabaseTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'database.default' => 'sqlite',
            'database.connections.sqlite.database' => ':memory:',
        ]);
        DB::purge('sqlite');

        Schema::connection('sqlite')->create('customers', function (Blueprint $table): void {
            $table->increments('id');
            $table->softDeletes();
        });
        Schema::connection('sqlite')->create('products', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('product_name');
        });
        Schema::connection('sqlite')->create('devices', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('status');
        });
        Schema::connection('sqlite')->create('contracts', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('status');
        });
    }

    public function test_kpis_only_expose_database_backed_metrics(): void
    {
        DB::table('customers')->insert([['deleted_at' => null], ['deleted_at' => null]]);
        DB::table('products')->insert([
            ['product_name' => 'Sản phẩm 1'],
            ['product_name' => 'Sản phẩm 2'],
            ['product_name' => 'Sản phẩm 3'],
        ]);
        DB::table('devices')->insert([
            ['status' => 'active'],
            ['status' => 'active'],
            ['status' => 'maintenance'],
            ['status' => 'error'],
        ]);
        DB::table('contracts')->insert([
            ['status' => 'active'],
            ['status' => 'expired'],
        ]);

        $kpis = app(DashboardService::class)->getKpis();

        $this->assertSame([2, 3, 4, 2, 1, 1], array_column($kpis, 'value'));
        foreach ($kpis as $kpi) {
            $this->assertArrayNotHasKey('trend', $kpi);
            $this->assertArrayNotHasKey('up', $kpi);
        }
    }
}
