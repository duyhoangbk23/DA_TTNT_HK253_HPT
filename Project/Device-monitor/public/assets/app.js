(function () {
    const CONFIG_KEY = 'hivemqConfig';
    const TELEMETRY_PAGE_SIZE = 25;
    let client = null;
    let connected = false;
    let selectedMcuId = null;
    let currentTelemetryPage = 1;
    let selectedChartRange = '1h';
    let mcuRows = [];
    let tdsChart = null;

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
                <td>${escapeHtml(row.mcu_id || '-')}</td>
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

    function renderTelemetryPagination(meta) {
        const controls = document.getElementById('telemetryPagination');
        const info = document.getElementById('telemetryPageInfo');
        const previous = document.getElementById('btnTelemetryPrevious');
        const next = document.getElementById('btnTelemetryNext');
        if (!controls || !meta) {
            if (controls) {
                controls.hidden = true;
            }
            return;
        }

        controls.hidden = meta.last_page <= 1;
        if (info) {
            info.textContent = `Trang ${meta.page}/${meta.last_page} · ${meta.total} bản ghi`;
        }
        if (previous) {
            previous.disabled = meta.page <= 1;
        }
        if (next) {
            next.disabled = meta.page >= meta.last_page;
        }
    }

    function handleApiError(response, json, targetElement) {
        const unavailable = response.status === 503 && json.error_code === 'DATABASE_UNAVAILABLE';
        const message = unavailable
            ? 'Dịch vụ dữ liệu tạm thời không khả dụng. Vui lòng thử lại sau.'
            : 'Không thể tải dữ liệu. Vui lòng thử lại.';

        if (targetElement) {
            targetElement.textContent = message;
            targetElement.classList.remove('d-none');
        }
    }

    function clearApiError(targetElement) {
        if (targetElement) {
            targetElement.classList.add('d-none');
            targetElement.textContent = '';
        }
    }

    async function loadTelemetry(mcuId = null, page = currentTelemetryPage) {
        const params = new URLSearchParams({ per_page: String(TELEMETRY_PAGE_SIZE), page: String(page) });
        if (mcuId) {
            params.set('mcu_id', mcuId);
        }

        const response = await fetch(`/api/telemetry?${params.toString()}`);
        const json = await readJsonResponse(response);
        if (!response.ok) {
            handleApiError(response, json, document.getElementById('telemetryDataError'));
            renderTelemetryRows([]);
            renderTelemetryPagination(null);
            return [];
        }

        clearApiError(document.getElementById('telemetryDataError'));
        const rows = json.data || [];
        currentTelemetryPage = json.meta?.page || page;
        renderTelemetryRows(rows);
        renderTelemetryPagination(json.meta);
        return rows;
    }

    function renderMcuList(rows) {
        const list = document.getElementById('mcuList');
        if (!list) {
            return;
        }

        list.innerHTML = '';
        if (!rows.length) {
            const empty = document.createElement('div');
            empty.className = 'mcu-list-empty muted';
            empty.textContent = 'Chưa có MCU gửi telemetry.';
            list.appendChild(empty);
            return;
        }

        rows.forEach((row) => {
            const item = document.createElement('button');
            item.type = 'button';
            item.className = 'mcu-item';
            if (row.mcu_id === selectedMcuId) {
                item.classList.add('is-selected');
            }

            const id = document.createElement('span');
            id.className = 'mcu-item-id';
            id.textContent = row.mcu_id;
            const meta = document.createElement('span');
            meta.className = 'mcu-item-meta';
            meta.textContent = `${row.telemetry_count} bản ghi • ${row.latest_timestamp || '-'}`;
            item.append(id, meta);
            item.addEventListener('click', () => selectMcu(row.mcu_id));
            list.appendChild(item);
        });
    }

    function updateSelectedMcuDetail() {
        const title = document.getElementById('selectedMcuTitle');
        const meta = document.getElementById('selectedMcuMeta');
        const selected = mcuRows.find((row) => row.mcu_id === selectedMcuId);

        if (!selected) {
            if (title) {
                title.textContent = 'Chọn một MCU';
            }
            if (meta) {
                meta.textContent = 'Chưa có telemetry được chọn.';
            }
            return;
        }

        if (title) {
            title.textContent = `Telemetry: ${selected.mcu_id}`;
        }
        if (meta) {
            meta.textContent = `${selected.telemetry_count} bản ghi • Cập nhật gần nhất: ${selected.latest_timestamp || '-'}`;
        }
    }

    function renderTdsChart(points) {
        const canvas = document.getElementById('tdsChart');
        const empty = document.getElementById('tdsChartEmpty');
        if (!canvas) {
            return;
        }

        if (!points.length) {
            if (tdsChart) {
                tdsChart.destroy();
                tdsChart = null;
            }
            canvas.hidden = true;
            if (empty) {
                empty.hidden = false;
                empty.textContent = 'MCU này chưa có giá trị TDS để vẽ biểu đồ.';
            }
            return;
        }

        canvas.hidden = false;
        if (empty) {
            empty.hidden = true;
        }

        const data = points.map((point) => Number(point.tds));
        const labels = points.map((point) => point.timestamp);
        if (tdsChart) {
            tdsChart.data.labels = labels;
            tdsChart.data.datasets[0].data = data;
            tdsChart.update();
            return;
        }

        tdsChart = new Chart(canvas, {
            type: 'line',
            data: {
                labels,
                datasets: [{
                    label: 'TDS',
                    data,
                    borderColor: '#38bdf8',
                    backgroundColor: 'rgba(56, 189, 248, 0.16)',
                    fill: true,
                    tension: 0.28,
                    pointRadius: 3,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { labels: { color: '#e5eefb' } },
                },
                scales: {
                    x: { ticks: { color: '#cbd5e1', maxRotation: 0 }, grid: { color: 'rgba(255, 255, 255, 0.08)' } },
                    y: { ticks: { color: '#cbd5e1' }, grid: { color: 'rgba(255, 255, 255, 0.08)' } },
                },
            },
        });
    }

    async function loadTdsChart(mcuId) {
        const params = new URLSearchParams({ mcu_id: mcuId, range: selectedChartRange });
        const response = await fetch(`/api/telemetry/chart?${params.toString()}`);
        const json = await readJsonResponse(response);
        if (!response.ok) {
            handleApiError(response, json, document.getElementById('chartDataError'));
            renderTdsChart([]);
            return;
        }

        clearApiError(document.getElementById('chartDataError'));
        renderTdsChart(json.data || []);
    }

    function setChartRange(range) {
        selectedChartRange = range;
        document.querySelectorAll('[data-chart-range]').forEach((button) => {
            button.classList.toggle('active', button.dataset.chartRange === range);
        });

        if (selectedMcuId) {
            loadTdsChart(selectedMcuId).catch(() => {
                updateConnectionStatus('Không tải được biểu đồ telemetry.');
            });
        }
    }

    async function loadMcus() {
        const response = await fetch('/api/mcus');
        const json = await readJsonResponse(response);
        if (!response.ok) {
            handleApiError(response, json, document.getElementById('mcuDataError'));
            return [];
        }

        clearApiError(document.getElementById('mcuDataError'));
        mcuRows = json.data || [];
        return mcuRows;
    }

    async function selectMcu(mcuId) {
        selectedMcuId = mcuId;
        currentTelemetryPage = 1;
        renderMcuList(mcuRows);
        updateSelectedMcuDetail();
        await Promise.all([loadTelemetry(mcuId), loadTdsChart(mcuId)]);
    }

    function changeTelemetryPage(page) {
        if (!selectedMcuId) {
            return;
        }

        currentTelemetryPage = Math.max(1, page);
        loadTelemetry(selectedMcuId, currentTelemetryPage).catch(() => {
            updateConnectionStatus('Không tải được dữ liệu telemetry.');
        });
    }

    async function refreshMcuView() {
        const rows = await loadMcus();
        if (!rows.length) {
            selectedMcuId = null;
            renderMcuList(rows);
            updateSelectedMcuDetail();
            renderTelemetryRows([]);
            renderTdsChart([]);
            return;
        }

        const isSelected = rows.some((row) => row.mcu_id === selectedMcuId);
        await selectMcu(isSelected ? selectedMcuId : rows[0].mcu_id);
    }

    async function loadSummary() {
        const total = document.querySelector('[data-summary-total]');
        if (!total) {
            return;
        }

        const response = await fetch('/api/telemetry/summary');
        const json = await readJsonResponse(response);
        if (!response.ok) {
            handleApiError(response, json, document.getElementById('dashboardDataError'));
            return;
        }

        clearApiError(document.getElementById('dashboardDataError'));
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
            handleApiError(response, json, document.getElementById('connectionInfo'));
            return null;
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
                if (result?.data) {
                    await refreshMcuView();
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

        refreshMcuView().catch(() => {
            updateConnectionStatus('Không tải được dữ liệu telemetry.');
        });

        const connectBtn = document.getElementById('btnConnect');
        const disconnectBtn = document.getElementById('btnDisconnect');
        const reloadBtn = document.getElementById('btnReload');
        const telemetryPrevious = document.getElementById('btnTelemetryPrevious');
        const telemetryNext = document.getElementById('btnTelemetryNext');
        const chartRangeControls = document.getElementById('chartRangeControls');

        if (connectBtn) {
            connectBtn.addEventListener('click', connectToHiveMQ);
        }
        if (disconnectBtn) {
            disconnectBtn.addEventListener('click', disconnectHiveMQ);
        }
        if (reloadBtn) {
            reloadBtn.addEventListener('click', () => refreshMcuView().catch(() => {
                updateConnectionStatus('Không tải được dữ liệu telemetry.');
            }));
        }
        if (telemetryPrevious) {
            telemetryPrevious.addEventListener('click', () => changeTelemetryPage(currentTelemetryPage - 1));
        }
        if (telemetryNext) {
            telemetryNext.addEventListener('click', () => changeTelemetryPage(currentTelemetryPage + 1));
        }
        if (chartRangeControls) {
            chartRangeControls.addEventListener('click', (event) => {
                const button = event.target.closest('[data-chart-range]');
                if (button) {
                    setChartRange(button.dataset.chartRange);
                }
            });
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
    }

    document.addEventListener('DOMContentLoaded', () => {
        initConfigPage();
        initTelemetryPage();
        initDashboardPage();
    });
})();
