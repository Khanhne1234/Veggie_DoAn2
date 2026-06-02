<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\PasswordReset;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class ResetPasswordController extends Controller
{
    public function showForgotPassword()
    {
        return view('clients.auth.forgot-passwod');
    }

    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ]);

        try {
            $user = User::where('email', $request->email)->first();

            if (!$user) {
                return back()->withErrors(['email' => 'Email không tồn tại trong hệ thống.']);
            }

            $token = Str::random(64);
            $tokenTable = config('auth.passwords.users.table', 'password_reset_tokens');

            DB::table($tokenTable)->updateOrInsert(
                ['email' => $user->email],
                [
                    'token' => Hash::make($token),
                    'created_at' => now(),
                ]
            );

            $resetUrl = route('password.reset', [
                'token' => $token,
                'email' => $user->email,
            ]);

            $mailer = app()->environment('local') ? 'log' : config('mail.default');

            Mail::mailer($mailer)->send('emails.reset-password', [
                'user' => $user,
                'resetUrl' => $resetUrl,
            ], function ($message) use ($user) {
                $message->to($user->email)
                    ->subject('Đặt lại mật khẩu cho tài khoản Veggie');
            });

            return back()->with('status', 'Đã gửi liên kết đặt lại mật khẩu đến email của bạn.');
        } catch (\Throwable $throwable) {
            report($throwable);

            return back()->withErrors(['email' => 'Không thể gửi liên kết đặt lại mật khẩu. Vui lòng thử lại.']);
        }
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
        $request->validate(
            [
                'token' => ['required'],
                'email' => ['required', 'email', 'exists:users,email'],
                'password' => ['required', 'confirmed', 'min:6'],
            ],
            [
                'email.required' => 'Vui lòng nhập email.',
                'email.email' => 'Định dạng email không hợp lệ.',
                'email.exists' => 'Email không tồn tại trong hệ thống.',
                'password.required' => 'Vui lòng nhập mật khẩu.',
                'password.confirmed' => 'Xác nhận mật khẩu không khớp.',
                'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự.',
                'token.required' => 'Mã token không hợp lệ.',
            ]
        );
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
            toastr()->success('Mật khẩu đã được đặt lại thành công. Bạn có thể đăng nhập với mật khẩu mới.');
            return redirect()->route('login');
        }
        toastr()->error('Không thể đặt lại mật khẩu. Vui lòng thử lại.');
        return back()->withInput();
    }
}
