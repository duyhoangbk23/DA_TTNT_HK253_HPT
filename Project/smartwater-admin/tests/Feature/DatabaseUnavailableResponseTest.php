<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use PDOException;
use Tests\TestCase;

class DatabaseUnavailableResponseTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Route::get('/test/database-unavailable', function (): void {
            throw new PDOException('SQLSTATE[HY000] [2002] Connection refused');
        });

        Route::get('/api/test/database-unavailable', function (): void {
            throw new PDOException('SQLSTATE[HY000] [2002] Connection refused');
        });
    }

    public function test_web_requests_receive_a_safe_503_page(): void
    {
        $this->get('/test/database-unavailable')
            ->assertStatus(503)
            ->assertSee('Hệ thống tạm thời không khả dụng')
            ->assertDontSee('SQLSTATE');
    }

    public function test_api_requests_receive_the_database_unavailable_contract(): void
    {
        $this->getJson('/api/test/database-unavailable')
            ->assertStatus(503)
            ->assertExactJson([
                'success' => false,
                'message' => 'Dịch vụ dữ liệu tạm thời không khả dụng.',
                'error_code' => 'DATABASE_UNAVAILABLE',
            ]);
    }
}
