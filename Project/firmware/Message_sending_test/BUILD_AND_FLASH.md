# Hướng dẫn build và nạp code

Thực hiện trong thư mục:

```bash
Project/firmware/Message_sending_test
```

## 1. Chuẩn bị

Kiểm tra file cấu hình:

- `src/config/SimulatorConfig.h`

Sửa các giá trị sau trước khi build:

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

## 2. Build

Chạy lệnh:

```bash
pio run
```

## 3. Nạp code lên ESP32

Chạy lệnh:

```bash
pio run --target upload
```

Nếu cần chỉ định cổng COM:

```bash
pio run --target upload --upload-port COMx
```

Thay `COMx` bằng cổng thực tế của ESP32.

## 4. Theo dõi log Serial

Chạy lệnh:

```bash
pio device monitor -b 115200
```

## 5. Luồng chạy

- ESP32 khởi động
- Kết nối WiFi
- Kết nối MQTT HiveMQ Cloud
- Publish telemetry theo chu kỳ
- Publish trạng thái thiết bị theo chu kỳ
- Nhận command và chỉ log ra Serial
