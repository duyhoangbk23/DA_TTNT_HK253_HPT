#include "relay.h"

#include <pin_map.h>

void Relay::begin(uint8_t pin) {
    _pin = pin;
    pinMode(_pin, OUTPUT);
    off();
}

void Relay::on() {
    digitalWrite(_pin, HIGH);
}

void Relay::off() {
    digitalWrite(_pin, LOW);
}

void Relay::set(bool enabled) {
    enabled ? on() : off();
}
