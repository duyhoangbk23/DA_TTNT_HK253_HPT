namespace SmartWater.Admin.Enums;

public enum EntityStatus
{
    Active,
    Inactive,
    Maintenance,
    Fault
}

public enum Severity
{
    Critical,
    Warning,
    Info
}

public enum WorkOrderStatus
{
    Scheduled,
    InProgress,
    Completed,
    Cancelled
}
