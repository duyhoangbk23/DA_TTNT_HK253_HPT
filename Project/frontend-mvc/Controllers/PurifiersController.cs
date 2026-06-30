using Microsoft.AspNetCore.Mvc;
using SmartWater.Admin.Services;
using SmartWater.Admin.ViewModels;

namespace SmartWater.Admin.Controllers;

public sealed class PurifiersController(IMockAdminDataService dataService) : Controller
{
    public IActionResult Index() => View(new TablePageViewModel<PurifierRowViewModel>
    {
        Title = "Máy lọc nước",
        Subtitle = "Theo dõi model, serial, firmware, ESP32, bảo hành, bộ lọc và trạng thái vận hành.",
        ActionLabel = "Thêm máy",
        Rows = dataService.GetPurifiers()
    });
}
