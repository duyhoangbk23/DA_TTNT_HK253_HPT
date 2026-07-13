namespace SmartWater.MqttService.Configurations;

public class MySqlOptions
{
    public const string SectionName = "MySql";

    public string Server { get; set; } = "127.0.0.1";
    public int Port { get; set; } = 3306;
    public string Database { get; set; } = "smartwater";
    public string User { get; set; } = "root";
    public string Password { get; set; } = "123456";

    public string GetConnectionString()
    {
        var builder = new MySqlConnector.MySqlConnectionStringBuilder
        {
            Server = Server,
            Port = (uint)Port,
            Database = Database,
            UserID = User,
            Password = Password,
            Pooling = true,
            MinimumPoolSize = 1,
            MaximumPoolSize = 10,
            ConnectionIdleTimeout = 300,
            ConnectionLifeTime = 1800
        };
        return builder.ConnectionString;
    }
}
