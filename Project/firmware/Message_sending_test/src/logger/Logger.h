#pragma once

#include <Arduino.h>

namespace Logger {

inline void info(const char* message) {
    Serial.println(message);
}

inline void info(const String& message) {
    Serial.println(message);
}

inline void info(const __FlashStringHelper* message) {
    Serial.println(message);
}

inline void warn(const char* message) {
    Serial.println(message);
}

inline void warn(const String& message) {
    Serial.println(message);
}

inline void error(const char* message) {
    Serial.println(message);
}

inline void error(const String& message) {
    Serial.println(message);
}

}  // namespace Logger
