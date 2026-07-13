using SmartWater.MqttService;
using SmartWater.MqttService.Configurations;
using SmartWater.MqttService.Repositories;
using SmartWater.MqttService.Services;

var builder = Host.CreateApplicationBuilder(args);

builder.Services.Configure<HiveMQOptions>(builder.Configuration.GetSection(HiveMQOptions.SectionName));
builder.Services.Configure<MySqlOptions>(builder.Configuration.GetSection(MySqlOptions.SectionName));

builder.Services.AddSingleton(sp =>
{
    var options = sp.GetRequiredService<Microsoft.Extensions.Options.IOptions<HiveMQOptions>>();
    return options.Value;
});
builder.Services.AddSingleton(sp =>
{
    var options = sp.GetRequiredService<Microsoft.Extensions.Options.IOptions<MySqlOptions>>();
    return options.Value;
});

builder.Services.AddSingleton<IDeviceRepository, DeviceRepository>();
builder.Services.AddSingleton<IDeviceService, DeviceService>();
builder.Services.AddSingleton<IMqttService, MqttService>();

builder.Services.AddHostedService<Worker>();

builder.Services.AddWindowsService(options =>
{
    options.ServiceName = "SmartWaterMQTT";
});

var host = builder.Build();
host.Run();
