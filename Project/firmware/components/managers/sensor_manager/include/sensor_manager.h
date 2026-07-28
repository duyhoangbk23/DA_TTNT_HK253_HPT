#pragma once

#include <Arduino.h>

#include <flow_sensor.h>
#include <pressure_sensor.h>
#include <sensor_data.h>
#include <tds_sensor.h>

class SensorManager {
public:
    void begin();
    void update(int wifiRssi);

    SensorData getTelemetry() const;
    bool consumeSensorError(SensorData& telemetry);

private:
    TdsSensor _tdsSensor;
    PressureSensor _pressureSensor;
    FlowSensor _flowSensor;

    SensorData _telemetry;
    bool _sensorErrorActive = false;
    bool _sensorErrorPending = false;
    mutable SemaphoreHandle_t _mutex = nullptr;
};
