<?php

namespace App\Http\Controllers;

use App\Support\MockData;

class ContractController extends Controller
{
    public function index()
    {
        return view('contracts.index', [
            'contracts' => MockData::contracts(),
        ]);
    }
}
