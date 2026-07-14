#include <Arduino.h>

#include <device_manager.h>

void setup() {
    DeviceManager::instance().begin();
}

void loop() {
    DeviceManager::instance().loop();
}
