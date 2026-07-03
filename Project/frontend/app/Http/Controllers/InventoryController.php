<?php

namespace App\Http\Controllers;

use App\Support\MockData;

class InventoryController extends Controller
{
    public function index()
    {
        return view('inventory.index', [
            'inventories' => MockData::inventories(),
        ]);
    }
}
