using SmartWater.Admin.Enums;
using SmartWater.Admin.ViewModels;

namespace SmartWater.Admin.Services;

public sealed class MockAdminDataService : IMockAdminDataService
{
    private readonly IReadOnlyList<CustomerRowViewModel> _customers;
    private readonly IReadOnlyList<DeviceRowViewModel> _devices;
    private readonly IReadOnlyList<PurifierRowViewModel> _purifiers;
    private readonly IReadOnlyList<TelemetryRowViewModel> _telemetry;
    private readonly IReadOnlyList<AlertRowViewModel> _alerts;
    private readonly IReadOnlyList<MaintenanceRowViewModel> _maintenance;
    private readonly IReadOnlyList<InventoryRowViewModel> _inventory;

    public MockAdminDataService()
    {
        _customers = BuildCustomers();
        _devices = BuildDevices();
        _purifiers = BuildPurifiers(_customers, _devices);
        _telemetry = BuildTelemetry(_devices);
        _alerts = BuildAlerts(_devices);
        _maintenance = BuildMaintenance(_customers, _purifiers);
        _inventory = BuildInventory();
    }

    public DashboardViewModel GetDashboard()
    {
        var online = _devices.Count(device => device.Online);
        var fault = _purifiers.Count(item => item.Status == EntityStatus.Fault);

        return new DashboardViewModel
        {
            Kpis =
            [
                new("Tổng khách hàng", _customers.Count.ToString("N0"), "+8.2%", "bi-people", "primary"),
                new("Máy đang hoạt động", _purifiers.Count(item => item.Status == EntityStatus.Active).ToString("N0"), "+5.4%", "bi-droplet", "success"),
                new("Máy đang lỗi", fault.ToString("N0"), "-1.1%", "bi-exclamation-octagon", "danger"),
                new("Thiết bị Online", online.ToString("N0"), "+3.7%", "bi-wifi", "info"),
                new("Thiết bị Offline", (_devices.Count - online).ToString("N0"), "-2.0%", "bi-wifi-off", "warning"),
                new("Lịch hôm nay", _maintenance.Count(item => item.Schedule.Date == DateTime.Today).ToString("N0"), "Today", "bi-calendar-check", "primary"),
                new("Lịch tuần này", _maintenance.Count.ToString("N0"), "7 ngày", "bi-calendar-week", "info"),
                new("Cảnh báo đang mở", _alerts.Count.ToString("N0"), "+4", "bi-bell", "danger"),
                new("Doanh thu tháng", "186.4M", "+12.6%", "bi-cash-coin", "success"),
                new("Yêu cầu hỗ trợ", "24", "-3", "bi-headset", "warning")
            ],
            Charts =
            [
                new("Doanh thu", "revenueChart", "area", [48, 52, 61, 58, 73, 82, 91], ["T2", "T3", "T4", "T5", "T6", "T7", "CN"]),
                new("Lịch bảo trì", "maintenanceChart", "bar", [12, 18, 15, 22, 19, 11, 8], ["T2", "T3", "T4", "T5", "T6", "T7", "CN"]),
                new("Thiết bị Online", "onlineChart", "line", [72, 78, 76, 81, 84, 83, 88], ["08:00", "10:00", "12:00", "14:00", "16:00", "18:00", "20:00"]),
                new("Lưu lượng nước", "flowChart", "area", [140, 156, 151, 170, 185, 178, 196], ["08:00", "10:00", "12:00", "14:00", "16:00", "18:00", "20:00"]),
                new("TDS", "tdsChart", "line", [155, 148, 151, 139, 133, 128, 124], ["08:00", "10:00", "12:00", "14:00", "16:00", "18:00", "20:00"]),
                new("pH", "phChart", "line", [7.1M, 7.2M, 7.0M, 7.3M, 7.4M, 7.2M, 7.1M], ["08:00", "10:00", "12:00", "14:00", "16:00", "18:00", "20:00"]),
                new("Nhiệt độ", "temperatureChart", "area", [28, 29, 30, 31, 30, 29, 28], ["08:00", "10:00", "12:00", "14:00", "16:00", "18:00", "20:00"]),
                new("Áp suất", "pressureChart", "line", [2.1M, 2.4M, 2.3M, 2.5M, 2.6M, 2.4M, 2.2M], ["08:00", "10:00", "12:00", "14:00", "16:00", "18:00", "20:00"])
            ],
            RecentAlerts = _alerts.Take(5).Select(item => new ActivityItemViewModel(item.Message, $"{item.DeviceId} - {item.Severity}", item.Time.ToString("HH:mm"), "bi-exclamation-triangle")).ToList(),
            RecentMaintenance = _maintenance.Take(5).ToList(),
            NewestCustomers = _customers.Take(5).ToList(),
            SystemHealth =
            [
                new("API Gateway", "Latency 42ms", "Healthy", "bi-check-circle"),
                new("SQL Server", "Connection pool 37%", "Healthy", "bi-database"),
                new("SignalR Hub", "1,284 connections", "Stable", "bi-broadcast"),
                new("MQTT Bridge", "98.2% delivery", "Healthy", "bi-router")
            ]
        };
    }

    public IReadOnlyList<CustomerRowViewModel> GetCustomers() => _customers;
    public IReadOnlyList<PurifierRowViewModel> GetPurifiers() => _purifiers;
    public IReadOnlyList<DeviceRowViewModel> GetDevices() => _devices;
    public DeviceDetailViewModel GetDeviceDetail(string id)
    {
        var device = _devices.FirstOrDefault(item => item.Id == id) ?? _devices[0];
        var telemetry = _telemetry.Where(item => item.DeviceId == device.Id).Take(8).ToList();

        return new DeviceDetailViewModel
        {
            Device = device,
            Metrics =
            [
                new("CPU", "38%", "+2%", "bi-cpu", "primary"),
                new("RAM", "62%", "-4%", "bi-memory", "info"),
                new("Signal", $"{device.WifiRssi} dBm", "RSSI", "bi-wifi", "success"),
                new("Battery", $"{device.Battery}%", "Mock", "bi-battery-half", "warning")
            ],
            RealtimeData = telemetry,
            Logs =
            [
                new("MQTT connected", "Broker accepted session", "2 phút trước", "bi-link-45deg"),
                new("OTA checked", "No firmware update required", "25 phút trước", "bi-cloud-check"),
                new("Telemetry pushed", "Latest sensor package received", "1 giờ trước", "bi-activity")
            ]
        };
    }

    public IReadOnlyList<TelemetryRowViewModel> GetTelemetry() => _telemetry;
    public IReadOnlyList<AlertRowViewModel> GetAlerts() => _alerts;
    public IReadOnlyList<MaintenanceRowViewModel> GetMaintenance() => _maintenance;
    public IReadOnlyList<InventoryRowViewModel> GetInventory() => _inventory;

    private static IReadOnlyList<CustomerRowViewModel> BuildCustomers() =>
        Enumerable.Range(1, 24).Select(index => new CustomerRowViewModel(
            $"CUS-{index:0000}",
            $"https://i.pravatar.cc/96?img={(index % 60) + 1}",
            CustomerNames[(index - 1) % CustomerNames.Length],
            $"09{index:00} {100 + index:000} {200 + index:000}",
            $"customer{index:00}@smartwater.vn",
            $"{12 + index} Nguyễn Văn Cừ, Quận {(index % 12) + 1}, TP.HCM",
            (index % 5) + 1,
            DateOnly.FromDateTime(DateTime.Today.AddDays(-index * 7)),
            index % 9 == 0 ? EntityStatus.Inactive : EntityStatus.Active)).ToList();

    private static IReadOnlyList<DeviceRowViewModel> BuildDevices() =>
        Enumerable.Range(1, 36).Select(index => new DeviceRowViewModel(
            $"ESP32-{index:00000}",
            $"v{1 + index % 3}.{index % 10}.{index % 7}",
            -38 - (index % 45),
            $"192.168.{index % 5}.{40 + index}",
            $"7C:DF:A1:{index:00}:4B:{90 + index:X2}",
            index % 6 == 0 ? "Disconnected" : "Connected",
            DateTime.Now.AddMinutes(-index * 4),
            index % 6 != 0,
            55 + (index % 42))).ToList();

    private static IReadOnlyList<PurifierRowViewModel> BuildPurifiers(IReadOnlyList<CustomerRowViewModel> customers, IReadOnlyList<DeviceRowViewModel> devices) =>
        Enumerable.Range(1, 32).Select(index => new PurifierRowViewModel(
            $"WPF-{index:00000}",
            Models[index % Models.Length],
            $"SN{DateTime.Today:yy}{index:000000}",
            devices[index % devices.Count].Firmware,
            devices[index % devices.Count].Id,
            customers[index % customers.Count].FullName,
            customers[index % customers.Count].Address,
            DateOnly.FromDateTime(DateTime.Today.AddDays(-index * 13)),
            DateOnly.FromDateTime(DateTime.Today.AddMonths(18).AddDays(index)),
            index % 13 == 0 ? EntityStatus.Fault : index % 7 == 0 ? EntityStatus.Maintenance : EntityStatus.Active)).ToList();

    private static IReadOnlyList<TelemetryRowViewModel> BuildTelemetry(IReadOnlyList<DeviceRowViewModel> devices) =>
        Enumerable.Range(1, 72).Select(index => new TelemetryRowViewModel(
            devices[index % devices.Count].Id,
            1.4M + index % 8,
            2.1M + (index % 5) / 10M,
            27 + index % 9,
            110 + index % 90,
            6.8M + (index % 8) / 10M,
            4.9M + (index % 3) / 10M,
            0.7M + (index % 4) / 10M,
            index % 4 == 0 ? "Off" : "On",
            index % 5 == 0 ? "Closed" : "Open",
            42 + index % 55,
            DateTime.Now.AddMinutes(-index))).ToList();

    private static IReadOnlyList<AlertRowViewModel> BuildAlerts(IReadOnlyList<DeviceRowViewModel> devices) =>
        Enumerable.Range(1, 18).Select(index => new AlertRowViewModel(
            $"ALT-{index:0000}",
            devices[index % devices.Count].Id,
            AlertMessages[index % AlertMessages.Length],
            index % 5 == 0 ? Severity.Critical : index % 2 == 0 ? Severity.Warning : Severity.Info,
            DateTime.Now.AddMinutes(-index * 9),
            Technicians[index % Technicians.Length],
            index % 3 == 0 ? "Acknowledged" : "Open")).ToList();

    private static IReadOnlyList<MaintenanceRowViewModel> BuildMaintenance(IReadOnlyList<CustomerRowViewModel> customers, IReadOnlyList<PurifierRowViewModel> purifiers) =>
        Enumerable.Range(1, 20).Select(index => new MaintenanceRowViewModel(
            $"WO-{index:0000}",
            customers[index % customers.Count].FullName,
            purifiers[index % purifiers.Count].SerialNumber,
            Technicians[index % Technicians.Length],
            DateTime.Today.AddHours(8 + index % 8).AddDays(index % 7),
            index % 4 == 0 ? WorkOrderStatus.Completed : index % 3 == 0 ? WorkOrderStatus.InProgress : WorkOrderStatus.Scheduled,
            20 + (index * 7) % 80,
            350000 + index * 45000)).ToList();

    private static IReadOnlyList<InventoryRowViewModel> BuildInventory() =>
        Enumerable.Range(1, 16).Select(index => new InventoryRowViewModel(
            $"PART-{index:000}",
            Parts[index % Parts.Length],
            index % 2 == 0 ? "Filter" : "Sensor",
            24 + index * 3,
            "pcs",
            85000 + index * 15000,
            40 + index,
            12 + index % 8,
            24 + index * 3)).ToList();

    private static readonly string[] CustomerNames = ["Nguyễn Minh Anh", "Trần Gia Huy", "Lê Thu Hà", "Phạm Quốc Bảo", "Hoàng Thanh Tâm", "Võ Đức Long"];
    private static readonly string[] Models = ["Aqua Pro X1", "PureMax 500", "HydroSafe S3", "Aqua Pro X2"];
    private static readonly string[] Technicians = ["Nguyễn Khoa", "Trần Phúc", "Lê Minh", "Phạm Duy"];
    private static readonly string[] AlertMessages = ["TDS vượt ngưỡng", "Áp suất thấp", "Mất kết nối MQTT", "Cần thay lõi lọc", "Nhiệt độ tăng cao"];
    private static readonly string[] Parts = ["Lõi lọc RO", "Cảm biến TDS", "Van điện từ", "Bơm áp lực", "Cảm biến áp suất", "Adapter nguồn"];
}
