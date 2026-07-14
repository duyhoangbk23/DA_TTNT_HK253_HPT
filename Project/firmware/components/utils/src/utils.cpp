#include "utils.h"

#include <esp_system.h>

namespace Utils {
void seedRandom() {
    randomSeed(esp_random());
}

float randomFloat(float minValue, float maxValue) {
    if (maxValue <= minValue) {
        return minValue;
    }

    const float scale = static_cast<float>(random(0, 10000)) / 10000.0f;
    return minValue + ((maxValue - minValue) * scale);
}

int randomInt(int minValue, int maxValue) {
    if (maxValue <= minValue) {
        return minValue;
    }

    return random(minValue, maxValue + 1);
}

float mapFloat(float value, float inMin, float inMax, float outMin, float outMax) {
    if (inMax <= inMin) {
        return outMin;
    }

    const float normalized = (value - inMin) / (inMax - inMin);
    return outMin + (normalized * (outMax - outMin));
}

float clampFloat(float value, float minValue, float maxValue) {
    if (value < minValue) {
        return minValue;
    }

    if (value > maxValue) {
        return maxValue;
    }

    return value;
}
}
