<?php
/** @var string $title */
/** @var string $active */
/** @var string $content */
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($title ?? 'Device Monitor', ENT_QUOTES, 'UTF-8') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="/assets/app.css" rel="stylesheet">
    <script src="https://unpkg.com/mqtt/dist/mqtt.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
</head>
<body data-page="<?= htmlspecialchars($active ?? 'dashboard', ENT_QUOTES, 'UTF-8') ?>">
<div class="app-shell">
    <aside class="sidebar">
        <div class="brand">
            <div class="brand-mark">DM</div>
            <div>
                <div class="brand-title">Device Monitor</div>
                <div class="brand-subtitle">HiveMQ telemetry</div>
            </div>
        </div>
        <nav class="nav flex-column gap-2">
            <a class="nav-link <?= ($active ?? '') === 'dashboard' ? 'active' : '' ?>" href="/">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
            <a class="nav-link <?= ($active ?? '') === 'config' ? 'active' : '' ?>" href="/config">
                <i class="bi bi-gear"></i> Cấu hình
            </a>
            <a class="nav-link <?= ($active ?? '') === 'telemetry' ? 'active' : '' ?>" href="/telemetry">
                <i class="bi bi-broadcast"></i> Telemetry Live
            </a>
        </nav>
        <div class="sidebar-note">
            API: <code>/api/telemetry</code>
        </div>
    </aside>
    <main class="main-content">
        <?= $content ?>
    </main>
</div>
<script src="/assets/app.js"></script>
</body>
</html>
