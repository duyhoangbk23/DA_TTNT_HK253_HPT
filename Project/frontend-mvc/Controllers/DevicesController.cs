using Microsoft.AspNetCore.Mvc;
using SmartWater.Admin.Services;
using SmartWater.Admin.ViewModels;

namespace SmartWater.Admin.Controllers;

public sealed class DevicesController(IMockAdminDataService dataService) : Controller
{
    public IActionResult Index() => View(new TablePageViewModel<DeviceRowViewModel>
    {
        Title = "Thiết bị ESP32",
        Subtitle = "Giám sát firmware, WiFi RSSI, MQTT, IP, MAC, online và pin.",
        ActionLabel = "Đăng ký thiết bị",
        Rows = dataService.GetDevices()
    });

    public IActionResult Details(string id) => View(dataService.GetDeviceDetail(id));
}
