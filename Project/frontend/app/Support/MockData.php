<?php

namespace App\Support;

use Illuminate\Support\Collection;

/**
 * Nguồn dữ liệu giả lập (Mock Data) cho toàn bộ giao diện demo.
 *
 * Cấu trúc dữ liệu bám sát ERD của hệ thống quản lý dịch vụ bảo trì máy lọc nước:
 * roles, employees, users, customers, categories, products, suppliers, batches,
 * inventories, contracts, devices, device_dashboard_data, maintenance_records, activity_log.
 *
 * Tất cả method đều trả về Collection để dễ lọc / phân trang ở tầng Controller.
 * KHÔNG chứa nghiệp vụ - chỉ phục vụ hiển thị.
 */
class MockData
{
    /* -------------------------------------------------------------------- */
    /*  Danh mục sản phẩm                                                    */
    /* -------------------------------------------------------------------- */
    public static function categories(): Collection
    {
        return collect([
            ['id' => 1, 'name' => 'Máy lọc nước RO', 'description' => 'Dòng máy lọc thẩm thấu ngược', 'status' => 'active'],
            ['id' => 2, 'name' => 'Máy lọc nước Nano', 'description' => 'Công nghệ lọc Nano tiết kiệm điện', 'status' => 'active'],
            ['id' => 3, 'name' => 'Máy lọc nước công nghiệp', 'description' => 'Công suất lớn cho nhà máy, tòa nhà', 'status' => 'active'],
            ['id' => 4, 'name' => 'Lõi lọc & Phụ kiện', 'description' => 'Lõi lọc thay thế và linh kiện', 'status' => 'active'],
        ]);
    }

    /* -------------------------------------------------------------------- */
    /*  Sản phẩm                                                             */
    /* -------------------------------------------------------------------- */
    public static function products(): Collection
    {
        $rows = [
            ['AQ-RO-50', 'AquaPure RO 50', 1, 'APRO-50', '50 L/h', 'active', 'AQ-RO-50.jpg'],
            ['AQ-RO-75', 'AquaPure RO 75', 1, 'APRO-75', '75 L/h', 'active', 'AQ-RO-75.jpg'],
            ['AQ-RO-100', 'AquaPure RO 100', 1, 'APRO-100', '100 L/h', 'active', 'AQ-RO-100.jpg'],
            ['AQ-NANO-30', 'AquaPure Nano 30', 2, 'APNA-30', '30 L/h', 'active', 'AQ-NANO-30.jpg'],
            ['AQ-NANO-45', 'AquaPure Nano 45', 2, 'APNA-45', '45 L/h', 'maintenance', 'AQ-NANO-45.jpg'],
            ['AQ-IND-500', 'AquaPure Industrial 500', 3, 'APIN-500', '500 L/h', 'active', 'AQ-IND-500.jpg'],
            ['AQ-IND-1000', 'AquaPure Industrial 1000', 3, 'APIN-1000', '1000 L/h', 'active', 'AQ-IND-1000.jpg'],
            ['AQ-CORE-01', 'Lõi lọc Sediment PP', 4, 'CORE-01', '-', 'active', 'AQ-CORE-01.jpg'],
            ['AQ-CORE-02', 'Lõi lọc Carbon GAC', 4, 'CORE-02', '-', 'active', 'AQ-CORE-02.jpg'],
            ['AQ-CORE-RO', 'Màng lọc RO 75GPD', 4, 'CORE-RO', '-', 'inactive', 'AQ-CORE-RO.jpg'],
        ];

        return collect($rows)->map(fn ($r, $i) => [
            'id'          => $i + 1,
            'code'        => $r[0],
            'name'        => $r[1],
            'category_id' => $r[2],
            'category'    => self::categories()->firstWhere('id', $r[2])['name'] ?? '-',
            'model'       => $r[3],
            'capacity'    => $r[4],
            'unit'        => 'Chiếc',
            'price'       => 3_200_000 + $i * 850_000,
            'status'      => $r[5],
            'image'       => $r[6],
        ]);
    }

    /* -------------------------------------------------------------------- */
    /*  Nhà cung cấp                                                         */
    /* -------------------------------------------------------------------- */
    public static function suppliers(): Collection
    {
        return collect([
            ['id' => 1, 'name' => 'Công ty TNHH Kỹ thuật nước Việt', 'contact' => 'Trần Minh Khoa', 'phone' => '0901 234 567', 'email' => 'sales@ktnvit.vn', 'address' => 'KCN Tân Bình, TP.HCM'],
            ['id' => 2, 'name' => 'Aqua Components JSC', 'contact' => 'Nguyễn Thu Hà', 'phone' => '0902 345 678', 'email' => 'contact@aquacomp.vn', 'address' => 'Bắc Ninh'],
            ['id' => 3, 'name' => 'Global Water Supply', 'contact' => 'Lê Quốc Bảo', 'phone' => '0903 456 789', 'email' => 'info@gws.com.vn', 'address' => 'Bình Dương'],
        ]);
    }

    /* -------------------------------------------------------------------- */
    /*  Kho thiết bị (tồn kho theo sản phẩm)                                 */
    /* -------------------------------------------------------------------- */
    public static function inventories(): Collection
    {
        return self::products()->map(function ($p, $i) {
            $qty      = [42, 18, 7, 65, 3, 12, 5, 210, 180, 0][$i] ?? 10;
            $reserved = [8, 2, 1, 10, 0, 3, 1, 40, 30, 0][$i] ?? 2;

            return [
                'id'            => $i + 1,
                'product_id'    => $p['id'],
                'product'       => $p['name'],
                'code'          => $p['code'],
                'model'         => $p['model'],
                'quantity'      => $qty,
                'reserved'      => $reserved,
                'available'     => max($qty - $reserved, 0),
                'unit_cost'     => $p['price'] * 0.7,
                'last_updated'  => now()->subDays($i)->format('d/m/Y'),
                'stock_status'  => $qty === 0 ? 'out' : ($qty <= 10 ? 'low' : 'ok'),
            ];
        });
    }

    /* -------------------------------------------------------------------- */
    /*  Lô hàng nhập kho                                                     */
    /* -------------------------------------------------------------------- */
    public static function batches(): Collection
    {
        $rows = [
            ['LOT-2025-001', 1, '15/01/2025', '15/01/2028', 120],
            ['LOT-2025-002', 2, '02/02/2025', '02/02/2028', 80],
            ['LOT-2025-003', 3, '20/03/2025', '20/03/2027', 300],
            ['LOT-2025-004', 1, '11/04/2025', '11/04/2028', 60],
            ['LOT-2025-005', 2, '05/05/2025', '05/05/2028', 150],
            ['LOT-2025-006', 3, '28/05/2025', '28/05/2027', 45],
        ];

        return collect($rows)->map(fn ($r, $i) => [
            'id'          => $i + 1,
            'code'        => $r[0],
            'supplier_id' => $r[1],
            'supplier'    => self::suppliers()->firstWhere('id', $r[1])['name'] ?? '-',
            'import_date' => $r[2],
            'expiry_date' => $r[3],
            'quantity'    => $r[4],
            'note'        => 'Nhập kho định kỳ theo hợp đồng cung ứng.',
        ]);
    }

    /** Chi tiết lô hàng (danh sách thiết bị/sản phẩm trong 1 lô). */
    public static function batchDetails(int $batchId): Collection
    {
        $products = self::products()->take(5)->values();

        return $products->map(fn ($p, $i) => [
            'id'         => $batchId * 10 + $i,
            'product'    => $p['name'],
            'code'       => $p['code'],
            'model'      => $p['model'],
            'quantity'   => (($batchId + $i) % 5 + 1) * 8,
            'unit_cost'  => $p['price'] * 0.7,
        ]);
    }

    /* -------------------------------------------------------------------- */
    /*  Khách hàng                                                           */
    /* -------------------------------------------------------------------- */
    public static function customers(): Collection
    {
        $names = [
            'Nguyễn Văn An', 'Trần Thị Bình', 'Lê Hoàng Cường', 'Phạm Thu Dung', 'Võ Minh Đức',
            'Đặng Thị Hoa', 'Bùi Quang Huy', 'Công ty CP Sài Gòn Food', 'Trường Mầm non Hoa Sen', 'Hồ Thị Lan',
            'Ngô Bá Khánh', 'Khách sạn Bình Minh', 'Dương Thị Mai', 'Đỗ Văn Nam', 'Nhà hàng Phố Biển',
            'Lý Thị Oanh', 'Trịnh Công Phúc', 'Chung cư Green Park', 'Vũ Thị Quỳnh', 'Mai Văn Sơn',
            'Phan Thị Trang', 'Cao Minh Tuấn', 'Bệnh viện Đa khoa An Sinh', 'Đinh Thị Vân',
        ];
        $districts = ['Quận 1', 'Quận 3', 'Quận 7', 'Quận Bình Thạnh', 'Quận Tân Bình', 'TP. Thủ Đức', 'Quận Gò Vấp'];

        return collect($names)->map(function ($name, $i) use ($districts) {
            $isCompany = str_contains($name, 'Công ty') || str_contains($name, 'Trường')
                || str_contains($name, 'Khách sạn') || str_contains($name, 'Nhà hàng')
                || str_contains($name, 'Chung cư') || str_contains($name, 'Bệnh viện');

            return [
                'id'      => $i + 1,
                'code'    => sprintf('KH-%04d', $i + 1),
                'name'    => $name,
                'avatar'  => 'https://i.pravatar.cc/120?img=' . (($i % 60) + 1),
                'phone'   => sprintf('09%02d %03d %03d', $i + 10, 100 + $i, 200 + $i),
                'email'   => 'kh' . ($i + 1) . '@email.com',
                'address' => sprintf('%d Nguyễn Trãi, %s, TP.HCM', 12 + $i, $districts[$i % count($districts)]),
                'type'    => $isCompany ? 'company' : 'individual',
                'status'  => ($i % 8 === 0) ? 'inactive' : 'active',
                'joined'  => now()->subDays(($i + 1) * 9)->format('d/m/Y'),
            ];
        });
    }

    public static function findCustomer(int $id): ?array
    {
        return self::customers()->firstWhere('id', $id);
    }

    /* -------------------------------------------------------------------- */
    /*  Hợp đồng                                                             */
    /* -------------------------------------------------------------------- */
    public static function contracts(): Collection
    {
        $types    = ['install' => 'Lắp đặt', 'maintenance' => 'Bảo trì', 'replace' => 'Thay thế'];
        $statuses = ['active', 'active', 'active', 'expired', 'cancelled'];
        $customers = self::customers();

        return collect(range(0, 17))->map(function ($i) use ($types, $statuses, $customers) {
            $cust     = $customers[$i % $customers->count()];
            $typeKey  = array_keys($types)[$i % 3];
            $start    = now()->subMonths($i + 1);
            $end      = (clone $start)->addMonths(24);

            return [
                'id'            => $i + 1,
                'code'          => sprintf('HĐ-2025-%03d', $i + 1),
                'customer_id'   => $cust['id'],
                'customer'      => $cust['name'],
                'device_code'   => sprintf('TB-%05d', 1000 + $i),
                'type'          => $typeKey,
                'type_label'    => $types[$typeKey],
                'sign_date'     => $start->format('d/m/Y'),
                'install_date'  => (clone $start)->addDays(3)->format('d/m/Y'),
                'end_date'      => $end->format('d/m/Y'),
                'cycle'         => [3, 6, 12][$i % 3] . ' tháng',
                'amount'        => (6 + $i) * 1_500_000,
                'status'        => $statuses[$i % count($statuses)],
                'expiring_soon' => $i % 6 === 1,
            ];
        });
    }

    public static function contractsForCustomer(int $customerId): Collection
    {
        return self::contracts()->where('customer_id', $customerId)->values();
    }

    public static function contractServices(): Collection
    {
        return collect([
            ['name' => 'Vệ sinh & thay lõi lọc số 1-2-3', 'interval' => 90, 'description' => 'Bảo trì định kỳ 3 tháng/lần'],
            ['name' => 'Kiểm tra màng RO', 'interval' => 180, 'description' => 'Kiểm tra & vệ sinh màng lọc RO'],
            ['name' => 'Thay lõi lọc chức năng', 'interval' => 365, 'description' => 'Thay lõi Mineral / Alkaline'],
        ]);
    }

    /* -------------------------------------------------------------------- */
    /*  Thiết bị đã lắp đặt tại khách hàng                                   */
    /* -------------------------------------------------------------------- */
    public static function devices(): Collection
    {
        $statuses = ['active', 'active', 'active', 'maintenance', 'error', 'pending'];
        $products = self::products()->whereIn('category_id', [1, 2, 3])->values();
        $customers = self::customers();

        return collect(range(0, 29))->map(function ($i) use ($statuses, $products, $customers) {
            $product = $products[$i % $products->count()];
            $cust    = $customers[$i % $customers->count()];

            return [
                'id'          => $i + 1,
                'code'        => sprintf('TB-%05d', 1000 + $i),
                'serial'      => sprintf('SN-%s-%04d', strtoupper(substr($product['model'], 0, 4)), 2500 + $i),
                'model'       => $product['model'],
                'product'     => $product['name'],
                'firmware'    => 'v' . (1 + $i % 3) . '.' . ($i % 10) . '.' . ($i % 5),
                'customer_id' => $cust['id'],
                'customer'    => $cust['name'],
                'batch'       => self::batches()[$i % self::batches()->count()]['code'],
                'import_date' => now()->subDays(30 + $i * 5)->format('d/m/Y'),
                'install_date'=> now()->subDays(20 + $i * 4)->format('d/m/Y'),
                'location'    => $cust['address'],
                'status'      => $statuses[$i % count($statuses)],
            ];
        });
    }

    public static function findDevice(int $id): ?array
    {
        return self::devices()->firstWhere('id', $id);
    }

    public static function devicesForCustomer(int $customerId): Collection
    {
        return self::devices()->where('customer_id', $customerId)->values();
    }

    /**
     * Dữ liệu cảm biến giả lập cho biểu đồ chi tiết thiết bị.
     * $range: 24h | 7d | 30d
     */
    public static function telemetry(string $range = '24h'): array
    {
        [$points, $labels] = match ($range) {
            '7d'    => [7, ['T2', 'T3', 'T4', 'T5', 'T6', 'T7', 'CN']],
            '30d'   => [10, ['01', '04', '07', '10', '13', '16', '19', '22', '25', '28']],
            default => [12, ['00h', '02h', '04h', '06h', '08h', '10h', '12h', '14h', '16h', '18h', '20h', '22h']],
        };

        $wave = fn (int $base, int $amp, int $shift) => collect(range(0, $points - 1))
            ->map(fn ($x) => round($base + $amp * sin(($x + $shift) / 2), 1))->all();

        return [
            'labels'      => $labels,
            'tds'         => $wave(52, 8, 1),        // ppm
            'temperature' => $wave(28, 3, 2),        // °C
            'water_flow'  => $wave(170, 25, 0),      // L
            'ph'          => $wave(72, 4, 3),        // -> chia 10 khi hiển thị (7.2)
        ];
    }

    /* -------------------------------------------------------------------- */
    /*  Nhân viên                                                            */
    /* -------------------------------------------------------------------- */
    public static function roles(): Collection
    {
        return collect([
            ['id' => 1, 'name' => 'Quản trị hệ thống'],
            ['id' => 2, 'name' => 'Quản lý'],
            ['id' => 3, 'name' => 'Kỹ thuật viên'],
            ['id' => 4, 'name' => 'Nhân viên kho'],
            ['id' => 5, 'name' => 'Chăm sóc khách hàng'],
        ]);
    }

    public static function employees(): Collection
    {
        $rows = [
            ['Hoàng Anh Duy', 'Giám đốc kỹ thuật', 1],
            ['Nguyễn Thị Mỹ Linh', 'Trưởng phòng dịch vụ', 2],
            ['Trần Văn Hùng', 'Kỹ thuật viên trưởng', 3],
            ['Lê Thị Ngọc', 'Kỹ thuật viên', 3],
            ['Phạm Quốc Việt', 'Kỹ thuật viên', 3],
            ['Đỗ Thành Long', 'Nhân viên kho', 4],
            ['Vũ Thị Hạnh', 'Chăm sóc khách hàng', 5],
            ['Bùi Minh Tú', 'Kỹ thuật viên', 3],
            ['Ngô Thị Thu', 'Chăm sóc khách hàng', 5],
        ];

        return collect($rows)->map(fn ($r, $i) => [
            'id'     => $i + 1,
            'code'   => sprintf('NV-%03d', $i + 1),
            'name'   => $r[0],
            'avatar' => 'https://i.pravatar.cc/120?img=' . (($i + 30) % 60 + 1),
            'email'  => 'nv' . ($i + 1) . '@smartwater.vn',
            'phone'  => sprintf('098%02d %03d %03d', $i, 100 + $i, 300 + $i),
            'position' => $r[1],
            'role_id'  => $r[2],
            'role'     => self::roles()->firstWhere('id', $r[2])['name'] ?? '-',
            'hire_date'=> now()->subMonths(($i + 1) * 5)->format('d/m/Y'),
            'status'   => ($i % 7 === 0 && $i !== 0) ? 'inactive' : 'active',
        ]);
    }

    /* -------------------------------------------------------------------- */
    /*  Bảo trì                                                              */
    /* -------------------------------------------------------------------- */
    public static function maintenance(): Collection
    {
        $types    = ['routine' => 'Định kỳ', 'repair' => 'Sửa chữa', 'replace' => 'Thay thế'];
        $statuses = ['completed', 'completed', 'pending'];
        $devices  = self::devices();
        $employees = self::employees()->where('role_id', 3)->values();

        return collect(range(0, 19))->map(function ($i) use ($types, $statuses, $devices, $employees) {
            $device   = $devices[$i % $devices->count()];
            $employee = $employees[$i % $employees->count()];
            $typeKey  = array_keys($types)[$i % 3];

            return [
                'id'          => $i + 1,
                'code'        => sprintf('BT-%04d', $i + 1),
                'device_id'   => $device['id'],
                'device_code' => $device['code'],
                'customer'    => $device['customer'],
                'employee'    => $employee['name'],
                'date'        => now()->subDays($i * 3)->format('d/m/Y'),
                'type'        => $typeKey,
                'type_label'  => $types[$typeKey],
                'description' => 'Vệ sinh thiết bị, kiểm tra & thay lõi lọc theo lịch.',
                'parts'       => $i % 2 === 0 ? 'Lõi PP, Lõi Carbon' : 'Màng RO 75GPD',
                'cost'        => (2 + $i % 5) * 250_000,
                'status'      => $statuses[$i % count($statuses)],
            ];
        });
    }

    public static function maintenanceForDevice(int $deviceId): Collection
    {
        return self::maintenance()->where('device_id', $deviceId)->values();
    }

    public static function maintenanceForCustomer(string $customerName): Collection
    {
        return self::maintenance()->where('customer', $customerName)->values();
    }

    /* -------------------------------------------------------------------- */
    /*  Nhật ký hoạt động                                                    */
    /* -------------------------------------------------------------------- */
    public static function activities(): Collection
    {
        $actions = [
            ['Đăng nhập hệ thống', 'Auth', 'bi-box-arrow-in-right'],
            ['Tạo hợp đồng mới', 'Hợp đồng', 'bi-file-earmark-plus'],
            ['Cập nhật thông tin khách hàng', 'Khách hàng', 'bi-person-gear'],
            ['Hoàn thành phiếu bảo trì', 'Bảo trì', 'bi-check2-circle'],
            ['Nhập kho lô hàng mới', 'Kho', 'bi-box-seam'],
            ['Kích hoạt thiết bị', 'Thiết bị', 'bi-cpu'],
            ['Xuất báo cáo tháng', 'Báo cáo', 'bi-file-earmark-bar-graph'],
            ['Thêm nhân viên mới', 'Nhân viên', 'bi-person-plus'],
        ];
        $employees = self::employees();

        return collect(range(0, 24))->map(function ($i) use ($actions, $employees) {
            $a   = $actions[$i % count($actions)];
            $emp = $employees[$i % $employees->count()];

            return [
                'id'          => $i + 1,
                'time'        => now()->subMinutes($i * 47)->format('d/m/Y H:i'),
                'user'        => $emp['name'],
                'avatar'      => $emp['avatar'],
                'action'      => $a[0],
                'module'      => $a[1],
                'icon'        => $a[2],
                'description' => $a[0] . ' - ' . $a[1] . ' bởi ' . $emp['name'],
                'ip'          => '192.168.1.' . (10 + $i),
            ];
        });
    }

    /* -------------------------------------------------------------------- */
    /*  Dashboard: KPI / Charts / Widgets                                    */
    /* -------------------------------------------------------------------- */
    public static function dashboardKpis(): Collection
    {
        $devices   = self::devices();
        $contracts = self::contracts();

        return collect([
            ['label' => 'Tổng khách hàng', 'value' => self::customers()->count(), 'trend' => '+8.2%', 'up' => true, 'icon' => 'bi-people', 'color' => 'primary'],
            ['label' => 'Tổng sản phẩm', 'value' => self::products()->count(), 'trend' => '+2', 'up' => true, 'icon' => 'bi-box-seam', 'color' => 'info'],
            ['label' => 'Tổng thiết bị', 'value' => $devices->count(), 'trend' => '+5.4%', 'up' => true, 'icon' => 'bi-cpu', 'color' => 'secondary'],
            ['label' => 'Thiết bị hoạt động', 'value' => $devices->where('status', 'active')->count(), 'trend' => '+3.7%', 'up' => true, 'icon' => 'bi-check-circle', 'color' => 'success'],
            ['label' => 'Thiết bị bảo trì', 'value' => $devices->where('status', 'maintenance')->count(), 'trend' => '-1.1%', 'up' => false, 'icon' => 'bi-tools', 'color' => 'warning'],
            ['label' => 'Hợp đồng còn hiệu lực', 'value' => $contracts->where('status', 'active')->count(), 'trend' => '+4', 'up' => true, 'icon' => 'bi-file-earmark-check', 'color' => 'primary'],
        ]);
    }

    /** Số lượng thiết bị theo từng trạng thái (cho biểu đồ tròn). */
    public static function deviceStatusBreakdown(): array
    {
        $devices = self::devices();

        return [
            'labels' => ['Hoạt động', 'Bảo trì', 'Lỗi', 'Chờ lắp đặt'],
            'series' => [
                $devices->where('status', 'active')->count(),
                $devices->where('status', 'maintenance')->count(),
                $devices->where('status', 'error')->count(),
                $devices->where('status', 'pending')->count(),
            ],
        ];
    }

    public static function customersByMonth(): array
    {
        return [
            'labels' => ['T1', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7', 'T8', 'T9', 'T10', 'T11', 'T12'],
            'series' => [4, 6, 8, 5, 9, 12, 10, 14, 11, 16, 13, 18],
        ];
    }

    public static function maintenanceByMonth(): array
    {
        return [
            'labels' => ['T1', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7', 'T8', 'T9', 'T10', 'T11', 'T12'],
            'series' => [12, 18, 15, 22, 19, 25, 21, 28, 24, 30, 27, 33],
        ];
    }

    /** Người dùng đang đăng nhập (giả lập) cho navbar / hồ sơ cá nhân. */
    public static function currentUser(): array
    {
        $emp = self::employees()->first();

        return [
            'name'     => $emp['name'],
            'avatar'   => $emp['avatar'],
            'email'    => $emp['email'],
            'phone'    => $emp['phone'],
            'position' => $emp['position'],
            'role'     => $emp['role'],
            'address'  => '25 Lê Lợi, Quận 1, TP.HCM',
        ];
    }

    /** Thông báo giả lập trên navbar. */
    public static function notifications(): Collection
    {
        return collect([
            ['icon' => 'bi-tools', 'color' => 'warning', 'title' => '3 thiết bị đến hạn bảo trì', 'time' => '5 phút trước'],
            ['icon' => 'bi-file-earmark-check', 'color' => 'primary', 'title' => 'Hợp đồng HĐ-2025-002 sắp hết hạn', 'time' => '1 giờ trước'],
            ['icon' => 'bi-box-seam', 'color' => 'danger', 'title' => 'Lõi lọc Sediment sắp hết hàng', 'time' => '3 giờ trước'],
            ['icon' => 'bi-person-plus', 'color' => 'success', 'title' => 'Khách hàng mới vừa đăng ký', 'time' => 'Hôm qua'],
        ]);
    }
}
