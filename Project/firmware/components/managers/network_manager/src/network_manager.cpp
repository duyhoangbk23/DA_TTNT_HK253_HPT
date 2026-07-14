#include "network_manager.h"

void NetworkManager::begin() {
    _wifiManager.begin();
    _mqttManager.begin();
}

void NetworkManager::update() {
    _wifiManager.update();
    _mqttManager.update();
}

void NetworkManager::loop() {
    _mqttManager.loop();
}

bool NetworkManager::isWifiConnected() const {
    return _wifiManager.isConnected();
}

bool NetworkManager::isMqttConnected() {
    return _mqttManager.isConnected();
}

int NetworkManager::getRSSI() const {
    return _wifiManager.getRSSI();
}

bool NetworkManager::publishTelemetry(const SensorData& telemetry) {
    return _mqttManager.publishTelemetry(telemetry);
}

bool NetworkManager::publishStatus(const SensorData& telemetry, const char* status) {
    return _mqttManager.publishStatus(telemetry, status);
}

void NetworkManager::setCommandCallback(CommandCallback callback) {
    _mqttManager.setCommandCallback(callback);
}
