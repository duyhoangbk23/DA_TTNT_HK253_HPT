#include "Logger.h"

#include <cstdarg>
#include <cstdio>

void Logger::begin(uint32_t baudRate) {
    Serial.begin(baudRate);
}

void Logger::info(const char* format, ...) {
    va_list args;
    va_start(args, format);
    log("INFO", format, args);
    va_end(args);
}

void Logger::error(const char* format, ...) {
    va_list args;
    va_start(args, format);
    log("ERROR", format, args);
    va_end(args);
}

void Logger::wifi(const char* format, ...) {
    va_list args;
    va_start(args, format);
    log("WIFI", format, args);
    va_end(args);
}

void Logger::mqtt(const char* format, ...) {
    va_list args;
    va_start(args, format);
    log("MQTT", format, args);
    va_end(args);
}

void Logger::log(const char* level, const char* format, va_list args) {
    char buffer[256];
    vsnprintf(buffer, sizeof(buffer), format, args);
    Serial.printf("[%s] %s\r\n", level, buffer);
}
