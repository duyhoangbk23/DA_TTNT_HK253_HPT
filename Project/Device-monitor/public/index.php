<?php

declare(strict_types=1);

use App\Database;
use App\DatabaseFailure;
use App\Responder;
use App\TelemetryRepository;
use App\TelemetryService;
use App\View;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

if (PHP_SAPI === 'cli-server') {
    $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
    $candidate = realpath(__DIR__ . $path);
    if ($path !== '/' && $candidate !== false && str_starts_with($candidate, __DIR__) && is_file($candidate)) {
        return false;
    }
}

$app = AppFactory::create();
$app->addBodyParsingMiddleware();
$app->addRoutingMiddleware();

$logDatabaseFailure = static function (Throwable $exception, ServerRequestInterface $request): void {
    $context = array_merge(DatabaseFailure::context($exception), [
        'method' => $request->getMethod(),
        'path' => $request->getUri()->getPath(),
    ]);

    error_log('Device Monitor database failure: ' . json_encode($context, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
};

$errorMiddleware = $app->addErrorMiddleware(false, true, true);
$errorMiddleware->setDefaultErrorHandler(
    static function (
        ServerRequestInterface $request,
        Throwable $exception,
        bool $displayErrorDetails,
        bool $logErrors,
        bool $logErrorDetails
    ) use ($app, $logDatabaseFailure): ResponseInterface {
        if (DatabaseFailure::isUnavailable($exception)) {
            $logDatabaseFailure($exception, $request);

            return Responder::json($app->getResponseFactory()->createResponse(), [
                'success' => false,
                'message' => 'Dịch vụ dữ liệu tạm thời không khả dụng.',
                'error_code' => 'DATABASE_UNAVAILABLE',
            ], 503);
        }

        error_log('Device Monitor application error: ' . json_encode([
            'exception' => $exception::class,
            'path' => $request->getUri()->getPath(),
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

        return Responder::json($app->getResponseFactory()->createResponse(), [
            'success' => false,
            'message' => 'Đã xảy ra lỗi hệ thống. Vui lòng thử lại sau.',
        ], 500);
    }
);

$database = new Database();
$repository = static function () use ($database): TelemetryRepository {
    return new TelemetryRepository($database->pdo());
};
$service = new TelemetryService();

$app->get('/', function (ServerRequestInterface $request, ResponseInterface $response) use ($repository) {
    $html = View::render('dashboard', [
        'title' => 'Device Monitor',
        'active' => 'dashboard',
        'summary' => [],
    ]);

    return Responder::html($response, $html);
});

$app->get('/config', function (ServerRequestInterface $request, ResponseInterface $response) {
    $html = View::render('config', [
        'title' => 'Cấu hình HiveMQ',
        'active' => 'config',
    ]);

    return Responder::html($response, $html);
});

$app->get('/telemetry', function (ServerRequestInterface $request, ResponseInterface $response) {
    $html = View::render('telemetry', [
        'title' => 'Telemetry Live',
        'active' => 'telemetry',
    ]);

    return Responder::html($response, $html);
});

$app->get('/api/health', function (ServerRequestInterface $request, ResponseInterface $response) {
    return Responder::json($response, [
        'status' => 'ok',
        'time' => gmdate('c'),
    ]);
});

$app->get('/api/mcus', function (ServerRequestInterface $request, ResponseInterface $response) use ($repository) {
    return Responder::json($response, [
        'data' => $repository()->mcus(),
    ]);
});

$app->get('/api/telemetry', function (ServerRequestInterface $request, ResponseInterface $response) use ($repository) {
    $query = $request->getQueryParams();
    $page = max(1, (int) ($query['page'] ?? 1));
    $perPage = max(1, min(100, (int) ($query['per_page'] ?? $query['limit'] ?? 25)));
    $mcuId = trim((string)($query['mcu_id'] ?? ''));
    $result = $repository()->paginate($page, $perPage, $mcuId === '' ? null : $mcuId);

    return Responder::json($response, [
        'data' => $result['data'],
        'meta' => $result['meta'],
    ]);
});

$app->get('/api/telemetry/chart', function (ServerRequestInterface $request, ResponseInterface $response) use ($repository) {
    $query = $request->getQueryParams();
    $mcuId = trim((string)($query['mcu_id'] ?? ''));
    if ($mcuId === '') {
        return Responder::json($response, [
            'message' => 'Missing mcu_id',
        ], 422);
    }

    $limit = max(1, min(500, (int)($query['limit'] ?? 500)));
    $ranges = [
        '1h' => 1,
        '6h' => 6,
        '12h' => 12,
        '1d' => 24,
        '1w' => 168,
    ];
    $range = (string) ($query['range'] ?? '1h');
    if (!array_key_exists($range, $ranges)) {
        return Responder::json($response, [
            'message' => 'Invalid range',
        ], 422);
    }

    return Responder::json($response, [
        'data' => $repository()->tdsSeries($mcuId, $limit, $ranges[$range]),
        'range' => $range,
    ]);
});

$app->get('/api/telemetry/summary', function (ServerRequestInterface $request, ResponseInterface $response) use ($repository) {
    return Responder::json($response, [
        'data' => $repository()->summary(),
    ]);
});

$app->get('/health/database', function (ServerRequestInterface $request, ResponseInterface $response) use ($database, $logDatabaseFailure) {
    try {
        $database->pdo()->query('SELECT 1');

        return Responder::json($response, [
            'status' => 'healthy',
            'database' => 'connected',
        ]);
    } catch (Throwable $exception) {
        $logDatabaseFailure($exception, $request);

        return Responder::json($response, [
            'status' => 'unhealthy',
            'database' => 'disconnected',
        ], 503);
    }
});

$app->post('/api/telemetry', function (ServerRequestInterface $request, ResponseInterface $response) use ($repository, $service) {
    $payload = $request->getParsedBody();
    if (is_string($payload)) {
        $payload = json_decode($payload, true) ?: [];
    }
    if (!is_array($payload)) {
        $payload = [];
    }

    $topic = trim((string)($payload['topic'] ?? ''));
    $rawPayload = $payload['payload'] ?? null;
    $timestamp = trim((string)($payload['timestamp'] ?? ''));
    $source = trim((string)($payload['source'] ?? 'hivemq'));

    if ($topic === '') {
        return Responder::json($response, [
            'message' => 'Missing topic',
        ], 422);
    }

    try {
        $normalized = $service->normalize($topic, $rawPayload, $timestamp, $source);
        $record = $repository()->insert($normalized);
    } catch (InvalidArgumentException $e) {
        return Responder::json($response, [
            'message' => $e->getMessage(),
        ], 422);
    }

    return Responder::json($response, [
        'message' => 'Telemetry saved',
        'data' => $record,
    ], 201);
});

$app->run();
