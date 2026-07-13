#include "SimulatorApp.h"

#include <WiFi.h>

void SimulatorApp::begin() {
    Serial.begin(115200);
    delay(200);

    randomSeed(micros());

    wifiManager_.begin(SimulatorConfig::WIFI_SSID, SimulatorConfig::WIFI_PASSWORD);
    mqttManager_.begin(
        SimulatorConfig::DEVICE_ID,
        SimulatorConfig::MQTT_HOST,
        SimulatorConfig::MQTT_PORT,
        SimulatorConfig::MQTT_USERNAME,
        SimulatorConfig::MQTT_PASSWORD);
}

void SimulatorApp::loop() {
    wifiManager_.loop();
    mqttManager_.loop();

    const unsigned long now = millis();
    if (now - lastTelemetryMs_ >= SimulatorConfig::TELEMETRY_INTERVAL_MS) {
        lastTelemetryMs_ = now;
        publishTelemetry();
    }
}

void SimulatorApp::publishTelemetry() {
    if (!mqttManager_.isConnected()) {
        return;
    }

    StaticJsonDocument<256> doc;
    doc["deviceId"] = SimulatorConfig::DEVICE_ID;
    doc["uptime"] = millis() / 1000UL;
    doc["wifi"] = WiFi.RSSI();
    doc["temperature"] = random(240, 340) / 10.0f;
    doc["tds"] = random(40, 301);
    doc["flowRate"] = random(0, 300) / 100.0f;
    doc["filterLife"] = random(0, 101);
    doc["waterLevel"] = random(0, 101);
    doc["pressure"] = random(10, 31) / 10.0f;

    mqttManager_.publishTelemetry(doc);
}
