<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDeviceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'device_code' => 'required|string|max:50|unique:devices,device_code',
            'serial_number' => 'required|string|max:100|unique:devices,serial_number',
            'product_id' => 'required|exists:products,id',
            'mcu_id' => 'nullable|string|max:50|exists:mcus,mcu_id',
            'customer_id' => 'nullable|exists:customers,id',
            'contract_id' => 'nullable|exists:contracts,id',
            'batch_id' => 'nullable|exists:batches,id',
            'import_date' => 'required|date',
            'install_date' => 'nullable|date',
            'firmware_version' => 'nullable|string|max:50',
            'location' => 'nullable|string|max:255',
            'status' => 'required|in:active,maintenance,error,pending',
        ];
    }
}
