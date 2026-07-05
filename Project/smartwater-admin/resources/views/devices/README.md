# resources/views/devices/ — Device management

Quản lý thiết bị đã bán.

## 📌 index.blade.php

**Route:** `/devices`
**Controller:** DeviceController@index

Danh sách tất cả thiết bị.

### DataTable

- Columns: Serial, Product, Customer, Status, Last Maintenance, Actions
- Search & filter enabled
- Status badges (active, maintenance, error, etc.)

---

## 📌 show.blade.php

**Route:** `/devices/{id}`
**Controller:** DeviceController@show

Chi tiết thiết bị với sensor data & charts.

### Bao gồm

1. **Device Information Panel:**
   - Serial
   - Product
   - Customer
   - Installation Date
   - Last Maintenance
   - Status badge

2. **Sensor Charts (4 Line Charts):**
   - **TDS (Total Dissolved Solids)** — ppm
   - **Temperature** — °C
   - **Flow Rate** — L/h
   - **pH Level** — 0-14

3. **Time Filter Buttons:** (demo only, não-functional)
   - 24h, 7d, 30d
   - Buttons toggle `.active` class only
   - Charts don't actually change data

4. **Maintenance History Table:**
   - Date, Type, Notes, Technician

### Dữ liệu

```php
$device = MockData::devices()->find($id);
$sensorData = MockData::deviceSensorData($id);  // Mock sensor readings
$maintenance = MockData::maintenanceRecords()
    ->where('device_id', $id);
```

### Chart Details

Each chart:
- X-axis: Time labels (hourly or daily based on filter)
- Y-axis: Sensor reading values
- Blue line (#1668e3)
- Smooth curve
- Height: 300px

**Example TDS Chart:**
```
Times: 00:00, 01:00, 02:00, ...
Values: 150, 145, 148, 152, ... (ppm)
```

---

## ⚠️ Known Limitations

❌ Time filter buttons (24h/7d/30d) don't update charts — only toggle CSS `.active` class
❌ Charts show static mock data, not real sensor readings
✅ Layout and styling are complete
✅ Charts render correctly with mock data

---

## 💡 Future Enhancement

To make time filters functional:
1. Add JavaScript click handler on time filter buttons
2. Fetch different `deviceSensorData` based on selected range
3. Redraw charts with `chart.updateSeries(newData)`

See `public/js/app.js` for ApexCharts helper functions.
