#include "SensorManager.h"

#include <Config.h>
#include <Logger.h>
#include <Utils.h>

SensorManager* SensorManager::_instance = nullptr;

void SensorManager::begin() {
    _instance = this;
    _mutex = xSemaphoreCreateMutex();

    pinMode(Config::Pins::TDS, INPUT);
    pinMode(Config::Pins::PRESSURE, INPUT);
    pinMode(Config::Pins::TURBIDITY, INPUT);
    pinMode(Config::Pins::FLOW, INPUT_PULLUP);
    pinMode(Config::Pins::FLOAT_SWITCH, INPUT_PULLUP);
    pinMode(Config::Pins::LEAK, INPUT_PULLUP);

    _telemetry.deviceId = Config::Device::DEVICE_ID;
    _telemetry.timestamp = millis();
    _telemetry.temperature = 0.0f;
    _telemetry.tds = 0;
    _telemetry.pressure = 0.0f;
    _telemetry.flow = 0.0f;
    _telemetry.turbidity = 0;
    _telemetry.waterLevel = 0;
    _telemetry.leak = 0;
    _telemetry.wifiRssi = 0;

    _lastFlowCalculationMs = millis();
    attachInterrupt(digitalPinToInterrupt(Config::Pins::FLOW), onFlowPulse, RISING);
    Logger::info("Sensor manager ready");
}

void SensorManager::update(int wifiRssi) {
    TelemetryData telemetry;
    telemetry.deviceId = Config::Device::DEVICE_ID;
    telemetry.timestamp = millis();
    telemetry.temperature = readTemperature();
    telemetry.tds = readTDS();
    telemetry.pressure = readPressure();
    telemetry.flow = readFlow();
    telemetry.turbidity = readTurbidity();
    telemetry.waterLevel = readFloat();
    telemetry.leak = readLeak();
    telemetry.wifiRssi = wifiRssi;

    if (_mutex != nullptr && xSemaphoreTake(_mutex, portMAX_DELAY) == pdTRUE) {
        _telemetry = telemetry;
        xSemaphoreGive(_mutex);
    }
}

TelemetryData SensorManager::getTelemetry() const {
    TelemetryData snapshot;

    if (_mutex != nullptr && xSemaphoreTake(_mutex, portMAX_DELAY) == pdTRUE) {
        snapshot = _telemetry;
        xSemaphoreGive(_mutex);
    }

    return snapshot;
}

int SensorManager::readTDS() {
    if (Config::Sensor::ENABLE_MOCK_DATA) {
        return Utils::randomInt(150, 350);
    }

    const int raw = analogRead(Config::Pins::TDS);
    const float mapped = Utils::mapFloat(static_cast<float>(raw), 0.0f, static_cast<float>(Config::Sensor::ADC_MAX), 0.0f, 1000.0f);
    return static_cast<int>(Utils::clampFloat(mapped, 0.0f, 1000.0f));
}

float SensorManager::readPressure() {
    if (Config::Sensor::ENABLE_MOCK_DATA) {
        return Utils::randomFloat(0.8f, 1.8f);
    }

    const int raw = analogRead(Config::Pins::PRESSURE);
    const float mapped = Utils::mapFloat(static_cast<float>(raw), 0.0f, static_cast<float>(Config::Sensor::ADC_MAX), 0.0f, 3.0f);
    return Utils::clampFloat(mapped, 0.0f, 3.0f);
}

float SensorManager::readFlow() {
    if (Config::Sensor::ENABLE_MOCK_DATA) {
        return Utils::randomFloat(0.2f, 1.5f);
    }

    const uint32_t now = millis();
    uint32_t pulseSnapshot = 0;

    noInterrupts();
    pulseSnapshot = _pulseCount;
    _pulseCount = 0;
    interrupts();

    const uint32_t elapsed = now - _lastFlowCalculationMs;
    _lastFlowCalculationMs = now;

    if (elapsed == 0U) {
        return 0.0f;
    }

    const float liters = static_cast<float>(pulseSnapshot) / Config::Sensor::FLOW_PULSES_PER_LITER;
    const float litersPerMinute = liters * (60000.0f / static_cast<float>(elapsed));
    return Utils::clampFloat(litersPerMinute, 0.0f, 50.0f);
}

int SensorManager::readLeak() {
    if (Config::Sensor::ENABLE_MOCK_DATA) {
        return Utils::randomInt(0, 1);
    }

    return digitalRead(Config::Pins::LEAK) == LOW ? 1 : 0;
}

int SensorManager::readFloat() {
    if (Config::Sensor::ENABLE_MOCK_DATA) {
        return Utils::randomInt(0, 1);
    }

    return digitalRead(Config::Pins::FLOAT_SWITCH) == LOW ? 1 : 0;
}

int SensorManager::readTurbidity() {
    if (Config::Sensor::ENABLE_MOCK_DATA) {
        return Utils::randomInt(5, 25);
    }

    const int raw = analogRead(Config::Pins::TURBIDITY);
    const float mapped = Utils::mapFloat(static_cast<float>(raw), 0.0f, static_cast<float>(Config::Sensor::ADC_MAX), 0.0f, 100.0f);
    return static_cast<int>(Utils::clampFloat(mapped, 0.0f, 100.0f));
}

float SensorManager::readTemperature() {
    if (Config::Sensor::ENABLE_MOCK_DATA) {
        return Utils::randomFloat(24.0f, 30.0f);
    }

    return Utils::randomFloat(24.0f, 30.0f);
}

void IRAM_ATTR SensorManager::onFlowPulse() {
    if (_instance != nullptr) {
        _instance->_pulseCount++;
    }
}

