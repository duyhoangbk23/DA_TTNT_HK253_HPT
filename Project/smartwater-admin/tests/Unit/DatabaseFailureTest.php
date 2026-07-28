<?php

namespace Tests\Unit;

use Illuminate\Database\QueryException;
use PDOException;
use Tests\TestCase;

class DatabaseFailureTest extends TestCase
{
    public function test_it_classifies_connection_failures_without_exposing_credentials(): void
    {
        $exception = new PDOException('SQLSTATE[HY000] [2002] Connection refused for user=admin password=secret');

        $this->assertTrue(\App\Support\DatabaseFailure::isUnavailable($exception));
        $this->assertStringNotContainsString('secret', \App\Support\DatabaseFailure::context($exception)['message']);
    }

    public function test_it_does_not_classify_sql_errors_as_database_unavailable(): void
    {
        $exception = new QueryException(
            'mysql',
            'select * from missing_table',
            [],
            new PDOException('SQLSTATE[42S02]: Base table or view not found: 1146')
        );

        $this->assertFalse(\App\Support\DatabaseFailure::isUnavailable($exception));
    }
}
