<?php

namespace App\Http\Controllers;

class AuthController extends Controller
{
    /** Chỉ hiển thị giao diện đăng nhập - KHÔNG xử lý xác thực. */
    public function login()
    {
        return view('auth.login');
    }
}
