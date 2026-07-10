#pragma once

#include <Arduino.h>

#include <pin_map.h>

class TdsSensor {
public:
    void begin(uint8_t pin = Config::Pins::TDS);
    int read();

private:
    uint8_t _pin = Config::Pins::TDS;
};
