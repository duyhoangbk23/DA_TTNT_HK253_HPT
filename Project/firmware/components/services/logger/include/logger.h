#pragma once

#include <Arduino.h>

class Logger {
public:
    static void begin(uint32_t baudRate = 115200);

    static void info(const char* format, ...);
    static void error(const char* format, ...);
    static void wifi(const char* format, ...);
    static void mqtt(const char* format, ...);

private:
    static void log(const char* level, const char* format, va_list args);
};
