using System.Text.Json;
using SmartWater.MqttService.Models;
using SmartWater.MqttService.Repositories;

namespace SmartWater.MqttService.Services;

public class DeviceService : IDeviceService
{
    private readonly IDeviceRepository _deviceRepository;

    public DeviceService(IDeviceRepository deviceRepository)
    {
        _deviceRepository = deviceRepository;
    }

    public async Task ProcessMessageAsync(string payload, CancellationToken cancellationToken = default)
    {
        DeviceDataDto? dto;

        try
        {
            dto = JsonSerializer.Deserialize<DeviceDataDto>(payload);
        }
        catch (JsonException)
        {
            return;
        }

        if (dto is null || string.IsNullOrWhiteSpace(dto.DeviceId))
            return;

        var deviceData = new DeviceData
        {
            DeviceId = dto.DeviceId,
            DataTime = dto.Timestamp,
            Tds = dto.Tds,
            Temperature = dto.Temperature,
            Alert = (sbyte)(dto.Alert ? 1 : 0),
            CreatedAt = DateTime.UtcNow
        };

        await _deviceRepository.InsertDeviceDataAsync(deviceData, cancellationToken);

        var existing = await _deviceRepository.GetDeviceByIdAsync(dto.DeviceId, cancellationToken);
        if (existing is null)
        {
            var device = new Device
            {
                DeviceId = dto.DeviceId,
                DeviceName = dto.DeviceId,
                Status = 1,
                CreatedAt = DateTime.UtcNow
            };
            await _deviceRepository.UpsertDeviceAsync(device, cancellationToken);
        }
    }
}
