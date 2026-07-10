#include "wifi_manager.h"

#include <app_config.h>
#include <logger.h>

void WifiManager::begin() {
    WiFi.mode(WIFI_STA);
    WiFi.setAutoReconnect(true);
    WiFi.persistent(false);
    startConnection();
}

void WifiManager::update() {
    if (WiFi.status() == WL_CONNECTED) {
        if (!_connectedLogged) {
            _connectedLogged = true;
            Logger::wifi("Connected to %s, IP: %s", WiFi.SSID().c_str(), WiFi.localIP().toString().c_str());
        }
        return;
    }

    _connectedLogged = false;

    const unsigned long now = millis();
    if ((now - _lastReconnectAttempt) < Config::Wifi::RECONNECT_INTERVAL_MS) {
        return;
    }

    startConnection();
}

bool WifiManager::isConnected() const {
    return WiFi.status() == WL_CONNECTED;
}

int WifiManager::getRSSI() const {
    return isConnected() ? WiFi.RSSI() : 0;
}

String WifiManager::getLocalIP() const {
    return isConnected() ? WiFi.localIP().toString() : String("");
}

String WifiManager::getSSID() const {
    return isConnected() ? WiFi.SSID() : String("");
}

void WifiManager::startConnection() {
    _lastReconnectAttempt = millis();

    Logger::wifi("Connecting to SSID: %s", Config::Wifi::SSID);
    WiFi.disconnect(false, false);
    WiFi.begin(Config::Wifi::SSID, Config::Wifi::PASSWORD);
}
