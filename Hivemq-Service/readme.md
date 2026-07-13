# SmartWater MQTT Service

## Mục tiêu

Xây dựng một **.NET 8 Worker Service** chạy dưới dạng **Windows Service**.

Service có nhiệm vụ:

* Kết nối HiveMQ Broker bằng MQTT.
* Subscribe một hoặc nhiều Topic.
* Nhận dữ liệu JSON từ thiết bị IoT.
* Parse dữ liệu.
* Lưu dữ liệu vào MySQL bằng Dapper.
* Tự động reconnect khi mất kết nối.
* Chạy liên tục 24/7.
* Không sử dụng Entity Framework.
* Không xây dựng hệ thống Logging.

ASP.NET Core Web là một dự án riêng, chỉ đọc dữ liệu từ MySQL.

---

# Công nghệ

* .NET 8 Worker Service
* MQTTnet
* Dapper
* MySqlConnector
* Dependency Injection
* Options Pattern

---

# Kiến trúc

```text
IoT Device
      │
      ▼
 HiveMQ Broker
      │
      ▼
SmartWater.MqttService
      │
      ▼
     MySQL
      │
      ▼
ASP.NET Core Web
```

### Trách nhiệm

**Worker Service**

* Kết nối MQTT
* Subscribe Topic
* Parse JSON
* Validate dữ liệu
* Lưu MySQL

**Web**

* Dashboard
* Quản lý thiết bị
* Báo cáo
* API

---

# Cấu trúc Project

```text
SmartWater.MqttService

├── Configurations
│   ├── HiveMQOptions.cs
│   ├── MySqlOptions.cs
│
├── Models
│
├── Services
│   ├── MqttService.cs
│   ├── DeviceService.cs
│
├── Repositories
│   └── DeviceRepository.cs
│
├── Helpers
│
├── Worker.cs
├── Program.cs
├── appsettings.json
└── README.md
```

---

# Luồng hoạt động

```text
Windows Service Start
        │
        ▼
Đọc appsettings.json
        │
        ▼
Kết nối MySQL
        │
        ▼
Kết nối HiveMQ
        │
        ▼
Subscribe Topic
        │
        ▼
Nhận JSON
        │
        ▼
Parse dữ liệu
        │
        ▼
Validate
        │
        ▼
Lưu MySQL bằng Dapper
        │
        ▼
Tiếp tục lắng nghe
```

---

# Cấu hình

Toàn bộ cấu hình được lưu trong **appsettings.json**.

## MySQL

```json
"MySql": {
  "Server": "127.0.0.1",
  "Port": 3306,
  "Database": "smartwater",
  "User": "root",
  "Password": "123456"
}
```

## HiveMQ

```json
"HiveMQ": {
  "Host": "broker.hivemq.com",
  "Port": 8883,
  "Username": "admin",
  "Password": "123456",
  "ClientId": "SmartWater-Service",
  "Topic": "device/+/status",
  "KeepAlive": 60,
  "UseTls": true
}
```

Không được hard-code thông tin kết nối trong source code.

Sử dụng **Options Pattern** để đọc cấu hình.

---

# Định dạng JSON từ thiết bị

Ví dụ:

```json
{
    "device_id":"DEV001",
    "timestamp":"2026-07-13T14:30:25",
    "tds":152,
    "temperature":28.5,
    "alert":false
}
```

Có thể mở rộng thêm các trường khác trong tương lai.

---

# Thiết kế Database

## devices

Lưu thông tin thiết bị.

| Cột         | Kiểu         |
| ----------- | ------------ |
| id          | bigint PK    |
| device_id   | varchar(50)  |
| device_name | varchar(200) |
| status      | tinyint      |
| created_at  | datetime     |

Index

* device_id

---

## device_data

Lưu dữ liệu nhận từ MQTT.

| Cột         | Kiểu          |
| ----------- | ------------- |
| id          | bigint PK     |
| device_id   | varchar(50)   |
| data_time   | datetime      |
| tds         | decimal(10,2) |
| temperature | decimal(10,2) |
| alert       | tinyint       |
| created_at  | datetime      |

Index

* device_id
* data_time
* (device_id, data_time)

Thiết kế theo dạng dữ liệu có cấu trúc để truy vấn nhanh.

Nếu sau này firmware bổ sung thêm trường mới thì cập nhật Model và Database tương ứng.

---

# MQTT

Worker phải hỗ trợ:

* Connect
* Disconnect
* Auto Reconnect
* Subscribe nhiều Topic
* QoS
* KeepAlive

Nếu mất kết nối:

1. Chờ vài giây.
2. Tự reconnect.
3. Subscribe lại toàn bộ Topic.

Worker không được tự thoát khi mất kết nối MQTT.

---

# Repository

Sử dụng **Dapper**.

Repository chỉ thực hiện:

* INSERT
* UPDATE
* DELETE
* SELECT

Không xử lý Business trong Repository.

---

# Service Layer

## MqttService

Chịu trách nhiệm:

* Connect Broker
* Subscribe Topic
* Nhận Message
* Gọi DeviceService

## DeviceService

Chịu trách nhiệm:

* Parse JSON
* Validate
* Gọi Repository lưu MySQL

---

# Coding Convention

Áp dụng:

* Dependency Injection
* SOLID
* Repository Pattern
* Service Pattern
* Options Pattern
* Async/Await
* CancellationToken

Không viết toàn bộ nghiệp vụ trong Worker.cs.

Worker chỉ có nhiệm vụ khởi động Service.

---

# Windows Service

Publish

```bash
dotnet publish -c Release
```

Tạo Service

```cmd
sc create SmartWaterMQTT binPath= "D:\Services\SmartWater.MqttService\SmartWater.MqttService.exe" start= auto
```

Khởi động

```cmd
sc start SmartWaterMQTT
```

Dừng

```cmd
sc stop SmartWaterMQTT
```

Xóa

```cmd
sc delete SmartWaterMQTT
```

---

# Mở rộng tương lai

Thiết kế để dễ mở rộng:

* Nhiều Broker HiveMQ.
* Nhiều Topic.
* Thêm loại thiết bị mới.
* Thêm trường dữ liệu mới.
* Thêm nhiều Repository.
* Thêm nhiều Service.
* Chuyển sang Linux Service hoặc Docker mà không cần thay đổi kiến trúc.
