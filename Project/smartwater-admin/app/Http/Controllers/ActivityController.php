<?php

namespace App\Http\Controllers;

use App\Support\MockData;

class ActivityController extends Controller
{
    public function index()
    {
        return view('activities.index', [
            'activities' => MockData::activities(),
        ]);
    }
}
