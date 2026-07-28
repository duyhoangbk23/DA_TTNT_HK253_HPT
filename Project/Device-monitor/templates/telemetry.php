<section class="panel-card mb-4">
    <div class="panel-head">
        <div>
            <h1 class="panel-title mb-1">Telemetry Live</h1>
            <div class="muted" id="connectionInfo">Chưa kết nối HiveMQ.</div>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-success" id="btnConnect">Kết nối</button>
            <button class="btn btn-outline-warning" id="btnDisconnect" disabled>Ngắt</button>
            <button class="btn btn-outline-danger" id="btnReload">Tải lại</button>
        </div>
    </div>
</section>

<div id="telemetryDataError" class="alert alert-warning d-none" role="alert"></div>

<section class="telemetry-live-grid">
    <aside class="panel-card mcu-panel">
        <div class="panel-head">
            <div>
                <h2 class="panel-title">MCU đã kết nối</h2>
                <div class="muted">Chọn MCU để xem telemetry.</div>
            </div>
        </div>
        <div id="mcuDataError" class="alert alert-warning d-none" role="alert"></div>
        <div class="mcu-list" id="mcuList" aria-live="polite"></div>
    </aside>

    <div class="panel-card telemetry-detail">
        <div class="panel-head">
            <div>
                <h2 class="panel-title" id="selectedMcuTitle">Chọn một MCU</h2>
                <div class="muted" id="selectedMcuMeta">Chưa có telemetry được chọn.</div>
            </div>
            <div class="btn-group" id="chartRangeControls" role="group" aria-label="Khoảng thời gian biểu đồ">
                <button type="button" class="btn btn-sm btn-outline-info active" data-chart-range="1h">1 giờ</button>
                <button type="button" class="btn btn-sm btn-outline-info" data-chart-range="6h">6 giờ</button>
                <button type="button" class="btn btn-sm btn-outline-info" data-chart-range="12h">12 giờ</button>
                <button type="button" class="btn btn-sm btn-outline-info" data-chart-range="1d">1 ngày</button>
                <button type="button" class="btn btn-sm btn-outline-info" data-chart-range="1w">1 tuần</button>
            </div>
        </div>
        <div id="chartDataError" class="alert alert-warning d-none" role="alert"></div>
        <div class="chart-wrap">
            <canvas id="tdsChart" aria-label="Biểu đồ TDS theo thời gian"></canvas>
            <div class="chart-empty muted" id="tdsChartEmpty">Chọn MCU để xem biểu đồ TDS.</div>
        </div>
        <div class="table-responsive">
        <table class="table telemetry-table align-middle" id="telemetryTable">
            <thead>
            <tr>
                <th>Thời gian</th>
                <th>Topic</th>
                <th>Device ID</th>
                <th>TDS</th>
                <th>Alert</th>
            </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
        <div class="d-flex justify-content-between align-items-center mt-3" id="telemetryPagination" hidden>
            <span class="muted" id="telemetryPageInfo"></span>
            <div class="btn-group">
                <button class="btn btn-sm btn-outline-light" id="btnTelemetryPrevious" type="button">Trước</button>
                <button class="btn btn-sm btn-outline-light" id="btnTelemetryNext" type="button">Sau</button>
            </div>
        </div>
    </div>
</section>
