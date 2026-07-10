#pragma once

#include <Arduino.h>
#include <WiFi.h>

class WifiManager {
public:
    void begin();
    void update();

    bool isConnected() const;
    int getRSSI() const;
    String getLocalIP() const;
    String getSSID() const;

private:
    void startConnection();

    unsigned long _lastReconnectAttempt = 0;
    bool _connectedLogged = false;
};
