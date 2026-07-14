(function () {
    const CONFIG_KEY = 'hivemqConfig';
    let client = null;
    let connected = false;

    function getConfig() {
        try {
            return JSON.parse(localStorage.getItem(CONFIG_KEY) || '{}');
        } catch {
            return {};
        }
    }

    function saveConfig(config) {
        localStorage.setItem(CONFIG_KEY, JSON.stringify(config));
    }

    function topicsFromConfig(config) {
        return String(config.subscriptions || 'devices/telemetry')
            .split(',')
            .map((topic) => topic.trim())
            .filter(Boolean);
    }

    function renderTelemetryRows(rows) {
        const tbody = document.querySelector('#telemetryTable tbody');
        if (!tbody) {
            return;
        }

        tbody.innerHTML = '';
        rows.forEach((row) => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>${escapeHtml(row.timestamp || '')}</td>
                <td>${escapeHtml(row.topic || '')}</td>
                <td>${escapeHtml(row.device_id || '-')}</td>
                <td>${formatNumber(row.tds)}</td>
                <td>${escapeHtml(row.alert || '-')}</td>
            `;
            tbody.appendChild(tr);
        });
    }

    function formatNumber(value) {
        if (value === null || value === undefined || value === '') {
            return '-';
        }

        const number = Number(value);
        return Number.isFinite(number) ? number.toFixed(2).replace(/\.00$/, '') : escapeHtml(String(value));
    }

    function escapeHtml(value) {
        return String(value)
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#39;');
    }

    async function readJsonResponse(response) {
        const text = await response.text();
        if (!text) {
            return {};
        }

        try {
            return JSON.parse(text);
        } catch {
            return { message: text };
        }
    }

    async function loadTelemetry() {
        const response = await fetch('/api/telemetry?limit=100');
        const json = await readJsonResponse(response);
        renderTelemetryRows(json.data || []);
    }

    async function loadSummary() {
        const total = document.querySelector('[data-summary-total]');
        if (!total) {
            return;
        }

        const response = await fetch('/api/telemetry/summary');
        const json = await readJsonResponse(response);
        const data = json.data || {};

        total.textContent = data.total ?? 0;
        document.querySelector('[data-summary-topics]').textContent = data.topics ?? 0;
        document.querySelector('[data-summary-devices]').textContent = data.devices ?? 0;
        document.querySelector('[data-summary-latest]').textContent = data.latest_timestamp || '-';

        const alertBox = document.querySelector('[data-summary-alerts]');
        if (alertBox) {
            alertBox.innerHTML = '';
            (data.alert_breakdown || []).forEach((row) => {
                const span = document.createElement('span');
                span.className = 'status-pill';
                span.textContent = `${row.label}: ${row.total}`;
                alertBox.appendChild(span);
            });
            if (!(data.alert_breakdown || []).length) {
                alertBox.innerHTML = '<span class="status-pill muted">Chưa có dữ liệu</span>';
            }
        }
    }

    function updateConnectionStatus(message) {
        const el = document.getElementById('connectionInfo');
        if (el) {
            el.textContent = message;
        }
    }

    function setButtonsState(isConnected) {
        const connectBtn = document.getElementById('btnConnect');
        const disconnectBtn = document.getElementById('btnDisconnect');

        if (connectBtn) {
            connectBtn.disabled = isConnected;
        }
        if (disconnectBtn) {
            disconnectBtn.disabled = !isConnected;
        }
    }

    async function sendTelemetry(topic, payload) {
        const response = await fetch('/api/telemetry', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
                body: JSON.stringify({
                    topic,
                    payload,
                    source: 'hivemq-web'
                })
        });
        const json = await readJsonResponse(response);
        if (!response.ok) {
            throw new Error(json.message || `HTTP ${response.status}`);
        }

        return json;
    }

    function connectToHiveMQ() {
        const config = getConfig();
        const topics = topicsFromConfig(config);

        if (!config.brokerUrl || !config.port) {
            updateConnectionStatus('Thiếu cấu hình broker.');
            return;
        }

        const broker = String(config.brokerUrl).replace(/^wss?:\/\//, '');
        const url = `wss://${broker}:${config.port}/mqtt`;

        client = mqtt.connect(url, {
            username: config.username || undefined,
            password: config.password || undefined,
            reconnectPeriod: 0
        });

        updateConnectionStatus(`Đang kết nối ${url} ...`);

        client.on('connect', () => {
            connected = true;
            setButtonsState(true);
            updateConnectionStatus(`Đã kết nối ${url}`);
            topics.forEach((topic) => client.subscribe(topic));
        });

        client.on('message', async (topic, payload) => {
            const text = payload.toString();
            let parsed = text;
            try {
                parsed = JSON.parse(text);
            } catch {
                parsed = text;
            }

            try {
                const result = await sendTelemetry(topic, parsed);
                if (result.data) {
                    const table = document.querySelector('#telemetryTable tbody');
                    if (table) {
                        const current = await fetch('/api/telemetry?limit=100');
                        const json = await readJsonResponse(current);
                        renderTelemetryRows(json.data || []);
                    }
                }
            } catch (error) {
                updateConnectionStatus(`Lỗi lưu telemetry: ${error.message}`);
            }
        });

        client.on('error', (error) => {
            updateConnectionStatus(`Lỗi kết nối: ${error.message}`);
        });

        client.on('close', () => {
            connected = false;
            setButtonsState(false);
            updateConnectionStatus('Đã ngắt kết nối.');
        });
    }

    function disconnectHiveMQ() {
        if (client && connected) {
            client.end(true);
        }
    }

    function initConfigPage() {
        const form = document.getElementById('configForm');
        if (!form) {
            return;
        }

        const config = getConfig();
        ['brokerUrl', 'port', 'username', 'password', 'subscriptions'].forEach((key) => {
            const input = document.getElementById(key);
            if (input) {
                input.value = config[key] || (key === 'subscriptions' ? 'devices/telemetry' : '');
            }
        });

        form.addEventListener('submit', (event) => {
            event.preventDefault();
            saveConfig({
                brokerUrl: document.getElementById('brokerUrl').value.trim(),
                port: document.getElementById('port').value.trim(),
                username: document.getElementById('username').value.trim(),
                password: document.getElementById('password').value.trim(),
                subscriptions: document.getElementById('subscriptions').value.trim()
            });

            const message = document.getElementById('saveMessage');
            if (message) {
                message.classList.remove('d-none');
                setTimeout(() => message.classList.add('d-none'), 1800);
            }
        });
    }

    function initTelemetryPage() {
        if (!document.getElementById('telemetryTable')) {
            return;
        }

        loadTelemetry().catch(() => {
            updateConnectionStatus('Không tải được dữ liệu telemetry.');
        });

        const connectBtn = document.getElementById('btnConnect');
        const disconnectBtn = document.getElementById('btnDisconnect');
        const reloadBtn = document.getElementById('btnReload');

        if (connectBtn) {
            connectBtn.addEventListener('click', connectToHiveMQ);
        }
        if (disconnectBtn) {
            disconnectBtn.addEventListener('click', disconnectHiveMQ);
        }
        if (reloadBtn) {
            reloadBtn.addEventListener('click', () => loadTelemetry());
        }

        const config = getConfig();
        updateConnectionStatus(
            config.brokerUrl
                ? `Broker: ${config.brokerUrl}:${config.port || ''} | Topics: ${topicsFromConfig(config).join(', ') || '-'}`
                : 'Chưa cấu hình HiveMQ.'
        );
    }

    function initDashboardPage() {
        if (!document.querySelector('[data-summary-total]')) {
            return;
        }

        loadSummary().catch(() => {
            // noop
        });
        loadTelemetry().catch(() => {
            // noop
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        initConfigPage();
        initTelemetryPage();
        initDashboardPage();
    });
})();
