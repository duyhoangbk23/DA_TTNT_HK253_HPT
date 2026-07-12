#include "SimulatorApp.h"

void SimulatorApp::begin() {
    Serial.begin(115200);
    delay(200);
    Logger::info("ESP32 Simulator starting...");

    wifiManager_.begin(SimulatorConfig::WIFI_SSID, SimulatorConfig::WIFI_PASSWORD);
    mqttManager_.setCommandHandler(&SimulatorApp::onCommand);
    mqttManager_.begin(
        deviceInfo_,
        SimulatorConfig::MQTT_HOST,
        SimulatorConfig::MQTT_PORT,
        SimulatorConfig::MQTT_USERNAME,
        SimulatorConfig::MQTT_PASSWORD,
        SimulatorConfig::MQTT_ROOT_CA);
}

void SimulatorApp::loop() {
    wifiManager_.loop();
    mqttManager_.loop();

    const unsigned long now = millis();
    if (now - lastTelemetryMs_ >= SimulatorConfig::TELEMETRY_INTERVAL_MS) {
        lastTelemetryMs_ = now;
        publishTelemetry();
    }

    if (now - lastStatusMs_ >= SimulatorConfig::STATUS_INTERVAL_MS) {
        lastStatusMs_ = now;
        publishStatus();
    }
}

void SimulatorApp::publishTelemetry() {
    if (!mqttManager_.isConnected()) {
        return;
    }

    const TelemetryData data = telemetryGenerator_.next();
    StaticJsonDocument<256> doc;
    doc["deviceId"] = deviceInfo_.deviceId;
    doc["temperature"] = data.temperature;
    doc["tds"] = data.tds;
    doc["flowRate"] = data.flowRate;
    doc["filterLife"] = data.filterLife;
    doc["waterLevel"] = data.waterLevel;
    doc["pressure"] = data.pressure;

    mqttManager_.publishTelemetry(doc);
}

void SimulatorApp::publishStatus() {
    if (!mqttManager_.isConnected()) {
        return;
    }

    StaticJsonDocument<192> doc;
    doc["online"] = true;
    doc["wifi"] = wifiManager_.signalStrength();
    doc["uptime"] = millis() / 1000UL;
    doc["ip"] = wifiManager_.localIp();

    mqttManager_.publishStatus(doc);
}

void SimulatorApp::onCommand(const String& topic, const String& payload) {
    (void)topic;
    (void)payload;
}
