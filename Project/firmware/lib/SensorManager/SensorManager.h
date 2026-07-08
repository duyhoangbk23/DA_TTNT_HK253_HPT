#pragma once

#include <Arduino.h>

#include <TelemetryData.h>

class SensorManager {
public:
    void begin();
    void update(int wifiRssi);

    TelemetryData getTelemetry() const;

    int readTDS();
    float readPressure();
    float readFlow();
    int readLeak();
    int readFloat();
    int readTurbidity();

private:
    float readTemperature();
    static void IRAM_ATTR onFlowPulse();

    TelemetryData _telemetry;
    mutable SemaphoreHandle_t _mutex = nullptr;

    volatile uint32_t _pulseCount = 0;
    uint32_t _lastFlowCalculationMs = 0;
    uint32_t _lastPulseSnapshot = 0;

    static SensorManager* _instance;
};

