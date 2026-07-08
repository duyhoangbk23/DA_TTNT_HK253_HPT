#include "AppController.h"

#include <Config.h>
#include <Logger.h>
#include <TelemetryData.h>

AppController* AppController::_instance = nullptr;

AppController& AppController::instance() {
    static AppController controller;
    return controller;
}

void AppController::begin() {
    _instance = this;

    Logger::begin(115200);
    Utils::seedRandom();

    pinMode(Config::Pins::RELAY, OUTPUT);
    pinMode(Config::Pins::BUZZER, OUTPUT);
    digitalWrite(Config::Pins::RELAY, LOW);
    digitalWrite(Config::Pins::BUZZER, LOW);

    _sensorManager.begin();
    _wifiManager.begin();
    _mqttManager.begin();
    _mqttManager.setCommandCallback(handleCommand);

    startTasks();
    Logger::info("System boot completed");
}

void AppController::loop() {
    _wifiManager.update();
    _mqttManager.update();
    _mqttManager.loop();
    vTaskDelay(pdMS_TO_TICKS(10));
}

void AppController::startTasks() {
    if (_tasksStarted) {
        return;
    }

    _tasksStarted = true;

    xTaskCreatePinnedToCore(sensorTask, "SensorTask", 4096, this, 2, nullptr, 1);
    xTaskCreatePinnedToCore(mqttPublishTask, "MqttTask", 6144, this, 2, nullptr, 1);
    xTaskCreatePinnedToCore(heartbeatTask, "HeartbeatTask", 4096, this, 1, nullptr, 1);
}

void AppController::processCommand(const JsonDocument& document) {
    if (!document["relay"].isNull()) {
        const int relayState = document["relay"] | 0;
        digitalWrite(Config::Pins::RELAY, relayState ? HIGH : LOW);
        Logger::mqtt("Relay command applied: %d", relayState);
    }

    if (!document["buzzer"].isNull()) {
        const int buzzerState = document["buzzer"] | 0;
        digitalWrite(Config::Pins::BUZZER, buzzerState ? HIGH : LOW);
        Logger::mqtt("Buzzer command applied: %d", buzzerState);
    }
}

void AppController::handleCommand(const JsonDocument& document) {
    if (_instance != nullptr) {
        _instance->processCommand(document);
    }
}

void AppController::sensorTask(void* parameter) {
    auto* controller = static_cast<AppController*>(parameter);
    TickType_t lastWake = xTaskGetTickCount();

    for (;;) {
        controller->_sensorManager.update(controller->_wifiManager.getRSSI());
        vTaskDelayUntil(&lastWake, pdMS_TO_TICKS(Config::Timing::SENSOR_READ_MS));
    }
}

void AppController::mqttPublishTask(void* parameter) {
    auto* controller = static_cast<AppController*>(parameter);
    TickType_t lastWake = xTaskGetTickCount();

    for (;;) {
        if (controller->_wifiManager.isConnected() && controller->_mqttManager.isConnected()) {
            TelemetryData telemetry = controller->_sensorManager.getTelemetry();
            telemetry.wifiRssi = controller->_wifiManager.getRSSI();
            telemetry.timestamp = millis();
            controller->_mqttManager.publishTelemetry(telemetry);
        }

        vTaskDelayUntil(&lastWake, pdMS_TO_TICKS(Config::Timing::MQTT_PUBLISH_MS));
    }
}

void AppController::heartbeatTask(void* parameter) {
    auto* controller = static_cast<AppController*>(parameter);
    TickType_t lastWake = xTaskGetTickCount();

    for (;;) {
        TelemetryData telemetry = controller->_sensorManager.getTelemetry();
        telemetry.wifiRssi = controller->_wifiManager.getRSSI();
        telemetry.timestamp = millis();

        const char* status = controller->_wifiManager.isConnected() ? "online" : "wifi-down";
        if (controller->_mqttManager.isConnected()) {
            controller->_mqttManager.publishStatus(telemetry, status);
        }

        vTaskDelayUntil(&lastWake, pdMS_TO_TICKS(Config::Timing::HEARTBEAT_MS));
    }
}
