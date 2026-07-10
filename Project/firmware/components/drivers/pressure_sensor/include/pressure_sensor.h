#pragma once

#include <Arduino.h>

#include <pin_map.h>

class PressureSensor {
public:
    void begin(uint8_t pin = Config::Pins::PRESSURE);
    float read();

private:
    uint8_t _pin = Config::Pins::PRESSURE;
};
