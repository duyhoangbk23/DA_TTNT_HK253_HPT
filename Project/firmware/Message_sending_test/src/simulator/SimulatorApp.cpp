#include "SimulatorApp.h"

#include <WiFi.h>

void SimulatorApp::begin() {
    /* Simulator giữ cùng hợp đồng payload với firmware thật để backend xử lý hai nguồn theo một luồng duy nhất. */
    Serial.begin(115200);
    delay(200);

    randomSeed(micros());

    wifiManager_.begin(SimulatorConfig::WIFI_SSID, SimulatorConfig::WIFI_PASSWORD);
    mqttManager_.begin(
        SimulatorConfig::MCU_ID,
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
        /* Mỗi chu kỳ tạo một mẫu telemetry, gắn mcu_id dạng chuỗi rồi publish lên topic telemetry đã cấu hình. */
        lastTelemetryMs_ = now;
        publishTelemetry();
    }
}

void SimulatorApp::publishTelemetry() {
    if (!mqttManager_.isConnected()) {
        return;
    }

    /* Simulator giữ cùng hợp đồng payload với firmware thật để backend xử lý hai nguồn theo một luồng duy nhất. */
    StaticJsonDocument<256> doc;
    const int tds = random(40, 301);
    doc["mcu_id"] = SimulatorConfig::MCU_ID;
    JsonObject telemetry = doc["telemetry"].to<JsonObject>();
    telemetry["tds"] = tds;
    /* Mỗi chu kỳ tạo một mẫu telemetry, gắn mcu_id dạng chuỗi rồi publish lên topic telemetry đã cấu hình. */
    mqttManager_.publishTelemetry(doc);
}
