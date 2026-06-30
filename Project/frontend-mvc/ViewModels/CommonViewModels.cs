using SmartWater.Admin.Enums;

namespace SmartWater.Admin.ViewModels;

public sealed record KpiCardViewModel(string Title, string Value, string Delta, string Icon, string Tone);
public sealed record ChartCardViewModel(string Title, string ElementId, string Type, IReadOnlyList<decimal> Values, IReadOnlyList<string> Labels);
public sealed record ActivityItemViewModel(string Title, string Detail, string Time, string Icon);
public sealed record CustomerRowViewModel(string Id, string Avatar, string FullName, string Phone, string Email, string Address, int DeviceCount, DateOnly CreatedAt, EntityStatus Status);
public sealed record PurifierRowViewModel(string Id, string Model, string SerialNumber, string Firmware, string Esp32, string Customer, string Location, DateOnly InstalledAt, DateOnly WarrantyUntil, EntityStatus Status);
public sealed record DeviceRowViewModel(string Id, string Firmware, int WifiRssi, string Ip, string Mac, string Mqtt, DateTime LastSeen, bool Online, int Battery);
public sealed record TelemetryRowViewModel(string DeviceId, decimal Flow, decimal Pressure, decimal Temperature, int Tds, decimal Ph, decimal Voltage, decimal Current, string PumpStatus, string ValveStatus, int TankLevel, DateTime Time);
public sealed record AlertRowViewModel(string Id, string DeviceId, string Message, Severity Severity, DateTime Time, string Owner, string Status);
public sealed record MaintenanceRowViewModel(string Id, string Customer, string Purifier, string Technician, DateTime Schedule, WorkOrderStatus Status, int Progress, decimal Cost);
public sealed record InventoryRowViewModel(string Code, string Name, string Category, int Quantity, string Unit, decimal Price, int Import, int Export, int Stock);

public sealed class DashboardViewModel
{
    public required IReadOnlyList<KpiCardViewModel> Kpis { get; init; }
    public required IReadOnlyList<ChartCardViewModel> Charts { get; init; }
    public required IReadOnlyList<ActivityItemViewModel> RecentAlerts { get; init; }
    public required IReadOnlyList<MaintenanceRowViewModel> RecentMaintenance { get; init; }
    public required IReadOnlyList<CustomerRowViewModel> NewestCustomers { get; init; }
    public required IReadOnlyList<ActivityItemViewModel> SystemHealth { get; init; }
}
