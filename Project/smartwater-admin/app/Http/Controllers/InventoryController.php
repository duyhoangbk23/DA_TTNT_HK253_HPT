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
        return view('inventory.index', [
            'inventories' => $inventories,
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
