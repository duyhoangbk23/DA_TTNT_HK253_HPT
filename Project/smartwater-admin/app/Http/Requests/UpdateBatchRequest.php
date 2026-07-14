<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBatchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'batch_code' => 'required|string|max:50|unique:batches,batch_code,' . $this->route('id'),
            'supplier_id' => 'required|exists:suppliers,id',
            'import_date' => 'required|date',
            'expiry_date' => 'nullable|date',
            'quantity' => 'required|integer|min:1',
            'note' => 'nullable|string|max:500',
        ];
    }
}
