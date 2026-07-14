#pragma once

#include <Arduino.h>

#include <pin_map.h>

class Buzzer {
public:
    void begin(uint8_t pin = Config::Pins::BUZZER);
    void on();
    void off();
    void set(bool enabled);

private:
    uint8_t _pin = Config::Pins::BUZZER;
};
