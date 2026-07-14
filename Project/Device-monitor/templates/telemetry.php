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

<section class="panel-card">
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
</section>
