<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class ForgotPasswordController extends Controller
{
    public function ShowforgotPassword()
    {
        return view('clients.auth.forgot-passwod');
    }

    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ]);

        $status = Password::sendResetLink($request->only('email'));

        if ($status === Password::RESET_LINK_SENT) {
            return back()->with('toastr', [
                'type' => 'success',
                'message' => 'Đã gửi liên kết đặt lại mật khẩu đến email của bạn.',
            ]);
        }

        return back()->with('toastr', [
            'type' => 'error',
            'message' => 'Không thể gửi liên kết đặt lại mật khẩu. Vui lòng thử lại.',
        ]);
    }

    public function ShowresetPassword(Request $request, $token)
    {
        return view('clients.auth.reset-password', [
            'token' => $token,
            'email' => $request->query('email'),
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email', 'exists:users,email'],
            'password' => ['required', 'confirmed', 'min:6'],
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect()->route('login')->with('toastr', [
                'type' => 'success',
                'message' => 'Đổi mật khẩu thành công!',
            ]);
        }

        return back()->withErrors(['email' => 'Đổi mật khẩu không thành công. Vui lòng kiểm tra lại thông tin.']);
    }
}
