#include "TelemetryGenerator.h"

#include <esp_system.h>

RandomTelemetryGenerator::RandomTelemetryGenerator() {
    randomSeed(esp_random());
}

TelemetryData RandomTelemetryGenerator::next() {
    TelemetryData data{};
    data.temperature = randomFloat(24.0f, 34.0f);
    data.tds = randomInt(40, 300);
    data.flowRate = randomFloat(0.0f, 3.0f);
    data.filterLife = randomInt(0, 100);
    data.waterLevel = randomInt(0, 100);
    data.pressure = randomFloat(1.0f, 3.0f);
    return data;
}

int RandomTelemetryGenerator::randomInt(int minInclusive, int maxInclusive) {
    return random(minInclusive, maxInclusive + 1);
}

float RandomTelemetryGenerator::randomFloat(float minInclusive, float maxInclusive) {
    const float scale = static_cast<float>(random(0, 10000)) / 10000.0f;
    return minInclusive + (maxInclusive - minInclusive) * scale;
}
