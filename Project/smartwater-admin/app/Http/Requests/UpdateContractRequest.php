<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateContractRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'contract_code' => 'required|string|max:50|unique:contracts,contract_code,' . $this->route('id'),
            'customer_id' => 'required|exists:customers,id',
            'contract_type' => 'required|string|max:100',
            'start_date' => 'required|date',
            'install_date' => 'nullable|date',
            'end_date' => 'required|date|after:start_date',
            'maintenance_cycle_months' => 'required|integer|min:1',
            'amount' => 'required|integer|min:0',
            'status' => 'required|in:active,inactive,expired',
        ];
    }
}
