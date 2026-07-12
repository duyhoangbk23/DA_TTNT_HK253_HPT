#pragma once

#include <Arduino.h>
#include <ArduinoJson.h>

#include "../config/SimulatorConfig.h"
#include "../device/DeviceInfo.h"
#include "../logger/Logger.h"
#include "../mqtt/MqttManager.h"
#include "../telemetry/TelemetryGenerator.h"
#include "../wifi/WifiManager.h"

class SimulatorApp {
   public:
    void begin();
    void loop();

   private:
    void publishTelemetry();
    void publishStatus();
    static void onCommand(const String& topic, const String& payload);

    DeviceInfo deviceInfo_{
        SimulatorConfig::DEVICE_ID,
        SimulatorConfig::SERIAL_NUMBER,
        SimulatorConfig::FIRMWARE_VERSION,
        SimulatorConfig::MODEL,
    };

    WifiManager wifiManager_;
    MqttManager mqttManager_;
    RandomTelemetryGenerator telemetryGenerator_;
    unsigned long lastTelemetryMs_ = 0;
    unsigned long lastStatusMs_ = 0;
};
