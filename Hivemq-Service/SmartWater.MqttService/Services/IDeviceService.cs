using SmartWater.MqttService.Models;

namespace SmartWater.MqttService.Services;

public interface IDeviceService
{
    Task ProcessMessageAsync(string payload, CancellationToken cancellationToken = default);
}
