CREATE DATABASE IF NOT EXISTS smartwater
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE smartwater;

CREATE TABLE IF NOT EXISTS devices (
    id          BIGINT AUTO_INCREMENT PRIMARY KEY,
    device_id   VARCHAR(50)  NOT NULL,
    device_name VARCHAR(200) NOT NULL DEFAULT '',
    status      TINYINT      NOT NULL DEFAULT 0,
    created_at  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_device_id (device_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS device_data (
    id          BIGINT AUTO_INCREMENT PRIMARY KEY,
    device_id   VARCHAR(50)   NOT NULL,
    data_time   DATETIME      NOT NULL,
    tds         DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    alert       TINYINT       NOT NULL DEFAULT 0,
    created_at  DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_device_id (device_id),
    INDEX idx_data_time (data_time),
    INDEX idx_device_data_time (device_id, data_time)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
