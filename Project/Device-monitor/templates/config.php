<section class="panel-card mb-4">
    <div class="panel-head">
        <h1 class="panel-title">Cấu hình HiveMQ</h1>
        <span class="muted">Lưu vào localStorage của trình duyệt</span>
    </div>
    <form id="configForm" class="row g-3">
        <div class="col-md-8">
            <label class="form-label" for="brokerUrl">Broker URL</label>
            <input class="form-control" id="brokerUrl" name="brokerUrl" placeholder="broker.hivemq.com">
        </div>
        <div class="col-md-4">
            <label class="form-label" for="port">Port WSS</label>
            <input class="form-control" id="port" name="port" type="number" value="8884">
        </div>
        <div class="col-md-6">
            <label class="form-label" for="username">Username</label>
            <input class="form-control" id="username" name="username">
        </div>
        <div class="col-md-6">
            <label class="form-label" for="password">Password</label>
            <input class="form-control" id="password" name="password" type="password">
        </div>
        <div class="col-12">
            <label class="form-label" for="subscriptions">Subscriptions</label>
            <input class="form-control" id="subscriptions" name="subscriptions" value="devices/telemetry" placeholder="devices/telemetry">
        </div>
        <div class="col-12">
            <button class="btn btn-primary" type="submit">Lưu cấu hình</button>
        </div>
    </form>
    <div id="saveMessage" class="alert alert-success mt-3 d-none">Đã lưu cấu hình.</div>
</section>

<section class="panel-card">
    <h2 class="panel-title">Payload mẫu</h2>
    <pre class="code-block mb-0">{
  "device_id": "esp32-01",
  "timestamp": "2026-07-13 10:00:00",
  "tds": 284,
  "alert": "normal"
}</pre>
</section>
