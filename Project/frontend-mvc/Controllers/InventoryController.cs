using Microsoft.AspNetCore.Mvc;
using SmartWater.Admin.Services;
using SmartWater.Admin.ViewModels;

namespace SmartWater.Admin.Controllers;

public sealed class InventoryController(IMockAdminDataService dataService) : Controller
{
    public IActionResult Index() => View(new TablePageViewModel<InventoryRowViewModel>
    {
        Title = "Kho linh kiện",
        Subtitle = "Quản lý nhập, xuất, tồn và định giá linh kiện thay thế.",
        ActionLabel = "Nhập kho",
        Rows = dataService.GetInventory()
    });
}
