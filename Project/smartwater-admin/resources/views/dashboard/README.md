# resources/views/dashboard/ — Dashboard page

Trang chính hiển thị KPI, biểu đồ, và dữ liệu tổng quan.

## 📌 index.blade.php

**Route:** `/` hoặc `/dashboard`
**Controller:** DashboardController@index

### Bao gồm

1. **KPI Cards (4):**
   - Tổng khách hàng
   - Tổng thiết bị
   - Hợp đồng hoạt động
   - Lô hàng này tháng

2. **ApexCharts (3):**
   - **Device Status Donut Chart** — Active/Maintenance/Error devices
   - **Customers Bar Chart** — Customers by region/category
   - **Maintenance Bar Chart** — Maintenance tasks

### Dữ liệu

Từ `MockData`:
- `currentUser` — User info (từ View Composer)
- `kpis` — 4 KPI values
- `deviceStatusChart` — Donut chart data
- `customersChart` — Bar chart data
- `maintenanceChart` — Bar chart data

### Cấu trúc Blade

```blade
@extends('layouts.app')

<div class="row g-3">
    <!-- 4 KPI Cards in grid -->
    @foreach ($kpis as $kpi)
        <div class="col-md-6 col-lg-3">
            <x-kpi-card ... />
        </div>
    @endforeach
</div>

<!-- Row 2: Charts -->
<div class="row g-3 mt-3">
    <div class="col-lg-4">
        <div id="chartDeviceStatus"></div>
    </div>
    <div class="col-lg-4">
        <div id="chartCustomers"></div>
    </div>
    <div class="col-lg-4">
        <div id="chartMaintenance"></div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        SW.donutChart(...);  // Device status
        SW.barChart(...);    // Customers
        SW.barChart(...);    // Maintenance
    });
</script>
```

---

## 🎨 Styling

- Grid layout responsive (4 cols desktop, 2 cols tablet, 1 col mobile)
- KPI cards giãn cách 3px (gap-3)
- Charts full width với custom height
- Blue & cyan color theme

---

## 📊 Chart Details

### Device Status (Donut)

```
Categories: Active, Maintenance, Error
Values: Number of devices in each status
Colors: Green (active), Yellow (maintenance), Red (error)
```

### Customers (Bar)

```
Categories: Ho Chi Minh, Hanoi, Da Nang, etc.
Values: Customer count per region
Color: Blue (#1668e3)
```

### Maintenance (Bar)

```
Categories: Preventive, Repair, Upgrade
Values: Tasks this month
Color: Blue
```

---

## 💡 Mở rộng dashboard

Để thêm KPI card mới:

```blade
<x-kpi-card 
    label="Metric name" 
    value="123"
    icon="bi-icon-name"
    color="primary"
    trend="+5%"
/>
```

Để thêm chart mới:

```blade
<div id="myChart"></div>
<script>
    SW.areaChart(
        document.getElementById('myChart'),
        'Series Name',
        ['Jan', 'Feb'],
        [100, 200],
        '#1668e3'
    );
</script>
```
