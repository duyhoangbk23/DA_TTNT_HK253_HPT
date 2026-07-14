#pragma once

#include <Arduino.h>
#include <ArduinoJson.h>

#include "../config/SimulatorConfig.h"
#include "../mqtt/MqttManager.h"
#include "../wifi/WifiManager.h"

class SimulatorApp {
   public:
    void begin();
    void loop();

   private:
    void publishTelemetry();

    WifiManager wifiManager_;
    MqttManager mqttManager_;
    unsigned long lastTelemetryMs_ = 0;
};
