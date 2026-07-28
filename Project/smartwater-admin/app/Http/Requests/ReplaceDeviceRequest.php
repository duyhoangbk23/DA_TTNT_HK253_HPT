<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReplaceDeviceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'product_id' => 'required|exists:products,id',
            'mcu_id' => 'required|string|max:50|exists:mcus,mcu_id',
            'install_date' => 'nullable|date',
        ];
    }
}
