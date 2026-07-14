# resources/views/devices/ - Device management

Quản lý thiết bị đã bán.

## index.blade.php

**Route:** `/devices`
**Controller:** `DeviceController@index`

Danh sách tất cả thiết bị.

### DataTable

- Columns: Serial, Product, Customer, Status, Last Maintenance, Actions
- Search & filter enabled
- Status badges (active, maintenance, error, etc.)

---

## show.blade.php

**Route:** `/devices/{id}`
**Controller:** `DeviceController@show`

Chi tiết thiết bị với telemetry TDS + alert.

### Bao gồm

1. **Device Information Panel**
   - Serial
   - Product
   - Customer
   - Installation Date
   - Last Maintenance
   - Status badge

2. **Telemetry Section**
   - 1 biểu đồ TDS
   - Bảng alert theo từng mốc thời gian

3. **Time Filter Buttons** (demo only)
   - 24h, 7d, 30d
   - Buttons toggle `.active` class only
   - Charts don't actually change data

4. **Maintenance History Table**
   - Date, Type, Notes, Technician

### Dữ liệu

```php
$device = MockData::devices()->find($id);
$sensorData = MockData::deviceSensorData($id);
$maintenance = MockData::maintenanceRecords()
    ->where('device_id', $id);
```

### Chart details

- X-axis: Time labels
- Y-axis: TDS values
- Blue line (#1668e3)
- Smooth curve
- Height: 300px

---

## Known limitations

- Time filter buttons do not update charts
- Charts show static mock data
- Alert table is derived from telemetry mock data

---

## Future enhancement

To make time filters functional:
1. Add JavaScript click handler on time filter buttons
2. Fetch different telemetry ranges based on selected filter
3. Redraw chart with `chart.updateSeries(newData)`
