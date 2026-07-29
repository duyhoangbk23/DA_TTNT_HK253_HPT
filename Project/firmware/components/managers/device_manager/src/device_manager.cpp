#include "device_manager.h"

#include <app_config.h>
#include <pin_map.h>

DeviceManager* DeviceManager::_instance = nullptr;

DeviceManager& DeviceManager::instance() {
    static DeviceManager controller;
    return controller;
}

void DeviceManager::begin() {
    // Entry point chỉ ủy quyền cho DeviceManager; toàn bộ khởi tạo phần cứng và task nền nằm trong manager này.
    _instance = this;

    Logger::begin(115200);
    Utils::seedRandom();

    _relay.begin(Config::Pins::RELAY);
    _buzzer.begin(Config::Pins::BUZZER);
    _relay.off();
    _buzzer.off();

    _cacheMutex = xSemaphoreCreateMutex();
    _sensorManager.begin();
    _networkManager.begin();
    _networkManager.setCommandCallback(handleCommand);

    startTasks();
    Logger::info("System boot completed");
}

void DeviceManager::loop() {
    _networkManager.update();
    _networkManager.loop();
    flushTelemetryCache();
    vTaskDelay(pdMS_TO_TICKS(10));
}

void DeviceManager::cacheTelemetry(const SensorData& telemetry) {
    // Khi MQTT mất kết nối, telemetry được giữ trong bộ đệm giới hạn và gửi bù sau khi kết nối phục hồi.
    if (_cacheMutex == nullptr || xSemaphoreTake(_cacheMutex, portMAX_DELAY) != pdTRUE) {
        Logger::error("Cannot lock telemetry cache");
        return;
    }

    if (_cacheSize == Config::Cache::MAX_TELEMETRY_RECORDS) {
        _cacheHead = (_cacheHead + 1U) % Config::Cache::MAX_TELEMETRY_RECORDS;
        --_cacheSize;
        Logger::error("Telemetry cache full; discarded oldest record");
    }

    const size_t tail = (_cacheHead + _cacheSize) % Config::Cache::MAX_TELEMETRY_RECORDS;
    _telemetryCache[tail] = telemetry;
    ++_cacheSize;
    xSemaphoreGive(_cacheMutex);
}

void DeviceManager::flushTelemetryCache() {
    // Khi MQTT mất kết nối, telemetry được giữ trong bộ đệm giới hạn và gửi bù sau khi kết nối phục hồi.
    if (!_networkManager.isMqttConnected()) {
        return;
    }

    if (_cacheMutex == nullptr || xSemaphoreTake(_cacheMutex, portMAX_DELAY) != pdTRUE) {
        return;
    }

    while (_cacheSize > 0U) {
        const SensorData telemetry = _telemetryCache[_cacheHead];
        if (!_networkManager.publishTelemetry(telemetry)) {
            break;
        }

        _cacheHead = (_cacheHead + 1U) % Config::Cache::MAX_TELEMETRY_RECORDS;
        --_cacheSize;
    }

    xSemaphoreGive(_cacheMutex);
}

void DeviceManager::startTasks() {
    // Chu kỳ chính duy trì các tác vụ mạng và xử lý lệnh mà không chặn các FreeRTOS task đọc/gửi telemetry.
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

        SensorData sensorError;
        if (controller->_sensorManager.consumeSensorError(sensorError)) {
            controller->cacheTelemetry(sensorError);
            controller->flushTelemetryCache();
        }

        vTaskDelayUntil(&lastWake, pdMS_TO_TICKS(Config::Timing::SENSOR_READ_MS));
    }
}

void DeviceManager::mqttPublishTask(void* parameter) {
    auto* controller = static_cast<DeviceManager*>(parameter);
    TickType_t lastWake = xTaskGetTickCount();

    for (;;) {
        SensorData telemetry = controller->_sensorManager.getTelemetry();
        telemetry.wifiRssi = controller->_networkManager.getRSSI();
        telemetry.timestamp = millis();
        controller->cacheTelemetry(telemetry);
        controller->flushTelemetryCache();

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
