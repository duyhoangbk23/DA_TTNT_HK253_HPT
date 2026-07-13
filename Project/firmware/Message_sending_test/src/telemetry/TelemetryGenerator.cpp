#include "TelemetryGenerator.h"

#include <esp_system.h>

RandomTelemetryGenerator::RandomTelemetryGenerator() {
    randomSeed(esp_random());
}

TelemetryData RandomTelemetryGenerator::next() {
    TelemetryData data{};
    data.tds = randomInt(40, 300);
    data.alert=randomInt(0, 1); 
    return data;
}

int RandomTelemetryGenerator::randomInt(int minInclusive, int maxInclusive) {
    return random(minInclusive, maxInclusive + 1);
}

float RandomTelemetryGenerator::randomFloat(float minInclusive, float maxInclusive) {
    const float scale = static_cast<float>(random(0, 10000)) / 10000.0f;
    return minInclusive + (maxInclusive - minInclusive) * scale;
}
