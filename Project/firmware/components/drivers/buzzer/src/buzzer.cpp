#include "buzzer.h"

#include <pin_map.h>

void Buzzer::begin(uint8_t pin) {
    _pin = pin;
    pinMode(_pin, OUTPUT);
    off();
}

void Buzzer::on() {
    digitalWrite(_pin, HIGH);
}

void Buzzer::off() {
    digitalWrite(_pin, LOW);
}

void Buzzer::set(bool enabled) {
    enabled ? on() : off();
}
