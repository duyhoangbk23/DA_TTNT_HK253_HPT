using Microsoft.AspNetCore.Mvc;
using SmartWater.Admin.Services;
using SmartWater.Admin.ViewModels;

namespace SmartWater.Admin.Controllers;

public sealed class AlertsController(IMockAdminDataService dataService) : Controller
{
    public IActionResult Index() => View(new TablePageViewModel<AlertRowViewModel>
    {
        Title = "Cảnh báo",
        Subtitle = "Phân loại critical, warning và info theo thiết bị, thời gian, người phụ trách.",
        ActionLabel = "Tạo rule",
        Rows = dataService.GetAlerts()
    });
}
