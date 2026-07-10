#include "device_manager.h"

#include <app_config.h>
#include <pin_map.h>

DeviceManager* DeviceManager::_instance = nullptr;

DeviceManager& DeviceManager::instance() {
    static DeviceManager controller;
    return controller;
}

void DeviceManager::begin() {
    _instance = this;

    Logger::begin(115200);
    Utils::seedRandom();

    _relay.begin(Config::Pins::RELAY);
    _buzzer.begin(Config::Pins::BUZZER);
    _relay.off();
    _buzzer.off();

    _sensorManager.begin();
    _networkManager.begin();
    _networkManager.setCommandCallback(handleCommand);

    startTasks();
    Logger::info("System boot completed");
}

void DeviceManager::loop() {
    _networkManager.update();
    _networkManager.loop();
    vTaskDelay(pdMS_TO_TICKS(10));
}

void DeviceManager::startTasks() {
    if (_tasksStarted) {
        return;
    }

    _tasksStarted = true;

    xTaskCreatePinnedToCore(sensorTask, "SensorTask", 4096, this, 2, nullptr, 1);
    xTaskCreatePinnedToCore(mqttPublishTask, "MqttTask", 6144, this, 2, nullptr, 1);
    xTaskCreatePinnedToCore(heartbeatTask, "HeartbeatTask", 4096, this, 1, nullptr, 1);
}

void DeviceManager::processCommand(const JsonDocument& document) {
    if (!document["relay"].isNull()) {
        const int relayState = document["relay"] | 0;
        _relay.set(relayState != 0);
        Logger::mqtt("Relay command applied: %d", relayState);
    }

    if (!document["buzzer"].isNull()) {
        const int buzzerState = document["buzzer"] | 0;
        _buzzer.set(buzzerState != 0);
        Logger::mqtt("Buzzer command applied: %d", buzzerState);
    }
}

void DeviceManager::handleCommand(const JsonDocument& document) {
    if (_instance != nullptr) {
        _instance->processCommand(document);
    }
}

void DeviceManager::sensorTask(void* parameter) {
    auto* controller = static_cast<DeviceManager*>(parameter);
    TickType_t lastWake = xTaskGetTickCount();

    for (;;) {
        controller->_sensorManager.update(controller->_networkManager.getRSSI());
        vTaskDelayUntil(&lastWake, pdMS_TO_TICKS(Config::Timing::SENSOR_READ_MS));
    }
}

void DeviceManager::mqttPublishTask(void* parameter) {
    auto* controller = static_cast<DeviceManager*>(parameter);
    TickType_t lastWake = xTaskGetTickCount();

    for (;;) {
        if (controller->_networkManager.isWifiConnected() && controller->_networkManager.isMqttConnected()) {
            SensorData telemetry = controller->_sensorManager.getTelemetry();
            telemetry.wifiRssi = controller->_networkManager.getRSSI();
            telemetry.timestamp = millis();
            controller->_networkManager.publishTelemetry(telemetry);
        }

        vTaskDelayUntil(&lastWake, pdMS_TO_TICKS(Config::Timing::MQTT_PUBLISH_MS));
    }
}

void DeviceManager::heartbeatTask(void* parameter) {
    auto* controller = static_cast<DeviceManager*>(parameter);
    TickType_t lastWake = xTaskGetTickCount();

    for (;;) {
        SensorData telemetry = controller->_sensorManager.getTelemetry();
        telemetry.wifiRssi = controller->_networkManager.getRSSI();
        telemetry.timestamp = millis();

        const char* status = controller->_networkManager.isWifiConnected() ? "online" : "wifi-down";
        if (controller->_networkManager.isMqttConnected()) {
            controller->_networkManager.publishStatus(telemetry, status);
        }

        vTaskDelayUntil(&lastWake, pdMS_TO_TICKS(Config::Timing::HEARTBEAT_MS));
    }
}
