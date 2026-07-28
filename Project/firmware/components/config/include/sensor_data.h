#pragma once

#include <Arduino.h>

struct SensorData {
    String mcuId;
    uint32_t timestamp = 0;
    int tds = 0;
    bool tdsAvailable = false;
    String alert = "sensor_disconnected";
    float pressure = 0.0f;
    float flow = 0.0f;
    int wifiRssi = 0;
};
