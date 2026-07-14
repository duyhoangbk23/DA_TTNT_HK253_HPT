#pragma once

#include <Arduino.h>

namespace SimulatorConfig {

inline constexpr const char* WIFI_SSID = "DD04122005";
inline constexpr const char* WIFI_PASSWORD = "04122005";

inline constexpr const char* MQTT_HOST = "2c8c328c4b19442bad0871e5d015b0a9.s1.eu.hivemq.cloud";
inline constexpr uint16_t MQTT_PORT = 8883;
inline constexpr const char* MQTT_USERNAME = "DD04122005";
inline constexpr const char* MQTT_PASSWORD = "Hoangduy0412";


inline constexpr const char* MQTT_ROOT_CA = R"EOF(
-----BEGIN CERTIFICATE-----
MIIF...REPLACE_WITH_HIVEMQ_CA...IDAQAB
-----END CERTIFICATE-----
)EOF";

inline constexpr const char* DEVICE_ID = "ESP32-0001";
inline constexpr const char* SERIAL_NUMBER = "SN-0001";
inline constexpr const char* FIRMWARE_VERSION = "v1.1";
inline constexpr const char* MODEL = "ESP32 NodeMCU 32S";

inline constexpr const char* TOPIC_PREFIX = "devices";
inline constexpr uint32_t TELEMETRY_INTERVAL_MS = 10000;
inline constexpr uint32_t STATUS_INTERVAL_MS = 30000;
inline constexpr uint32_t WIFI_RECONNECT_INTERVAL_MS = 5000;
inline constexpr uint32_t MQTT_RECONNECT_INTERVAL_MS = 5000;

}  // namespace SimulatorConfig
