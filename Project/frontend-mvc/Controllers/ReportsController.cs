using Microsoft.AspNetCore.Mvc;
using SmartWater.Admin.Services;

namespace SmartWater.Admin.Controllers;

public sealed class ReportsController(IMockAdminDataService dataService) : Controller
{
    public IActionResult Index() => View(dataService.GetDashboard().Charts);
}
