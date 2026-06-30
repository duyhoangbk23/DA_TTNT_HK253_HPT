using SmartWater.Admin.Enums;

namespace SmartWater.Admin.Helpers;

public static class UiHelper
{
    public static string StatusBadge(EntityStatus status) => status switch
    {
        EntityStatus.Active => "text-bg-success",
        EntityStatus.Inactive => "text-bg-secondary",
        EntityStatus.Maintenance => "text-bg-warning",
        EntityStatus.Fault => "text-bg-danger",
        _ => "text-bg-secondary"
    };

    public static string SeverityBadge(Severity severity) => severity switch
    {
        Severity.Critical => "text-bg-danger",
        Severity.Warning => "text-bg-warning",
        Severity.Info => "text-bg-info",
        _ => "text-bg-secondary"
    };

    public static string WorkOrderBadge(WorkOrderStatus status) => status switch
    {
        WorkOrderStatus.Scheduled => "text-bg-primary",
        WorkOrderStatus.InProgress => "text-bg-warning",
        WorkOrderStatus.Completed => "text-bg-success",
        WorkOrderStatus.Cancelled => "text-bg-secondary",
        _ => "text-bg-secondary"
    };
}
