# Firmware main schema

## Pin map (ESP32 DevKitC / ESP32-WROOM-32)

| Component | ESP32 pin | Wiring |
| --- | --- | --- |
| TDS sensor TX | GPIO16 (`RX2`) | Connect to sensor TX; receives UART data. |
| TDS sensor RX | GPIO17 (`TX2`) | Connect to sensor RX; reserved for sensor requests/configuration. |
| TDS sensor GND | GND | Common ground is required. |
| TDS sensor VCC | 3.3V or sensor-rated supply | The TX signal into GPIO16 must be 3.3V logic. |
| Pressure sensor | GPIO35 | ADC input. |
| Flow sensor | GPIO32 | Pulse input. |
| Relay | GPIO13 | Digital output. |
| Buzzer | GPIO14 | Digital output. |

`Serial` on USB remains for logs at 115200 baud. TDS uses `Serial2` at 9600 baud, 8N1, so it does not conflict with flashing or logging.

## TDS UART input

The sensor must emit one ASCII reading per line, for example `TDS: 245.7 ppm\r\n` or `245\n`. The firmware accepts values from 0 to 2000 ppm and rounds decimal readings to the nearest integer. No valid frame for 10 seconds marks the sensor disconnected.

## Wi-Fi and MQTT contract

Wi-Fi and HiveMQ credentials follow `Message_sending_test`. The main firmware publishes to the same topic, `devices/telemetry`, every five minutes. `Config::Device::MCU_ID` is emitted as the string field `mcu_id`. Its payload keeps the simulator's `mcu_id` and nested `telemetry.tds` contract:

```json
{
  "mcu_id": "ESP32_001",
  "telemetry": {
    "tds": 246,
    "alert": "normal"
  }
}
```

When UART input is unavailable, it publishes an error payload with `"tds": null` and `"alert": "sensor_disconnected"` as soon as MQTT is available.

## Connection and cache behaviour

- Wi-Fi reconnects every 10 seconds; MQTT reconnects every 5 seconds and re-subscribes to `devices/command/ESP32_001`.
- Every scheduled telemetry sample is first placed in a FIFO RAM cache of 24 records (up to two hours at the five-minute interval).
- When MQTT reconnects, cached records are sent oldest-first. A failed publish remains queued; if the cache fills, the oldest record is discarded and logged.
- The cache is RAM-only and is cleared by reboot or power loss.
