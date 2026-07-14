/* =====================================================================
   SmartWater Admin - Core UI script
   Sidebar collapse/toggle, DataTables init, ApexChart helpers.
   ===================================================================== */
(function () {
    'use strict';

    /* ---------------------------------------------- Sidebar toggle */
    const body = document.body;
    const desktopQuery = window.matchMedia('(min-width: 992px)');

    document.querySelectorAll('[data-toggle-sidebar]').forEach((btn) => {
        btn.addEventListener('click', () => {
            if (desktopQuery.matches) {
                body.classList.toggle('sidebar-collapsed');
                localStorage.setItem('sw-sidebar', body.classList.contains('sidebar-collapsed') ? '1' : '0');
            } else {
                body.classList.toggle('sidebar-open');
            }
        });
    });

    // Khôi phục trạng thái sidebar trên desktop
    if (desktopQuery.matches && localStorage.getItem('sw-sidebar') === '1') {
        body.classList.add('sidebar-collapsed');
    }

    // Đóng sidebar khi bấm nền mờ (mobile)
    document.querySelector('.sidebar-backdrop')?.addEventListener('click', () => {
        body.classList.remove('sidebar-open');
    });

    /* ---------------------------------------------- DataTables init */
    if (window.jQuery && jQuery.fn.DataTable) {
        const viLang = {
            search: '',
            searchPlaceholder: 'Tìm kiếm...',
            lengthMenu: 'Hiển thị _MENU_',
            info: 'Hiển thị _START_–_END_ / _TOTAL_ mục',
            infoEmpty: 'Không có dữ liệu',
            zeroRecords: 'Không tìm thấy kết quả phù hợp',
            paginate: { previous: '‹', next: '›' },
        };

        jQuery('[data-datatable]').each(function () {
            const $t = jQuery(this);
            const dt = $t.DataTable({
                language: viLang,
                pageLength: parseInt($t.data('page-length') || 10, 10),
                lengthChange: false,
                // Ẩn ô tìm kiếm mặc định (dùng toolbar tuỳ biến), giữ info + phân trang
                dom: $t.data('dom') || '<"px-3"t><"d-flex flex-wrap justify-content-between align-items-center px-3 py-2"ip>',
                order: [],
                columnDefs: [{ orderable: false, targets: $t.data('no-sort') ? String($t.data('no-sort')).split(',').map(Number) : [] }],
            });

            // Ô tìm kiếm tuỳ biến ngoài bảng: [data-dt-search="#tableId"]
            jQuery('[data-dt-search="#' + $t.attr('id') + '"]').on('keyup change input', function () {
                dt.search(this.value).draw();
            });

            // Bộ lọc theo cột: <select data-dt-filter="#tableId" data-dt-column="4">
            jQuery('[data-dt-filter="#' + $t.attr('id') + '"]').on('change', function () {
                const col = parseInt(this.dataset.dtColumn, 10);
                const val = this.value ? '^' + this.value + '$' : '';
                dt.column(col).search(val, true, false).draw();
            });
        });
    }

    /* ---------------------------------------------- ApexCharts helpers */
    window.SW = window.SW || {};
    const BLUE = '#1668e3';
    const CYAN = '#17b6d6';

    const baseOptions = {
        chart: { fontFamily: 'inherit', toolbar: { show: false }, zoom: { enabled: false } },
        dataLabels: { enabled: false },
        grid: { borderColor: '#eef1f7', strokeDashArray: 4, padding: { left: 8, right: 8 } },
        tooltip: { theme: 'light' },
        legend: { position: 'bottom', markers: { radius: 12 } },
    };

    SW.areaChart = function (el, name, labels, series, color) {
        if (!el) return;
        return new ApexCharts(el, {
            ...baseOptions,
            chart: { ...baseOptions.chart, type: 'area', height: el.dataset.height || 300 },
            series: [{ name, data: series }],
            colors: [color || BLUE],
            stroke: { curve: 'smooth', width: 3 },
            fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.35, opacityTo: 0.02, stops: [0, 90] } },
            xaxis: { categories: labels, axisBorder: { show: false }, axisTicks: { show: false } },
        }).render() && null;
    };

    SW.lineChart = function (el, name, labels, series, color) {
        if (!el) return;
        new ApexCharts(el, {
            ...baseOptions,
            chart: { ...baseOptions.chart, type: 'line', height: el.dataset.height || 300 },
            series: [{ name, data: series }],
            colors: [color || CYAN],
            stroke: { curve: 'smooth', width: 3 },
            xaxis: { categories: labels, axisBorder: { show: false }, axisTicks: { show: false } },
        }).render();
    };

    SW.barChart = function (el, name, labels, series, color) {
        if (!el) return;
        new ApexCharts(el, {
            ...baseOptions,
            chart: { ...baseOptions.chart, type: 'bar', height: el.dataset.height || 300 },
            series: [{ name, data: series }],
            colors: [color || BLUE],
            plotOptions: { bar: { borderRadius: 6, columnWidth: '48%' } },
            xaxis: { categories: labels, axisBorder: { show: false }, axisTicks: { show: false } },
        }).render();
    };

    SW.donutChart = function (el, labels, series) {
        if (!el) return;
        new ApexCharts(el, {
            ...baseOptions,
            chart: { ...baseOptions.chart, type: 'donut', height: el.dataset.height || 300 },
            series: series,
            labels: labels,
            colors: ['#16a34a', '#d98a1c', '#e0304a', '#1668e3'],
            stroke: { width: 0 },
            plotOptions: { pie: { donut: { size: '70%' } } },
            legend: { position: 'bottom' },
        }).render();
    };
})();
