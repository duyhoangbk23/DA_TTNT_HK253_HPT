#pragma once

#include <Arduino.h>
#include <ArduinoJson.h>

#include <buzzer.h>
#include <logger.h>
#include <network_manager.h>
#include <relay.h>
#include <sensor_manager.h>
#include <utils.h>

class DeviceManager {
public:
    static DeviceManager& instance();

    void begin();
    void loop();

private:
    DeviceManager() = default;

    void startTasks();
    void processCommand(const JsonDocument& document);

    static void sensorTask(void* parameter);
    static void mqttPublishTask(void* parameter);
    static void heartbeatTask(void* parameter);
    static void handleCommand(const JsonDocument& document);

    static DeviceManager* _instance;

    NetworkManager _networkManager;
    SensorManager _sensorManager;
    Relay _relay;
    Buzzer _buzzer;

    bool _tasksStarted = false;
};
