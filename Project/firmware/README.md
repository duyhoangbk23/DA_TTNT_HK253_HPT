# Firmware ESP32

## Yêu cầu
- Cài `Visual Studio Code`
- Cài extension `PlatformIO IDE`
- Kết nối board ESP32 qua cáp USB

## Cấu hình hiện tại
- Board: `ESP32-DevKitC-v4 / ESP32-NodeMCU-32S 38Pins`
- MCU  : ` ESP32-WROOM-32`
- Framework: `Arduino`
- Baud monitor: `115200`

## Định danh telemetry

Firmware chính dùng `Config::Device::MCU_ID` và publish trường JSON `mcu_id` dạng
chuỗi. Ví dụ: `"mcu_id": "ESP32_001"`. Simulator `Message_sending_test` cũng dùng
cùng tên `MCU_ID`/`mcu_id`.

## Build code
Mở thư mục `Project/firmware` trong VS Code, sau đó chạy:

```bash
pio run
```

Lệnh này sẽ biên dịch toàn bộ firmware theo cấu hình trong `platformio.ini`.

## Nạp code vào ESP32
Sau khi build xong, nạp firmware bằng:

```bash
pio run --target upload
```

Nếu máy không tự nhận cổng, chỉ định thủ công:

```bash
pio run --target upload --upload-port COMx
```

Thay `COMx` bằng cổng thực tế của ESP32 trên máy bạn.

## Theo dõi serial
Mở terminal để xem log:

```bash
pio device monitor -b 115200
```

## Quy trình nhanh
1. Mở `Project/firmware`
2. Chạy `pio run`
3. Chạy `pio run --target upload`
4. Mở `pio device monitor -b 115200`

## Lưu ý
- Nếu upload lỗi, kiểm tra lại cổng COM và driver USB-to-UART.
- Nếu board không vào chế độ nạp, giữ nút `BOOT` trong lúc upload.
