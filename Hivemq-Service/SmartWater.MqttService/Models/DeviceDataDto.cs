using System.Text.Json.Serialization;

namespace SmartWater.MqttService.Models;

public class DeviceDataDto
{
    [JsonPropertyName("device_id")]
    public string DeviceId { get; set; } = string.Empty;

    [JsonPropertyName("timestamp")]
    public DateTime Timestamp { get; set; }

    [JsonPropertyName("tds")]
    public decimal Tds { get; set; }

    [JsonPropertyName("alert")]
    public bool Alert { get; set; }
}
