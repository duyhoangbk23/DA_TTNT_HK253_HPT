<?php

namespace App\Services;

use App\Models\Inventory;

class InventoryService
{
    public function getAllInventories()
    {
        return Inventory::with('product')->get();
    }

    public function getInventoryById($id)
    {
        return Inventory::with('product')->findOrFail($id);
    }

    public function adjustInventory($id, array $data)
    {
        $inventory = Inventory::findOrFail($id);

        if ($data['reserved_quantity'] > $data['quantity']) {
            throw new \Exception('Reserved quantity cannot exceed total quantity');
        }

        $inventory->update([
            'quantity' => $data['quantity'],
            'reserved_quantity' => $data['reserved_quantity'],
            'unit_cost' => $data['unit_cost'],
        ]);

        return $inventory;
    }

    public function getAvailable($id)
    {
        $inventory = Inventory::findOrFail($id);
        return max($inventory->quantity - $inventory->reserved_quantity, 0);
    }

    public function getStockStatus($id)
    {
        $inventory = Inventory::findOrFail($id);
        if ($inventory->quantity == 0) {
            return 'out';
        }
        return $inventory->quantity <= 10 ? 'low' : 'ok';
    }
}
