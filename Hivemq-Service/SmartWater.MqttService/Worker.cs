using SmartWater.MqttService.Services;

namespace SmartWater.MqttService;

public class Worker : BackgroundService
{
    private readonly IMqttService _mqttService;

    public Worker(IMqttService mqttService)
    {
        _mqttService = mqttService;
    }

    protected override async Task ExecuteAsync(CancellationToken stoppingToken)
    {
        await _mqttService.StartAsync(stoppingToken);
        await Task.Delay(Timeout.Infinite, stoppingToken);
    }

    public override async Task StopAsync(CancellationToken cancellationToken)
    {
        await _mqttService.StopAsync(cancellationToken);
        await base.StopAsync(cancellationToken);
    }
}
