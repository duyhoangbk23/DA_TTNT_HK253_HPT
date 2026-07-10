#include "sensor_manager.h"

#include <app_config.h>
#include <logger.h>
#include <pin_map.h>

void SensorManager::begin() {
    _mutex = xSemaphoreCreateMutex();

    _tdsSensor.begin(Config::Pins::TDS);
    _pressureSensor.begin(Config::Pins::PRESSURE);
    _flowSensor.begin(Config::Pins::FLOW);

    _telemetry.deviceId = Config::Device::DEVICE_ID;
    _telemetry.timestamp = millis();
    _telemetry.tds = 0;
    _telemetry.pressure = 0.0f;
    _telemetry.flow = 0.0f;
    _telemetry.wifiRssi = 0;

    Logger::info("Sensor manager ready");
}

void SensorManager::update(int wifiRssi) {
    SensorData telemetry;
    telemetry.deviceId = Config::Device::DEVICE_ID;
    telemetry.timestamp = millis();
    telemetry.tds = _tdsSensor.read();
    telemetry.pressure = _pressureSensor.read();
    telemetry.flow = _flowSensor.read();
    telemetry.wifiRssi = wifiRssi;

    if (_mutex != nullptr && xSemaphoreTake(_mutex, portMAX_DELAY) == pdTRUE) {
        _telemetry = telemetry;
        xSemaphoreGive(_mutex);
    }
}

SensorData SensorManager::getTelemetry() const {
    SensorData snapshot;

    if (_mutex != nullptr && xSemaphoreTake(_mutex, portMAX_DELAY) == pdTRUE) {
        snapshot = _telemetry;
        xSemaphoreGive(_mutex);
    }

    return snapshot;
}
