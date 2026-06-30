using Microsoft.AspNetCore.Mvc;
using SmartWater.Admin.Services;
using SmartWater.Admin.ViewModels;

namespace SmartWater.Admin.Controllers;

public sealed class MaintenanceController(IMockAdminDataService dataService) : Controller
{
    public IActionResult Index() => View(new TablePageViewModel<MaintenanceRowViewModel>
    {
        Title = "Bảo trì",
        Subtitle = "Lập lịch, checklist, tiến độ, kỹ thuật viên, chi phí và biên bản bảo trì.",
        ActionLabel = "Lập lịch",
        Rows = dataService.GetMaintenance()
    });
}
