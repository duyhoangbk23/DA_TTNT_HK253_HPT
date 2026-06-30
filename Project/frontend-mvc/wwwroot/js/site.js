(() => {
    const renderCharts = () => {
        if (!window.ApexCharts) {
            return;
        }

        document.querySelectorAll(".apex-chart").forEach((element) => {
            const values = JSON.parse(element.dataset.values ?? "[]");
            const labels = JSON.parse(element.dataset.labels ?? "[]");
            const type = element.dataset.type ?? "line";
            const chart = new ApexCharts(element, {
                chart: { type, height: 230, toolbar: { show: false } },
                colors: ["#0d6efd"],
                dataLabels: { enabled: false },
                grid: { borderColor: "#eef2f7" },
                stroke: { curve: "smooth", width: 3 },
                series: [{ name: element.dataset.chartId ?? "Series", data: values }],
                xaxis: { categories: labels },
                yaxis: { labels: { style: { colors: "#667085" } } }
            });
            chart.render();
        });
    };

    const enhanceTables = () => {
        document.querySelectorAll(".data-table").forEach((table) => {
            const wrapper = document.createElement("div");
            wrapper.className = "d-flex justify-content-between align-items-center gap-2 mb-3";
            wrapper.innerHTML = '<input class="form-control form-control-sm w-auto" placeholder="Search" /><span class="text-muted small">Mock pagination / sort UI</span>';
            table.parentElement?.insertBefore(wrapper, table);
            const input = wrapper.querySelector("input");
            input?.addEventListener("input", () => {
                const term = input.value.toLowerCase();
                table.querySelectorAll("tbody tr").forEach((row) => {
                    row.classList.toggle("d-none", !row.textContent?.toLowerCase().includes(term));
                });
            });
        });
    };

    document.addEventListener("DOMContentLoaded", () => {
        renderCharts();
        enhanceTables();
        if (location.pathname.toLowerCase().includes("telemetry")) {
            window.setInterval(() => document.querySelectorAll(".data-table tbody tr").forEach((row) => row.classList.toggle("table-active")), 5000);
        }
    });
})();
