#include "MqttManager.h"

#include <cstring>

#include "../config/SimulatorConfig.h"
#include "../logger/Logger.h"

MqttManager* MqttManager::instance_ = nullptr;

void MqttManager::begin(const DeviceInfo& deviceInfo, const char* host, uint16_t port, const char* username, const char* password, const char* rootCa) {
    deviceInfo_ = deviceInfo;
    host_ = host;
    port_ = port;
    username_ = username;
    password_ = password;
    rootCa_ = rootCa;
    client_.setClient(net_);
    client_.setServer(host_.c_str(), port_);
    client_.setBufferSize(512);
    client_.setKeepAlive(30);
    client_.setSocketTimeout(10);
    client_.setCallback(&MqttManager::onMessage);
    instance_ = this;
    connect();
}

void MqttManager::loop() {
    if (client_.connected()) {
        client_.loop();
        ensureSubscription();
        return;
    }

    const unsigned long now = millis();
    if (now - lastAttemptMs_ < SimulatorConfig::MQTT_RECONNECT_INTERVAL_MS) {
        return;
    }

    connect();
}

bool MqttManager::isConnected() const {
    return client_.connected();
}

bool MqttManager::publishTelemetry(const JsonDocument& document) {
    if (!client_.connected()) {
        return false;
    }

    char payload[512];
    const size_t length = serializeJson(document, payload, sizeof(payload));
    Logger::info("Publish telemetry...");
    return client_.publish(telemetryTopic().c_str(), reinterpret_cast<const uint8_t*>(payload), length);
}

bool MqttManager::publishStatus(const JsonDocument& document) {
    if (!client_.connected()) {
        return false;
    }

    char payload[256];
    const size_t length = serializeJson(document, payload, sizeof(payload));
    Logger::info("Publish status...");
    return client_.publish(statusTopic().c_str(), reinterpret_cast<const uint8_t*>(payload), length);
}

void MqttManager::setCommandHandler(CommandHandler handler) {
    commandHandler_ = handler;
}

String MqttManager::telemetryTopic() const {
    return String(SimulatorConfig::TOPIC_PREFIX) + "/" + deviceInfo_.deviceId + "/telemetry";
}

String MqttManager::statusTopic() const {
    return String(SimulatorConfig::TOPIC_PREFIX) + "/" + deviceInfo_.deviceId + "/status";
}

String MqttManager::commandTopic() const {
    return String(SimulatorConfig::TOPIC_PREFIX) + "/" + deviceInfo_.deviceId + "/command";
}

void MqttManager::onMessage(char* topic, byte* payload, unsigned int length) {
    if (instance_ == nullptr) {
        return;
    }

    String topicString(topic);
    String payloadString;
    payloadString.reserve(length);
    for (unsigned int i = 0; i < length; ++i) {
        payloadString += static_cast<char>(payload[i]);
    }
    instance_->handleMessage(topicString, payloadString);
}

void MqttManager::handleMessage(const String& topic, const String& payload) {
    Logger::info(String("Receive command from ") + topic + ": " + payload);
    if (commandHandler_ != nullptr) {
        commandHandler_(topic, payload);
    }
}

void MqttManager::connect() {
    lastAttemptMs_ = millis();
    if (WiFi.status() != WL_CONNECTED) {
        return;
    }

    Logger::info("Connecting MQTT...");
    net_.setCACert(rootCa_.c_str());

    const String clientId = deviceInfo_.deviceId + "-" + String(static_cast<uint32_t>(esp_random()), HEX);
    const bool ok = client_.connect(
        clientId.c_str(),
        username_.c_str(),
        password_.c_str());

    if (!ok) {
        Logger::error(String("MQTT connect failed, state=") + client_.state());
        return;
    }

    subscribed_ = false;
    ensureSubscription();
}

void MqttManager::ensureSubscription() {
    if (!client_.connected() || subscribed_) {
        return;
    }

    if (client_.subscribe(commandTopic().c_str())) {
        subscribed_ = true;
    }
}
