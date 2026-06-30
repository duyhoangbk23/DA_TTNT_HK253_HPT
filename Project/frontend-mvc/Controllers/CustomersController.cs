using Microsoft.AspNetCore.Mvc;
using SmartWater.Admin.Services;
using SmartWater.Admin.ViewModels;

namespace SmartWater.Admin.Controllers;

public sealed class CustomersController(IMockAdminDataService dataService) : Controller
{
    public IActionResult Index() => View(new TablePageViewModel<CustomerRowViewModel>
    {
        Title = "Khách hàng",
        Subtitle = "Quản lý hồ sơ, thiết bị, thanh toán và lịch sử dịch vụ của khách hàng.",
        ActionLabel = "Thêm khách hàng",
        Rows = dataService.GetCustomers()
    });
}
