#pragma once

#include <Arduino.h>
#include <ArduinoJson.h>
#include <PubSubClient.h>
#include <WiFiClientSecure.h>

#include "../device/DeviceInfo.h"

class MqttManager {
   public:
    using CommandHandler = void (*)(const String& topic, const String& payload);

    void begin(const DeviceInfo& deviceInfo, const char* host, uint16_t port, const char* username, const char* password, const char* rootCa);
    void loop();
    bool isConnected() const;
    bool publishTelemetry(const JsonDocument& document);
    bool publishStatus(const JsonDocument& document);
    void setCommandHandler(CommandHandler handler);
    String telemetryTopic() const;
    String statusTopic() const;
    String commandTopic() const;

   private:
    static void onMessage(char* topic, byte* payload, unsigned int length);
    void handleMessage(const String& topic, const String& payload);
    void connect();
    void ensureSubscription();

    static MqttManager* instance_;

    WiFiClientSecure net_;
    PubSubClient client_;
    DeviceInfo deviceInfo_{};
    String host_;
    uint16_t port_ = 8883;
    String username_;
    String password_;
    String rootCa_;
    CommandHandler commandHandler_ = nullptr;
    bool subscribed_ = false;
    unsigned long lastAttemptMs_ = 0;
};
