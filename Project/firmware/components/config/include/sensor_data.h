#pragma once

#include <Arduino.h>

struct SensorData {
    String deviceId;
    uint32_t timestamp = 0;
    int tds = 0;
    float pressure = 0.0f;
    float flow = 0.0f;
    int wifiRssi = 0;
};
