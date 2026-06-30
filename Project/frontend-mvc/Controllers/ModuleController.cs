using Microsoft.AspNetCore.Mvc;

namespace SmartWater.Admin.Controllers;

public sealed class AppointmentsController : ModuleControllerBase { public AppointmentsController() : base("Lịch hẹn", "Lịch hẹn bảo trì, sửa chữa và chăm sóc khách hàng.") { } }
public sealed class TechniciansController : ModuleControllerBase { public TechniciansController() : base("Kỹ thuật viên", "Phân công, năng lực, lịch làm việc và hiệu suất kỹ thuật viên.") { } }
public sealed class FirmwareController : ModuleControllerBase { public FirmwareController() : base("Firmware OTA", "Quản lý phiên bản, lịch sử OTA và rollout firmware.") { } }
public sealed class WarrantyController : ModuleControllerBase { public WarrantyController() : base("Bảo hành", "Theo dõi thời hạn, điều kiện và yêu cầu bảo hành.") { } }
public sealed class InvoicesController : ModuleControllerBase { public InvoicesController() : base("Hóa đơn", "Quản lý hóa đơn dịch vụ, linh kiện và thanh toán.") { } }
public sealed class AuditController : ModuleControllerBase { public AuditController() : base("Nhật ký hệ thống", "Audit log, login history và hoạt động hệ thống.") { } }
public sealed class UsersController : ModuleControllerBase { public UsersController() : base("Người dùng", "Quản trị tài khoản, trạng thái và phiên đăng nhập.") { } }
public sealed class RolesController : ModuleControllerBase { public RolesController() : base("Phân quyền", "Role, permission và phân quyền theo nghiệp vụ.") { } }
public sealed class SettingsController : ModuleControllerBase { public SettingsController() : base("Cấu hình", "Cấu hình chung, MQTT, thông báo, theme và ngôn ngữ.") { } }

public abstract class ModuleControllerBase(string title, string subtitle) : Controller
{
    public IActionResult Index() => View("~/Views/Shared/Module.cshtml", (title, subtitle));
}
