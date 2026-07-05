<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'code' => 'required|string|max:50|unique:products,product_code,' . $this->product,
            'name' => 'required|string|max:150',
            'category_id' => 'required|exists:categories,id',
            'model' => 'required|string|max:100',
            'capacity' => 'required|string|max:50',
            'unit' => 'nullable|string|max:50',
            'price' => 'required|integer|min:0',
            'status' => 'required|in:active,maintenance,inactive',
            'image_path' => 'nullable|string|max:255',
        ];
    }
}
