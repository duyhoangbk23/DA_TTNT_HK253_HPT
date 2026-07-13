namespace SmartWater.MqttService.Configurations;

public class HiveMQOptions
{
    public const string SectionName = "HiveMQ";

    public string Host { get; set; } = "broker.hivemq.com";
    public int Port { get; set; } = 8883;
    public string Username { get; set; } = string.Empty;
    public string Password { get; set; } = string.Empty;
    public string ClientId { get; set; } = "SmartWater-Service";
    public string Topic { get; set; } = "device/+/status";
    public int KeepAlive { get; set; } = 60;
    public bool UseTls { get; set; } = true;
}
