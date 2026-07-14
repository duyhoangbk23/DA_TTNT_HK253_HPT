# ESP32 Simulator

Module giả lập gửi telemetry lên HiveMQ Cloud.

## Mục tiêu
- Kết nối WiFi
- Kết nối MQTT qua TLS
- Publish telemetry định kỳ
- Publish trạng thái thiết bị
- Subscribe command và chỉ log ra Serial

## Phạm vi
- Không đọc GPIO
- Không đọc ADC, UART, I2C, SPI
- Không dùng sensor
- Không điều khiển relay
- Không dùng driver phần cứng riêng

## Cấu trúc
- `src/config/SimulatorConfig.h`
- `src/device/DeviceInfo.h`
- `src/logger/Logger.h`
- `src/mqtt/MqttManager.h`
- `src/simulator/SimulatorApp.h`
- `src/telemetry/TelemetryGenerator.h`
- `src/wifi/WifiManager.h`
- `src/main.cpp`

## Cấu hình
Sửa file `src/config/SimulatorConfig.h`:
- `WIFI_SSID`
- `WIFI_PASSWORD`
- `MQTT_HOST`
- `MQTT_PORT`
- `MQTT_USERNAME`
- `MQTT_PASSWORD`
- `MQTT_ROOT_CA`
- `DEVICE_ID`
- `SERIAL_NUMBER`
- `FIRMWARE_VERSION`
- `MODEL`
- `TOPIC_PREFIX`
- `TELEMETRY_INTERVAL_MS`
- `STATUS_INTERVAL_MS`

## Build
Chạy trong thư mục `Project/firmware/Message_sending_test`:

```bash
pio run
```

## Monitor

```bash
pio device monitor -b 115200
```
