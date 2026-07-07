<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\UpdateProfileRequest;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $activities = $user->activityLogs()->latest()->take(6)->get();

        return view('profile.index', [
            'user' => $user,
            'activities' => $activities,
        ]);
    }

    public function updateProfile(UpdateProfileRequest $request)
    {
        $user = Auth::user();
        $data = $request->validated();

        // Cập nhật username và email
        $user->username = $data['username'];
        $user->email = $data['email'];

        // Nếu có mật khẩu mới, cập nhật
        if (!empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        // Xử lý upload avatar
        if ($request->hasFile('avatar')) {
            // Xoá ảnh cũ nếu có
            if ($user->avatar_path && Storage::disk('public')->exists($user->avatar_path)) {
                Storage::disk('public')->delete($user->avatar_path);
            }

            // Lưu ảnh mới
            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar_path = $path;
        }

        $user->save();

        return back()->with('success', 'Hồ sơ đã được cập nhật');
    }

    public function updatePassword(UpdateProfileRequest $request)
    {
        $user = Auth::user();

        // Kiểm tra mật khẩu hiện tại
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Mật khẩu hiện tại không đúng']);
        }

        // Cập nhật mật khẩu mới
        $user->password = Hash::make($request->password);
        $user->save();

        return back()->with('success', 'Mật khẩu đã được cập nhật');
    }
}
