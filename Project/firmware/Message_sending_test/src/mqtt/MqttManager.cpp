#include "MqttManager.h"

#include "../config/SimulatorConfig.h"
#include <WiFi.h>

void MqttManager::begin(const char* deviceId, const char* host, uint16_t port, const char* username, const char* password) {
    deviceId_ = deviceId;
    host_ = host;
    port_ = port;
    username_ = username;
    password_ = password;

    client_.setClient(net_);
    client_.setServer(host_.c_str(), port_);
    client_.setBufferSize(512);
    client_.setKeepAlive(30);
    client_.setSocketTimeout(10);

    connect();
}

void MqttManager::loop() {
    if (client_.connected()) {
        client_.loop();
        return;
    }

    const unsigned long now = millis();
    if (now - lastAttemptMs_ < SimulatorConfig::MQTT_RECONNECT_INTERVAL_MS) {
        return;
    }

    connect();
}

bool MqttManager::isConnected() {
    return client_.connected();
}

bool MqttManager::publishTelemetry(const JsonDocument& document) {
    /* Mỗi chu kỳ tạo một mẫu telemetry, gắn mcu_id dạng chuỗi rồi publish lên topic telemetry đã cấu hình. */
    if (!client_.connected()) {
        return false;
    }

    char payload[256];
    const size_t length = serializeJson(document, payload, sizeof(payload));
    const String topic = String(SimulatorConfig::TOPIC_PREFIX)+ "/telemetry";
    return client_.publish(topic.c_str(), reinterpret_cast<const uint8_t*>(payload), length);
}

void MqttManager::connect() {
    /* MQTT chỉ thử kết nối lại khi Wi-Fi đã kết nối, đồng thời ghi nhận thời điểm thử để áp dụng khoảng reconnect. */
    lastAttemptMs_ = millis();
    if (WiFi.status() != WL_CONNECTED) {
        return;
    }

    net_.setInsecure();

    const String clientId = deviceId_ + "-" + String(random(0x7fffffff), HEX);
    client_.connect(clientId.c_str(), username_.c_str(), password_.c_str());
}
