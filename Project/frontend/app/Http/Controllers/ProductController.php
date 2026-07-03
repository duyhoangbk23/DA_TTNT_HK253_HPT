<?php

namespace App\Http\Controllers;

use App\Support\MockData;

class ProductController extends Controller
{
    public function index()
    {
        return view('products.index', [
            'products'   => MockData::products(),
            'categories' => MockData::categories(),
        ]);
    }
}
