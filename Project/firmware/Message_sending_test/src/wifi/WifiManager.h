#pragma once

#include <Arduino.h>
#include <WiFi.h>

class WifiManager {
   public:
    void begin(const char* ssid, const char* password);
    void loop();
    bool isConnected() const;
    int32_t signalStrength() const;
    String localIp() const;

   private:
    void connect();

    const char* ssid_ = nullptr;
    const char* password_ = nullptr;
    unsigned long lastAttemptMs_ = 0;
};
