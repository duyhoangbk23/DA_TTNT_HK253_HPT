#pragma once

#include <Arduino.h>

#include <pin_map.h>
#include <tds_uart_parser.h>

class TdsSensor {
public:
    void begin(uint8_t rxPin = Config::Pins::TDS_RX, uint8_t txPin = Config::Pins::TDS_TX);
    bool read(int& tds);
    bool isConnected() const;

private:
    uint8_t _rxPin = Config::Pins::TDS_RX;
    uint8_t _txPin = Config::Pins::TDS_TX;
    unsigned long _lastReadingMs = 0;
    int _lastTds = 0;
    bool _hasReading = false;
    TdsUartParser _parser;
};
