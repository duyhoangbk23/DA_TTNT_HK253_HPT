#include "WifiManager.h"

#include "../config/SimulatorConfig.h"

void WifiManager::begin(const char* ssid, const char* password) {
    ssid_ = ssid;
    password_ = password;
    WiFi.mode(WIFI_STA);
    WiFi.setAutoReconnect(true);
    connect();
}

void WifiManager::loop() {
    if (WiFi.status() == WL_CONNECTED) {
        return;
    }

    const unsigned long now = millis();
    if (now - lastAttemptMs_ < SimulatorConfig::WIFI_RECONNECT_INTERVAL_MS) {
        return;
    }

    connect();
}

bool WifiManager::isConnected() const {
    return WiFi.status() == WL_CONNECTED;
}

String WifiManager::localIp() const {
    return isConnected() ? WiFi.localIP().toString() : String("0.0.0.0");
}

void WifiManager::connect() {
    lastAttemptMs_ = millis();
    WiFi.disconnect(true);
    WiFi.begin(ssid_, password_);
}
