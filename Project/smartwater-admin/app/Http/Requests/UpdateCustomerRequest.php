<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_code' => 'required|string|max:50|unique:customers,customer_code,' . $this->route('id'),
            'customer_name' => 'required|string|max:150',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|max:150',
            'address' => 'nullable|string|max:255',
            'type' => 'required|in:individual,business',
            'status' => 'required|in:active,inactive',
            'joined_at' => 'nullable|date',
        ];
    }
}
