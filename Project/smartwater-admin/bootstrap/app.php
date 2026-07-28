<?php

use App\Support\DatabaseFailure;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );

        $exceptions->render(function (\PDOException|QueryException $exception, Request $request) {
            if (! DatabaseFailure::isUnavailable($exception)) {
                return null;
            }

            Log::critical('Database service unavailable.', array_merge(
                DatabaseFailure::context($exception),
                ['method' => $request->method(), 'path' => $request->path()]
            ));

            $payload = [
                'success' => false,
                'message' => 'Dịch vụ dữ liệu tạm thời không khả dụng.',
                'error_code' => 'DATABASE_UNAVAILABLE',
            ];

            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json($payload, 503);
            }

            return response()->view('errors.503', [], 503);
        });
    })->create();
