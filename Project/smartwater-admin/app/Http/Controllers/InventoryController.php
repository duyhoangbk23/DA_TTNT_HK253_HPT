<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Services\InventoryService;
use App\Http\Requests\AdjustInventoryRequest;
use App\Http\Resources\InventoryResource;

class InventoryController extends Controller
{
    protected $inventoryService;

    public function __construct(InventoryService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }

    public function index()
    {
        $inventories = $this->inventoryService->getAllInventories();

        $formatted = $inventories->map(fn ($inv) => [
            'id' => $inv->id,
            'product' => $inv->product?->product_name ?? '-',
            'code' => $inv->product?->product_code ?? '-',
            'model' => $inv->product?->model ?? '-',
            'quantity' => $inv->quantity,
            'reserved' => $inv->reserved_quantity,
            'available' => max($inv->quantity - $inv->reserved_quantity, 0),
            'unit_cost' => $inv->unit_cost,
            'last_updated' => $inv->updated_at->format('d/m/Y H:i'),
            'stock_status' => $inv->quantity == 0 ? 'out' : ($inv->quantity <= 10 ? 'low' : 'ok'),
        ])->toArray();

        return view('inventory.index', [
            'inventories' => $formatted,
        ]);
    }

    public function show($id)
    {
        $inventory = $this->inventoryService->getInventoryById($id);
        return view('inventory.show', ['inventory' => $inventory]);
    }

    public function adjust(AdjustInventoryRequest $request, $id)
    {
        $inventory = $this->inventoryService->adjustInventory($id, $request->validated());
        return redirect()->route('inventory.index')->with('success', 'Tồn kho đã được cập nhật');
    }

    public function apiIndex()
    {
        $inventories = $this->inventoryService->getAllInventories();
        return response()->json(InventoryResource::collection($inventories));
    }

    public function apiShow($id)
    {
        $inventory = $this->inventoryService->getInventoryById($id);
        return response()->json(new InventoryResource($inventory));
    }

    public function apiAdjust(AdjustInventoryRequest $request, $id)
    {
        $inventory = $this->inventoryService->adjustInventory($id, $request->validated());
        return response()->json(new InventoryResource($inventory));
    }
}
