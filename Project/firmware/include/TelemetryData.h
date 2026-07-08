#pragma once

#include <Arduino.h>

struct TelemetryData {
    String deviceId;
    uint32_t timestamp = 0;
    float temperature = 0.0f;
    int tds = 0;
    float pressure = 0.0f;
    float flow = 0.0f;
    int turbidity = 0;
    int waterLevel = 0;
    int leak = 0;
    int wifiRssi = 0;
};

