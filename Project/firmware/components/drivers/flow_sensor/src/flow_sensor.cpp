#include "flow_sensor.h"

#include <app_config.h>
#include <pin_map.h>
#include <utils.h>

FlowSensor* FlowSensor::_instance = nullptr;

void FlowSensor::begin(uint8_t pin) {
    _instance = this;
    _pin = pin;
    pinMode(_pin, INPUT_PULLUP);
    _lastCalculationMs = millis();
    attachInterrupt(digitalPinToInterrupt(_pin), onPulse, RISING);
}

float FlowSensor::read() {
    if (Config::Sensor::ENABLE_MOCK_DATA) {
        return Utils::randomFloat(0.2f, 1.5f);
    }

    const uint32_t now = millis();
    uint32_t pulseSnapshot = 0;

    noInterrupts();
    pulseSnapshot = _pulseCount;
    _pulseCount = 0;
    interrupts();

    const uint32_t elapsed = now - _lastCalculationMs;
    _lastCalculationMs = now;

    if (elapsed == 0U) {
        return 0.0f;
    }

    const float liters = static_cast<float>(pulseSnapshot) / Config::Sensor::FLOW_PULSES_PER_LITER;
    const float litersPerMinute = liters * (60000.0f / static_cast<float>(elapsed));
    return Utils::clampFloat(litersPerMinute, 0.0f, 50.0f);
}

void IRAM_ATTR FlowSensor::onPulse() {
    if (_instance != nullptr) {
        _instance->_pulseCount++;
    }
}
