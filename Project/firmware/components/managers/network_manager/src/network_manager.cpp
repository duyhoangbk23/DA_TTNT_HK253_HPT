#include "network_manager.h"

void NetworkManager::begin() {
    _mqttMutex = xSemaphoreCreateMutex();
    _wifiManager.begin();
    _mqttManager.begin();
}

void NetworkManager::update() {
    _wifiManager.update();
    if (lockMqtt()) {
        _mqttManager.update();
        unlockMqtt();
    }
}

void NetworkManager::loop() {
    if (lockMqtt()) {
        _mqttManager.loop();
        unlockMqtt();
    }
}

bool NetworkManager::isWifiConnected() const {
    return _wifiManager.isConnected();
}

bool NetworkManager::isMqttConnected() {
    if (!lockMqtt()) {
        return false;
    }

    const bool connected = _mqttManager.isConnected();
    unlockMqtt();
    return connected;
}

int NetworkManager::getRSSI() const {
    return _wifiManager.getRSSI();
}

bool NetworkManager::publishTelemetry(const SensorData& telemetry) {
    if (!lockMqtt()) {
        return false;
    }

    const bool published = _mqttManager.publishTelemetry(telemetry);
    unlockMqtt();
    return published;
}

bool NetworkManager::publishStatus(const SensorData& telemetry, const char* status) {
    if (!lockMqtt()) {
        return false;
    }

    const bool published = _mqttManager.publishStatus(telemetry, status);
    unlockMqtt();
    return published;
}

void NetworkManager::setCommandCallback(CommandCallback callback) {
    if (lockMqtt()) {
        _mqttManager.setCommandCallback(callback);
        unlockMqtt();
    }
}

bool NetworkManager::lockMqtt() {
    return _mqttMutex != nullptr && xSemaphoreTake(_mqttMutex, portMAX_DELAY) == pdTRUE;
}

void NetworkManager::unlockMqtt() {
    xSemaphoreGive(_mqttMutex);
}
