namespace SmartWater.MqttService.Services;

public interface IMqttService
{
    Task StartAsync(CancellationToken cancellationToken = default);
    Task StopAsync(CancellationToken cancellationToken = default);
}
