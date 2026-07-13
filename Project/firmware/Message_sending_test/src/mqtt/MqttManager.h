#pragma once

#include <Arduino.h>
#include <ArduinoJson.h>
#include <PubSubClient.h>
#include <WiFiClientSecure.h>

class MqttManager {
   public:
    void begin(const char* deviceId, const char* host, uint16_t port, const char* username, const char* password);
    void loop();
    bool isConnected() const;
    bool publishTelemetry(const JsonDocument& document);

   private:
    void connect();

    WiFiClientSecure net_;
    PubSubClient client_;
    String deviceId_;
    String host_;
    uint16_t port_ = 8883;
    String username_;
    String password_;
    unsigned long lastAttemptMs_ = 0;
};
