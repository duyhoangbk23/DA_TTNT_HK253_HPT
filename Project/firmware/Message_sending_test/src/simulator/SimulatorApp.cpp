#include "SimulatorApp.h"

#include <WiFi.h>

void SimulatorApp::begin() {
    /* Simulator dùng cùng envelope và topic telemetry như firmware thật cho hai trường hiện có: mcu_id và telemetry.tds. */
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

    /* Document mô phỏng chỉ chứa mcu_id dạng chuỗi và telemetry.tds; telemetry.alert chưa được tạo ở đường chạy này. */
    StaticJsonDocument<256> doc;
    const int tds = random(40, 301);
    doc["mcu_id"] = SimulatorConfig::MCU_ID;
    JsonObject telemetry = doc["telemetry"].to<JsonObject>();
    telemetry["tds"] = tds;
    /* Mỗi chu kỳ tạo một mẫu telemetry, gắn mcu_id dạng chuỗi rồi publish lên topic telemetry đã cấu hình. */
    mqttManager_.publishTelemetry(doc);
}
