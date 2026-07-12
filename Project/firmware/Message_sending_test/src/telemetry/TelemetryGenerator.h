#pragma once

#include <Arduino.h>

struct TelemetryData {
    float temperature;
    int tds;
    float flowRate;
    int filterLife;
    int waterLevel;
    float pressure;
};

class ITelemetryGenerator {
   public:
    virtual ~ITelemetryGenerator() = default;
    virtual TelemetryData next() = 0;
};

class RandomTelemetryGenerator final : public ITelemetryGenerator {
   public:
    RandomTelemetryGenerator();
    TelemetryData next() override;

   private:
    int randomInt(int minInclusive, int maxInclusive);
    float randomFloat(float minInclusive, float maxInclusive);
};
