using SmartWater.MqttService.Models;

namespace SmartWater.MqttService.Repositories;

public interface IDeviceRepository
{
    Task InsertDeviceDataAsync(DeviceData data, CancellationToken cancellationToken = default);
    Task<Device?> GetDeviceByIdAsync(string deviceId, CancellationToken cancellationToken = default);
    Task UpsertDeviceAsync(Device device, CancellationToken cancellationToken = default);
}
