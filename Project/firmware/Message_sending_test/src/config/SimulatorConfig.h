#pragma once

#include <Arduino.h>

namespace SimulatorConfig {

inline constexpr const char* WIFI_SSID = "your-wifi-ssid";
inline constexpr const char* WIFI_PASSWORD = "your-wifi-password";

inline constexpr const char* MQTT_HOST = "your-hivemq-host.s1.eu.hivemq.cloud";
inline constexpr uint16_t MQTT_PORT = 8883;
inline constexpr const char* MQTT_USERNAME = "your-mqtt-username";
inline constexpr const char* MQTT_PASSWORD = "your-mqtt-password";

inline constexpr const char* MQTT_ROOT_CA = R"EOF(
-----BEGIN CERTIFICATE-----
MIIF...REPLACE_WITH_HIVEMQ_CA...IDAQAB
-----END CERTIFICATE-----
)EOF";

inline constexpr const char* DEVICE_ID = "sim-esp32-001";
inline constexpr const char* SERIAL_NUMBER = "SIM-ESP32-0001";
inline constexpr const char* FIRMWARE_VERSION = "sim-1.0.0";
inline constexpr const char* MODEL = "ESP32-Simulator";

inline constexpr const char* TOPIC_PREFIX = "devices";
inline constexpr uint32_t TELEMETRY_INTERVAL_MS = 10000;
inline constexpr uint32_t STATUS_INTERVAL_MS = 30000;
inline constexpr uint32_t WIFI_RECONNECT_INTERVAL_MS = 5000;
inline constexpr uint32_t MQTT_RECONNECT_INTERVAL_MS = 5000;

}  // namespace SimulatorConfig
