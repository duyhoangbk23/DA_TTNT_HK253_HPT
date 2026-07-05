<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'code' => $this->product_code,
            'name' => $this->product_name,
            'category_id' => $this->category_id,
            'category' => $this->category->name ?? null,
            'model' => $this->model,
            'capacity' => $this->capacity,
            'unit' => $this->unit ?? 'Chiếc',
            'price' => $this->price,
            'status' => $this->status,
            'image_path' => $this->image_path,
            'created_at' => $this->created_at,
        ];
    }
}
