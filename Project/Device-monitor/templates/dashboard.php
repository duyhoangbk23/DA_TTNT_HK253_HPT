<?php /** @var array $summary */ ?>
<section class="hero-card mb-4">
    <div>
        <div class="eyebrow">HiveMQ + Slim Framework</div>
        <h1 class="page-title">Nhận payload MQTT, parse JSON, lưu telemetry vào database</h1>
        <p class="page-lead">Web này lấy dữ liệu từ HiveMQ, gọi API Slim để lưu từng message telemetry và hiển thị bảng dữ liệu đã chuẩn hóa.</p>
    </div>
    <div class="hero-actions">
        <a class="btn btn-light" href="/telemetry">Mở telemetry live</a>
        <a class="btn btn-outline-light" href="/config">Cấu hình broker</a>
    </div>
</section>

<div id="dashboardDataError" class="alert alert-warning d-none" role="alert"></div>

<section class="stats-grid mb-4">
    <div class="stat-card">
        <div class="stat-label">Tổng telemetry</div>
        <div class="stat-value" data-summary-total><?= (int)($summary['total'] ?? 0) ?></div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Topic khác nhau</div>
        <div class="stat-value" data-summary-topics><?= (int)($summary['topics'] ?? 0) ?></div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Thiết bị</div>
        <div class="stat-value" data-summary-devices><?= (int)($summary['devices'] ?? 0) ?></div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Bản ghi mới nhất</div>
        <div class="stat-value small" data-summary-latest><?= htmlspecialchars((string)($summary['latest_timestamp'] ?? '-'), ENT_QUOTES, 'UTF-8') ?></div>
    </div>
</section>

<section class="panel-card">
    <div class="panel-head">
        <h2 class="panel-title">Cảnh báo TDS</h2>
        <a class="text-link" href="/api/telemetry/summary" target="_blank" rel="noreferrer">Xem JSON</a>
    </div>
    <div class="status-tags" data-summary-alerts>
        <?php foreach (($summary['alert_breakdown'] ?? []) as $row): ?>
            <span class="status-pill">
                <?= htmlspecialchars((string)$row['label'], ENT_QUOTES, 'UTF-8') ?>:
                <?= (int)$row['total'] ?>
            </span>
        <?php endforeach; ?>
        <?php if (empty($summary['alert_breakdown'])): ?>
            <span class="status-pill muted">Chưa có dữ liệu</span>
        <?php endif; ?>
    </div>
</section>
