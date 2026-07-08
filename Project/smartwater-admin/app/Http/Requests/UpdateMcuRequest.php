<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMcuRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $mcuId = $this->route('mcu');

        return [
            'mcu_code' => 'required|string|max:50|unique:mcus,mcu_code,' . $mcuId,
            'serial_number' => ['required', 'string', 'max:100', 'unique:mcus,serial_number,' . $mcuId, 'regex:/^SN-\d{6}$/'],
            'firmware_version' => 'nullable|string|max:50',
            'status' => 'nullable|in:online,offline,error',
        ];
    }

    public function messages(): array
    {
        return [
            'mcu_code.unique' => 'Mã MCU này đã tồn tại.',
            'serial_number.unique' => 'Serial number này đã tồn tại.',
            'serial_number.regex' => 'Serial của MCU phải theo định dạng SN-xxxxxx (6 số).',
        ];
    }
}
