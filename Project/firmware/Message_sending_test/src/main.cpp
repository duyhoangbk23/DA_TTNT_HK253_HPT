#include <Arduino.h>

#include "simulator/SimulatorApp.h"

namespace {
SimulatorApp app;
}

void setup() {
    /* Simulator giữ cùng hợp đồng payload với firmware thật để backend xử lý hai nguồn theo một luồng duy nhất. */
    app.begin();
}

void loop() {
    /* Mỗi chu kỳ tạo một mẫu telemetry, gắn mcu_id dạng chuỗi rồi publish lên topic telemetry đã cấu hình. */
    app.loop();
}
