namespace SmartWater.MqttService.Models;

public class Device
{
    public long Id { get; set; }
    public string DeviceId { get; set; } = string.Empty;
    public string DeviceName { get; set; } = string.Empty;
    public sbyte Status { get; set; }
    public DateTime CreatedAt { get; set; }
}
