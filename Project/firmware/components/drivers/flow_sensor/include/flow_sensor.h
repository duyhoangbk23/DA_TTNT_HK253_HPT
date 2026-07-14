#pragma once

#include <Arduino.h>

#include <pin_map.h>

class FlowSensor {
public:
    void begin(uint8_t pin = Config::Pins::FLOW);
    float read();

private:
    static void IRAM_ATTR onPulse();

    static FlowSensor* _instance;
    volatile uint32_t _pulseCount = 0;
    uint32_t _lastCalculationMs = 0;
    uint8_t _pin = Config::Pins::FLOW;
};
