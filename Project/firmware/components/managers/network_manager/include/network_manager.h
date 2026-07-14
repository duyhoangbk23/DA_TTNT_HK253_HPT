#pragma once

#include <Arduino.h>
#include <ArduinoJson.h>

#include <mqtt_manager.h>
#include <sensor_data.h>
#include <wifi_manager.h>

class NetworkManager {
public:
    using CommandCallback = MQTTManager::CommandCallback;

    void begin();
    void update();
    void loop();

    bool isWifiConnected() const;
    bool isMqttConnected();
    int getRSSI() const;

    bool publishTelemetry(const SensorData& telemetry);
    bool publishStatus(const SensorData& telemetry, const char* status = "online");
    void setCommandCallback(CommandCallback callback);

private:
    WifiManager _wifiManager;
    MQTTManager _mqttManager;
};
