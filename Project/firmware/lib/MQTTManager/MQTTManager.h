#pragma once

#include <Arduino.h>
#include <ArduinoJson.h>
#include <PubSubClient.h>
#include <WiFiClientSecure.h>

#include <TelemetryData.h>

class MQTTManager {
public:
    using CommandCallback = void (*)(const JsonDocument& document);

    MQTTManager();

    void begin();
    void update();
    void loop();

    bool isConnected();
    bool publishTelemetry(const TelemetryData& telemetry);
    bool publishStatus(const TelemetryData& telemetry, const char* status = "online");
    void setCommandCallback(CommandCallback callback);

private:
    static MQTTManager* _instance;
    static void onMessage(char* topic, byte* payload, unsigned int length);

    void handleMessage(char* topic, byte* payload, unsigned int length);
    bool reconnect();
    bool publishDocument(const char* topic, JsonDocument& document, bool retained = false);
    String buildClientId() const;

    WiFiClientSecure _secureClient;
    PubSubClient _client;
    CommandCallback _commandCallback = nullptr;
    unsigned long _lastReconnectAttempt = 0;
    bool _secureConfigured = false;
};
