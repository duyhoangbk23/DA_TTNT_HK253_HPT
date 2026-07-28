<?php

namespace App\Http\Requests;

use App\Models\Device;
use App\Models\Mcu;
use Illuminate\Foundation\Http\FormRequest;

class StoreContractRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'contract_code' => 'required|string|max:50|unique:contracts,contract_code',
            'customer_name' => 'required|string|max:150',
            'contract_type' => 'required|string|max:100',
            'start_date' => 'required|date',
            'install_date' => 'nullable|date',
            'end_date' => 'required|date|after:start_date',
            'maintenance_cycle_months' => 'required|integer|min:1',
            'amount' => 'required|integer|min:0',
            'status' => 'required|in:active,inactive,expired',
            'device_ids' => 'nullable|array',
            'device_ids.*' => 'required_with:device_ids|distinct|exists:devices,id',
            'mcu_ids' => 'nullable|array',
            'mcu_ids.*' => 'required_with:mcu_ids|distinct|string|max:50|exists:mcus,mcu_id',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $deviceIds = $this->input('device_ids', []);
            $mcuIds = $this->input('mcu_ids', []);

            if (is_array($deviceIds) || is_array($mcuIds)) {
                $deviceCount = is_array($deviceIds) ? count($deviceIds) : 0;
                $mcuCount = is_array($mcuIds) ? count($mcuIds) : 0;

                if ($deviceCount !== $mcuCount) {
                    $validator->errors()->add('mcu_ids', 'Số lượng MCU phải bằng số lượng thiết bị.');
                }
            }

            foreach ((array) $deviceIds as $deviceId) {
                if (!Device::unused()->whereKey($deviceId)->exists()) {
                    $validator->errors()->add('device_ids', 'Có thiết bị đã được sử dụng hoặc không còn khả dụng.');
                    break;
                }
            }

            foreach ((array) $mcuIds as $mcuId) {
                if (!Mcu::unused()->where('mcu_id', $mcuId)->exists()) {
                    $validator->errors()->add('mcu_ids', 'Có MCU đã được sử dụng hoặc không còn khả dụng.');
                    break;
                }
            }
        });
    }
}
