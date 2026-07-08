#pragma once

#include <Arduino.h>

namespace Utils {
void seedRandom();
float randomFloat(float minValue, float maxValue);
int randomInt(int minValue, int maxValue);
float mapFloat(float value, float inMin, float inMax, float outMin, float outMax);
float clampFloat(float value, float minValue, float maxValue);
}

