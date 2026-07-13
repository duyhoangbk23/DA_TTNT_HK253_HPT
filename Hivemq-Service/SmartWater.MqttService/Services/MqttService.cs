using MQTTnet;
using MQTTnet.Client;
using MQTTnet.Protocol;
using SmartWater.MqttService.Configurations;

namespace SmartWater.MqttService.Services;

public class MqttService : IMqttService, IDisposable
{
    private readonly HiveMQOptions _options;
    private readonly IDeviceService _deviceService;
    private readonly IMqttClient _client;
    private readonly MqttClientOptions _clientOptions;
    private readonly SemaphoreSlim _reconnectLock = new(1, 1);
    private CancellationTokenSource? _reconnectCts;
    private bool _disposed;

    public MqttService(HiveMQOptions options, IDeviceService deviceService)
    {
        _options = options;
        _deviceService = deviceService;

        var factory = new MqttFactory();
        _client = factory.CreateMqttClient();

        var builder = new MqttClientOptionsBuilder()
            .WithTcpServer(_options.Host, _options.Port)
            .WithClientId(_options.ClientId)
            .WithKeepAlivePeriod(TimeSpan.FromSeconds(_options.KeepAlive))
            .WithCleanSession();

        if (_options.UseTls)
        {
            builder.WithTlsOptions(o => o.UseTls());
        }

        if (!string.IsNullOrEmpty(_options.Username))
            builder.WithCredentials(_options.Username, _options.Password);

        _clientOptions = builder.Build();

        _client.DisconnectedAsync += OnDisconnectedAsync;
        _client.ApplicationMessageReceivedAsync += OnMessageReceivedAsync;
    }

    public async Task StartAsync(CancellationToken cancellationToken = default)
    {
        _reconnectCts = CancellationTokenSource.CreateLinkedTokenSource(cancellationToken);
        await ConnectAsync(cancellationToken);
    }

    public async Task StopAsync(CancellationToken cancellationToken = default)
    {
        _reconnectCts?.Cancel();

        if (_client.IsConnected)
        {
            await _client.DisconnectAsync(
                new MqttClientDisconnectOptionsBuilder().WithReason(MqttClientDisconnectOptionsReason.NormalDisconnection).Build(),
                cancellationToken);
        }

        _client.DisconnectedAsync -= OnDisconnectedAsync;
        _client.ApplicationMessageReceivedAsync -= OnMessageReceivedAsync;
    }

    private async Task ConnectAsync(CancellationToken cancellationToken = default)
    {
        try
        {
            if (_client.IsConnected)
                return;

            var result = await _client.ConnectAsync(_clientOptions, cancellationToken);

            if (result.ResultCode == MqttClientConnectResultCode.Success)
            {
                var topics = _options.Topic.Split(',', StringSplitOptions.TrimEntries | StringSplitOptions.RemoveEmptyEntries);

                foreach (var topic in topics)
                {
                    await _client.SubscribeAsync(topic, MqttQualityOfServiceLevel.AtLeastOnce, cancellationToken: cancellationToken);
                }
            }
        }
        catch (Exception)
        {
            await TryReconnectAsync(cancellationToken);
        }
    }

    private async Task OnDisconnectedAsync(MqttClientDisconnectedEventArgs args)
    {
        if (args.Reason == MqttClientDisconnectReason.NormalDisconnection)
            return;

        await TryReconnectAsync(_reconnectCts?.Token ?? CancellationToken.None);
    }

    private async Task TryReconnectAsync(CancellationToken cancellationToken)
    {
        if (!await _reconnectLock.WaitAsync(0, cancellationToken))
            return;

        try
        {
            while (!cancellationToken.IsCancellationRequested && !_client.IsConnected)
            {
                try
                {
                    await Task.Delay(TimeSpan.FromSeconds(5), cancellationToken);
                    await ConnectAsync(cancellationToken);
                }
                catch (OperationCanceledException)
                {
                    break;
                }
                catch
                {
                }
            }
        }
        finally
        {
            _reconnectLock.Release();
        }
    }

    private async Task OnMessageReceivedAsync(MqttApplicationMessageReceivedEventArgs args)
    {
        var payload = args.ApplicationMessage?.PayloadSegment;
        if (payload is null || payload.Value.Count == 0)
            return;

        var message = System.Text.Encoding.UTF8.GetString(payload.Value);
        await _deviceService.ProcessMessageAsync(message, CancellationToken.None);
    }

    public void Dispose()
    {
        if (_disposed) return;
        _disposed = true;
        _reconnectCts?.Cancel();
        _reconnectCts?.Dispose();
        _reconnectLock.Dispose();
        _client?.Dispose();
    }
}
