#include "pressure_sensor.h"

#include <app_config.h>
#include <pin_map.h>
#include <utils.h>

void PressureSensor::begin(uint8_t pin) {
    _pin = pin;
    pinMode(_pin, INPUT);
}

float PressureSensor::read() {
    if (Config::Sensor::ENABLE_MOCK_DATA) {
        return Utils::randomFloat(0.8f, 1.8f);
    }

    const int raw = analogRead(_pin);
    const float mapped = Utils::mapFloat(static_cast<float>(raw), 0.0f, static_cast<float>(Config::Sensor::ADC_MAX), 0.0f, 3.0f);
    return Utils::clampFloat(mapped, 0.0f, 3.0f);
}
