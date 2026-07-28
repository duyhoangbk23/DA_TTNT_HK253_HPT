#pragma once

#include <cstdlib>
#include <cstring>

class TdsUartParser {
public:
    void push(char character) {
        if (character == '\r') {
            return;
        }

        if (character == '\n') {
            _line[_length] = '\0';
            _ready = _length > 0;
            _length = 0;
            return;
        }

        if (_length < sizeof(_line) - 1U) {
            _line[_length++] = character;
        }
    }

    void push(const char* text) {
        if (text == nullptr) {
            return;
        }

        while (*text != '\0') {
            push(*text++);
        }
    }

    bool takeReading(int& tds) {
        if (!_ready) {
            return false;
        }

        _ready = false;
        char* valueStart = _line;
        while (*valueStart != '\0' && (*valueStart < '0' || *valueStart > '9')) {
            ++valueStart;
        }

        if (*valueStart == '\0') {
            return false;
        }

        char* valueEnd = nullptr;
        const float value = strtof(valueStart, &valueEnd);
        if (valueEnd == valueStart || value < 0.0f || value > 2000.0f) {
            return false;
        }

        tds = static_cast<int>(value + 0.5f);
        return true;
    }

private:
    char _line[48]{};
    size_t _length = 0;
    bool _ready = false;
};
