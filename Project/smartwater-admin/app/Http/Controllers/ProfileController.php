<?php

namespace App\Http\Controllers;

use App\Support\MockData;

class ProfileController extends Controller
{
    public function index()
    {
        return view('profile.index', [
            'user'       => MockData::currentUser(),
            'activities' => MockData::activities()->take(6),
        ]);
    }
}
