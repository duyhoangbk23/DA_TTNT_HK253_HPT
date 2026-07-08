#include <Arduino.h>

#include <AppController.h>

void setup() {
    AppController::instance().begin();
}

void loop() {
    AppController::instance().loop();
}

