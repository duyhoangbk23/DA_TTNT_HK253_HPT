#include "tds_sensor.h"

#include <app_config.h>
#include <pin_map.h>
#include <utils.h>

void TdsSensor::begin(uint8_t pin) {
    _pin = pin;
    pinMode(_pin, INPUT);
}

int TdsSensor::read() {
    if (Config::Sensor::ENABLE_MOCK_DATA) {
        return Utils::randomInt(150, 350);
    }

    const int raw = analogRead(_pin);
    const float mapped = Utils::mapFloat(static_cast<float>(raw), 0.0f, static_cast<float>(Config::Sensor::ADC_MAX), 0.0f, 1000.0f);
    return static_cast<int>(Utils::clampFloat(mapped, 0.0f, 1000.0f));
}
