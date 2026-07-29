#include "tds_sensor.h"

#include <app_config.h>
#include <pin_map.h>
void TdsSensor::begin(uint8_t rxPin, uint8_t txPin) {
    _rxPin = rxPin;
    _txPin = txPin;
    Serial2.begin(Config::Sensor::TDS_BAUD_RATE, SERIAL_8N1, _rxPin, _txPin);
}

bool TdsSensor::read(int& tds) {
    // Dữ liệu UART được đưa vào parser để nhận bản đọc TDS hoàn chỉnh.
    while (Serial2.available() > 0) {
        _parser.push(static_cast<char>(Serial2.read()));
    }

    if (_parser.takeReading(tds)) {
        _lastTds = tds;
        _hasReading = true;
        _lastReadingMs = millis();
        return true;
    }

    if (isConnected()) {
        tds = _lastTds;
        return true;
    }

    return false;
}

bool TdsSensor::isConnected() const {
    // TDS chỉ được xem là hợp lệ trong cửa sổ timeout; quá hạn sẽ tạo alert sensor_disconnected.
    return _hasReading && (millis() - _lastReadingMs) <= Config::Sensor::TDS_TIMEOUT_MS;
}
