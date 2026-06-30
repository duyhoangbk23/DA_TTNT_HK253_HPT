using SmartWater.Admin.ViewModels;

namespace SmartWater.Admin.Services;

public interface IMockAdminDataService
{
    DashboardViewModel GetDashboard();
    IReadOnlyList<CustomerRowViewModel> GetCustomers();
    IReadOnlyList<PurifierRowViewModel> GetPurifiers();
    IReadOnlyList<DeviceRowViewModel> GetDevices();
    DeviceDetailViewModel GetDeviceDetail(string id);
    IReadOnlyList<TelemetryRowViewModel> GetTelemetry();
    IReadOnlyList<AlertRowViewModel> GetAlerts();
    IReadOnlyList<MaintenanceRowViewModel> GetMaintenance();
    IReadOnlyList<InventoryRowViewModel> GetInventory();
}
