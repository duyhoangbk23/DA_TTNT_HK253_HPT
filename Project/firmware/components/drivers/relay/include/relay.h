#pragma once

#include <Arduino.h>

#include <pin_map.h>

class Relay {
public:
    void begin(uint8_t pin = Config::Pins::RELAY);
    void on();
    void off();
    void set(bool enabled);

private:
    uint8_t _pin = Config::Pins::RELAY;
};
