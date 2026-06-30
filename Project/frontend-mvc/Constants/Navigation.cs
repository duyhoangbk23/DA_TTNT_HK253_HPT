namespace SmartWater.Admin.Constants;

public sealed record NavigationItem(string Label, string Controller, string Icon, string Section);

public static class Navigation
{
    public static readonly IReadOnlyList<NavigationItem> Items =
    [
        new("Dashboard", "Dashboard", "bi-grid-1x2-fill", "Overview"),
        new("Khách hàng", "Customers", "bi-people", "Operation"),
        new("Máy lọc nước", "Purifiers", "bi-droplet", "Operation"),
        new("Thiết bị ESP32", "Devices", "bi-cpu", "IoT"),
        new("Bảo trì", "Maintenance", "bi-tools", "Service"),
        new("Lịch hẹn", "Appointments", "bi-calendar-week", "Service"),
        new("Kỹ thuật viên", "Technicians", "bi-person-gear", "Service"),
        new("Telemetry", "Telemetry", "bi-activity", "IoT"),
        new("Cảnh báo", "Alerts", "bi-exclamation-triangle", "IoT"),
        new("Firmware OTA", "Firmware", "bi-cloud-arrow-up", "IoT"),
        new("Kho linh kiện", "Inventory", "bi-box-seam", "Business"),
        new("Bảo hành", "Warranty", "bi-shield-check", "Business"),
        new("Hóa đơn", "Invoices", "bi-receipt", "Business"),
        new("Báo cáo", "Reports", "bi-bar-chart", "Insight"),
        new("Nhật ký hệ thống", "Audit", "bi-journal-text", "System"),
        new("Người dùng", "Users", "bi-person-badge", "System"),
        new("Phân quyền", "Roles", "bi-lock", "System"),
        new("Cấu hình", "Settings", "bi-sliders", "System")
    ];
}
