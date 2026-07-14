#pragma once

#include <Arduino.h>

struct DeviceInfo {
    String deviceId;
    String serialNumber;
    String firmwareVersion;
    String model;
};
