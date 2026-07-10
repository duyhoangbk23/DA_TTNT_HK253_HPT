#pragma once

#include <Arduino.h>

namespace Config {
namespace Device {
constexpr const char* DEVICE_ID = "device001";
}

namespace Wifi {
constexpr const char* SSID = "YOUR_WIFI_SSID";
constexpr const char* PASSWORD = "YOUR_WIFI_PASSWORD";
constexpr uint32_t RECONNECT_INTERVAL_MS = 10000UL;
}

namespace Mqtt {
constexpr const char* HOST = "2c8c328c4b19442bad0871e5d015b0a9.s1.eu.hivemq.cloud";
constexpr uint16_t PORT = 8883;
constexpr const char* USERNAME = "DD04122005";
constexpr const char* PASSWORD = "Hoangduy0412";
constexpr uint32_t RECONNECT_INTERVAL_MS = 5000UL;
constexpr uint16_t MAX_PACKET_SIZE = 1024;
}

namespace Topics {
constexpr const char* TELEMETRY = "waterpurifier/device001/telemetry";
constexpr const char* STATUS = "waterpurifier/device001/status";
constexpr const char* COMMAND = "waterpurifier/device001/command";
}

namespace Sensor {
constexpr bool ENABLE_MOCK_DATA = true;
constexpr float FLOW_PULSES_PER_LITER = 450.0f;
constexpr uint16_t ADC_MAX = 4095;
constexpr float ADC_REF_VOLTAGE = 3.3f;
}

namespace Timing {
constexpr uint32_t SENSOR_READ_MS = 1000UL;
constexpr uint32_t MQTT_PUBLISH_MS = 5000UL;
constexpr uint32_t HEARTBEAT_MS = 30000UL;
}
}
