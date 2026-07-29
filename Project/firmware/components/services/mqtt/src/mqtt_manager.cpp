#include "mqtt_manager.h"

#include <app_config.h>
#include <logger.h>

#include <WiFi.h>
#include <cstring>

MQTTManager* MQTTManager::_instance = nullptr;

MQTTManager::MQTTManager()
    : _client(_secureClient) {
}

void MQTTManager::begin() {
    _instance = this;

    _secureClient.setInsecure();
    _secureConfigured = true;

    _client.setServer(Config::Mqtt::HOST, Config::Mqtt::PORT);
    _client.setCallback(onMessage);
    _client.setBufferSize(Config::Mqtt::MAX_PACKET_SIZE);
    _client.setKeepAlive(60);
    _client.setSocketTimeout(5);

    Logger::info("MQTT manager ready");
}

void MQTTManager::update() {
    if (WiFi.status() != WL_CONNECTED) {
        return;
    }

    if (_client.connected()) {
        return;
    }

    const unsigned long now = millis();
    if ((now - _lastReconnectAttempt) < Config::Mqtt::RECONNECT_INTERVAL_MS) {
        return;
    }

    _lastReconnectAttempt = now;
    reconnect();
}

void MQTTManager::loop() {
    if (_client.connected()) {
        _client.loop();
    }
}

bool MQTTManager::isConnected() {
    return _client.connected();
}

bool MQTTManager::publishTelemetry(const SensorData& telemetry) {
    // JSON telemetry giữ mcu_id dạng chuỗi cùng TDS và trạng thái alert trên topic devices/telemetry.
    if (!_client.connected()) {
        return false;
    }

    JsonDocument document;
    document["mcu_id"] = telemetry.mcuId;
    JsonObject reading = document["telemetry"].to<JsonObject>();
    if (telemetry.tdsAvailable) {
        reading["tds"] = telemetry.tds;
    } else {
        reading["tds"] = nullptr;
    }
    reading["alert"] = telemetry.alert;

    return publishDocument(Config::Topics::TELEMETRY, document, false);
}

bool MQTTManager::publishStatus(const SensorData& telemetry, const char* status) {
    if (!_client.connected()) {
        return false;
    }

    JsonDocument document;
    document["mcu_id"] = telemetry.mcuId;
    document["status"] = status != nullptr ? status : "online";
    document["wifi_connected"] = WiFi.status() == WL_CONNECTED;
    document["mqtt_connected"] = _client.connected();
    document["wifi_rssi"] = telemetry.wifiRssi;
    document["ip_address"] = WiFi.status() == WL_CONNECTED ? WiFi.localIP().toString() : "";
    document["uptime_ms"] = millis();

    return publishDocument(Config::Topics::STATUS, document, true);
}

void MQTTManager::setCommandCallback(CommandCallback callback) {
    _commandCallback = callback;
}

void MQTTManager::onMessage(char* topic, byte* payload, unsigned int length) {
    if (_instance != nullptr) {
        _instance->handleMessage(topic, payload, length);
    }
}

void MQTTManager::handleMessage(char* topic, byte* payload, unsigned int length) {
    if (strcmp(topic, Config::Topics::COMMAND) != 0) {
        return;
    }

    JsonDocument document;
    const DeserializationError error = deserializeJson(document, payload, length);
    if (error) {
        Logger::error("Invalid MQTT command JSON: %s", error.c_str());
        return;
    }

    if (_commandCallback != nullptr) {
        _commandCallback(document);
    }
}

bool MQTTManager::reconnect() {
    // MQTT chỉ kết nối lại sau khi Wi-Fi hoạt động để tránh chặn luồng publish telemetry.
    if (WiFi.status() != WL_CONNECTED) {
        Logger::wifi("Skipping MQTT reconnect because WiFi is down");
        return false;
    }

    const String clientId = buildClientId();
    Logger::mqtt("Connecting as %s", clientId.c_str());

    const bool connected = _client.connect(
        clientId.c_str(),
        Config::Mqtt::USERNAME,
        Config::Mqtt::PASSWORD,
        Config::Topics::STATUS,
        0,
        true,
        "offline"
    );

    if (!connected) {
        Logger::mqtt("Connect failed, state=%d", _client.state());
        return false;
    }

    _client.subscribe(Config::Topics::COMMAND);
    SensorData telemetry;
    telemetry.mcuId = Config::Device::MCU_ID;
    telemetry.timestamp = millis();
    publishStatus(telemetry, "online");
    Logger::mqtt("Connected and subscribed to command topic");
    return true;
}

bool MQTTManager::publishDocument(const char* topic, JsonDocument& document, bool retained) {
    // Payload được tuần tự hóa ngay trước khi publish lên topic đã cấu hình.
    char buffer[512];
    const size_t length = serializeJson(document, buffer, sizeof(buffer));
    if (length == 0U) {
        Logger::error("Failed to serialize JSON for topic %s", topic);
        return false;
    }

    const bool published = _client.publish(topic, reinterpret_cast<const uint8_t*>(buffer), length, retained);
    if (!published) {
        Logger::mqtt("Publish failed on topic %s", topic);
    }

    return published;
}

String MQTTManager::buildClientId() const {
    const uint64_t chipId = ESP.getEfuseMac();
    char buffer[48];
    snprintf(buffer, sizeof(buffer), "%s-%04X", Config::Device::MCU_ID, static_cast<uint16_t>(chipId & 0xFFFF));
    return String(buffer);
}
