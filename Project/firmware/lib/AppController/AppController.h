#pragma once

#include <Arduino.h>
#include <ArduinoJson.h>

#include <TelemetryData.h>
#include <Utils.h>
#include <MQTTManager.h>
#include <SensorManager.h>
#include <WifiManager.h>

class AppController {
public:
    static AppController& instance();

    void begin();
    void loop();

private:
    AppController() = default;

    void startTasks();
    void processCommand(const JsonDocument& document);

    static void sensorTask(void* parameter);
    static void mqttPublishTask(void* parameter);
    static void heartbeatTask(void* parameter);
    static void handleCommand(const JsonDocument& document);

    static AppController* _instance;

    WifiManager _wifiManager;
    MQTTManager _mqttManager;
    SensorManager _sensorManager;

    bool _tasksStarted = false;
};
