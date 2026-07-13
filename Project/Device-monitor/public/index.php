<?php

declare(strict_types=1);

use App\Database;
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

$errorMiddleware = $app->addErrorMiddleware(
    (bool) filter_var($_ENV['APP_DEBUG'] ?? 'true', FILTER_VALIDATE_BOOL),
    true,
    true
);

$pdo = (new Database())->pdo();
$repository = new TelemetryRepository($pdo);
$service = new TelemetryService();

$app->get('/', function (ServerRequestInterface $request, ResponseInterface $response) use ($repository) {
    $summary = $repository->summary();
    $html = View::render('dashboard', [
        'title' => 'Device Monitor',
        'active' => 'dashboard',
        'summary' => $summary,
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

$app->get('/api/telemetry', function (ServerRequestInterface $request, ResponseInterface $response) use ($repository) {
    $query = $request->getQueryParams();
    $limit = max(1, min(500, (int)($query['limit'] ?? 100)));

    return Responder::json($response, [
        'data' => $repository->latest($limit),
    ]);
});

$app->get('/api/telemetry/summary', function (ServerRequestInterface $request, ResponseInterface $response) use ($repository) {
    return Responder::json($response, [
        'data' => $repository->summary(),
    ]);
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

    $normalized = $service->normalize($topic, $rawPayload, $timestamp, $source);
    $record = $repository->insert($normalized);

    return Responder::json($response, [
        'message' => 'Telemetry saved',
        'data' => $record,
    ], 201);
});

$app->run();
