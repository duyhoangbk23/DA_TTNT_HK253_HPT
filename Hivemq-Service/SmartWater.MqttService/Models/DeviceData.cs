namespace SmartWater.MqttService.Models;

public class DeviceData
{
    public long Id { get; set; }
    public string DeviceId { get; set; } = string.Empty;
    public DateTime DataTime { get; set; }
    public decimal Tds { get; set; }
    public decimal Temperature { get; set; }
    public sbyte Alert { get; set; }
    public DateTime CreatedAt { get; set; }
}
