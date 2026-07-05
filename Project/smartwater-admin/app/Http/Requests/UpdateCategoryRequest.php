<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:100|unique:categories,name,' . $this->category,
            'description' => 'nullable|string|max:500',
            'status' => 'required|in:active,inactive',
        ];
    }
}
