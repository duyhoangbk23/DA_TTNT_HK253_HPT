namespace SmartWater.Admin.ViewModels;

public sealed class TablePageViewModel<TRow>
{
    public required string Title { get; init; }
    public required string Subtitle { get; init; }
    public required string ActionLabel { get; init; }
    public required IReadOnlyList<TRow> Rows { get; init; }
}

public sealed class DeviceDetailViewModel
{
    public required DeviceRowViewModel Device { get; init; }
    public required IReadOnlyList<KpiCardViewModel> Metrics { get; init; }
    public required IReadOnlyList<TelemetryRowViewModel> RealtimeData { get; init; }
    public required IReadOnlyList<ActivityItemViewModel> Logs { get; init; }
}
