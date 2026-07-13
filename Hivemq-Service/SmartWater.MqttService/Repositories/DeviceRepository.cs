using Dapper;
using MySqlConnector;
using SmartWater.MqttService.Configurations;
using SmartWater.MqttService.Models;

namespace SmartWater.MqttService.Repositories;

public class DeviceRepository : IDeviceRepository
{
    private readonly string _connectionString;

    public DeviceRepository(MySqlOptions mySqlOptions)
    {
        _connectionString = mySqlOptions.GetConnectionString();
    }

    private MySqlConnection CreateConnection() => new(_connectionString);

    public async Task InsertDeviceDataAsync(DeviceData data, CancellationToken cancellationToken = default)
    {
        using var connection = CreateConnection();
        const string sql = @"
            INSERT INTO device_data (device_id, data_time, tds, temperature, alert, created_at)
            VALUES (@DeviceId, @DataTime, @Tds, @Temperature, @Alert, @CreatedAt);";

        await connection.ExecuteAsync(
            new CommandDefinition(sql, data, cancellationToken: cancellationToken));
    }

    public async Task<Device?> GetDeviceByIdAsync(string deviceId, CancellationToken cancellationToken = default)
    {
        using var connection = CreateConnection();
        const string sql = "SELECT id, device_id, device_name, status, created_at FROM devices WHERE device_id = @DeviceId LIMIT 1;";

        return await connection.QueryFirstOrDefaultAsync<Device>(
            new CommandDefinition(sql, new { DeviceId = deviceId }, cancellationToken: cancellationToken));
    }

    public async Task UpsertDeviceAsync(Device device, CancellationToken cancellationToken = default)
    {
        using var connection = CreateConnection();
        const string sql = @"
            INSERT INTO devices (device_id, device_name, status, created_at)
            VALUES (@DeviceId, @DeviceName, @Status, @CreatedAt)
            ON DUPLICATE KEY UPDATE
                device_name = VALUES(device_name),
                status = VALUES(status);";

        await connection.ExecuteAsync(
            new CommandDefinition(sql, device, cancellationToken: cancellationToken));
    }
}
