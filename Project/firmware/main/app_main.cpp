#include <Arduino.h>

#include <device_manager.h>

void setup() {
    // Entry point chỉ ủy quyền cho DeviceManager; toàn bộ khởi tạo phần cứng và task nền nằm trong manager này.
    DeviceManager::instance().begin();
}

void loop() {
    // Chu kỳ chính duy trì các tác vụ mạng và xử lý lệnh mà không chặn các FreeRTOS task đọc/gửi telemetry.
    DeviceManager::instance().loop();
}
