#include <Arduino.h>

#include "simulator/SimulatorApp.h"

namespace {
SimulatorApp app;
}

void setup() {
    app.begin();
}

void loop() {
    app.loop();
}
