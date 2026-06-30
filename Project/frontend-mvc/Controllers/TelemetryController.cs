using Microsoft.AspNetCore.Mvc;
using SmartWater.Admin.Services;
using SmartWater.Admin.ViewModels;

namespace SmartWater.Admin.Controllers;

public sealed class TelemetryController(IMockAdminDataService dataService) : Controller
{
    public IActionResult Index() => View(new TablePageViewModel<TelemetryRowViewModel>
    {
        Title = "Telemetry",
        Subtitle = "Realtime dashboard với auto refresh giả lập cho flow, pressure, TDS, pH và trạng thái bơm.",
        ActionLabel = "Auto refresh",
        Rows = dataService.GetTelemetry()
    });
}
