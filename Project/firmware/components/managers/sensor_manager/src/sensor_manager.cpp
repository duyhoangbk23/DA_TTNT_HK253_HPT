#include "sensor_manager.h"

#include <app_config.h>
#include <logger.h>
#include <pin_map.h>

void SensorManager::begin() {
    _mutex = xSemaphoreCreateMutex();

    _tdsSensor.begin(Config::Pins::TDS_RX, Config::Pins::TDS_TX);
    _pressureSensor.begin(Config::Pins::PRESSURE);
    _flowSensor.begin(Config::Pins::FLOW);

    _telemetry.mcuId = Config::Device::MCU_ID;
    _telemetry.timestamp = millis();
    _telemetry.tds = 0;
    _telemetry.tdsAvailable = false;
    _telemetry.alert = "sensor_disconnected";
    _telemetry.pressure = 0.0f;
    _telemetry.flow = 0.0f;
    _telemetry.wifiRssi = 0;

    Logger::info("Sensor manager ready");
}

void SensorManager::update(int wifiRssi) {
    // Snapshot cảm biến được bảo vệ bằng mutex để task đọc cảm biến và task MQTT không dùng dữ liệu đang cập nhật dở.
    SensorData telemetry;
    telemetry.mcuId = Config::Device::MCU_ID;
    telemetry.timestamp = millis();
    telemetry.tdsAvailable = _tdsSensor.read(telemetry.tds);
    telemetry.alert = telemetry.tdsAvailable ? "normal" : "sensor_disconnected";
    telemetry.pressure = _pressureSensor.read();
    telemetry.flow = _flowSensor.read();
    telemetry.wifiRssi = wifiRssi;

    if (_mutex != nullptr && xSemaphoreTake(_mutex, portMAX_DELAY) == pdTRUE) {
        if (!telemetry.tdsAvailable && !_sensorErrorActive) {
            _sensorErrorActive = true;
            _sensorErrorPending = true;
            Logger::error("TDS sensor disconnected or no UART data on RX %u", Config::Pins::TDS_RX);
        } else if (telemetry.tdsAvailable && _sensorErrorActive) {
            _sensorErrorActive = false;
            Logger::info("TDS sensor UART data restored");
        }

        _telemetry = telemetry;
        xSemaphoreGive(_mutex);
    }
}

bool SensorManager::consumeSensorError(SensorData& telemetry) {
    // TDS chỉ được xem là hợp lệ trong cửa sổ timeout; quá hạn sẽ tạo alert sensor_disconnected.
    if (_mutex == nullptr || xSemaphoreTake(_mutex, portMAX_DELAY) != pdTRUE) {
        return false;
    }

    const bool pending = _sensorErrorPending;
    if (pending) {
        telemetry = _telemetry;
        _sensorErrorPending = false;
    }
    xSemaphoreGive(_mutex);

    return pending;
}

SensorData SensorManager::getTelemetry() const {
    SensorData snapshot;

    if (_mutex != nullptr && xSemaphoreTake(_mutex, portMAX_DELAY) == pdTRUE) {
        snapshot = _telemetry;
        xSemaphoreGive(_mutex);
    }

    return snapshot;
}
