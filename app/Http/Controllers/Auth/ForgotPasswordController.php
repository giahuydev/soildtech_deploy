<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    // Hiện form nhập email
    public function showLinkRequestForm()
    {
        return view('auth.forgot_password');
    }

    // Gửi email reset password
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? back()->with('success', 'Link đặt lại mật khẩu đã được gửi đến email của bạn.')
            : back()->withErrors(['email' => __($status)]);
    }

    // Hiện form nhập mật khẩu mới
    public function showResetForm(Request $request, $token)
    {
        // FIX LỖI VIEW: Gọi đúng tên file view trong thư mục auth/
        // Dựa trên cây thư mục: resources/views/auth/reset_password.blade.php
        return view('auth.reset_password', [
            'token' => $token,
            'email' => $request->email,
        ]);
    }

    // Submit đặt mật khẩu mới
    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:6|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) use ($request) {
                $user->password = bcrypt($request->password);
                $user->save();
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('success', 'Đặt lại mật khẩu thành công!')
            : back()->withErrors(['email' => __($status)]);
    }
}