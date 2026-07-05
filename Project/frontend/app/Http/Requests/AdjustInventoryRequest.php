<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdjustInventoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'quantity' => 'required|integer|min:0',
            'reserved_quantity' => 'required|integer|min:0',
            'unit_cost' => 'required|numeric|min:0',
        ];
    }
}
