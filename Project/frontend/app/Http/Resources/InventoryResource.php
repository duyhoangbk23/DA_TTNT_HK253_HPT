<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InventoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $available = max($this->quantity - $this->reserved_quantity, 0);
        $stockStatus = $this->quantity == 0 ? 'out' : ($this->quantity <= 10 ? 'low' : 'ok');

        return [
            'id' => $this->id,
            'product_id' => $this->product_id,
            'product' => $this->product->name ?? null,
            'code' => $this->product->product_code ?? null,
            'model' => $this->product->model ?? null,
            'quantity' => $this->quantity,
            'reserved' => $this->reserved_quantity,
            'available' => $available,
            'unit_cost' => $this->unit_cost,
            'stock_status' => $stockStatus,
            'last_updated' => $this->updated_at,
        ];
    }
}
