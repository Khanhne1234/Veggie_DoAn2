<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\Controller;
use App\Mail\ActivationMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

use function Flasher\Toastr\Prime\toastr;

class AuthController extends Controller
{
    public function Showregister()
    {
        return view('clients.pages.register');
    }

    public function register(Request $request)
    {
        $request->validate(
            [
                'name' => 'required|string|max:255',
                'email' => 'required|string|max:255|email|unique:users',
                'password' => 'required|min:6',
            ],
            [
                'name.required' => 'Name là bắt buộc',
                'email.required' => 'Email là bắt buộc',
                'email.email' => 'Email phải là một địa chỉ email hợp lệ',
                'email.unique' => 'Email đã tồn tại',
                'password.required' => 'Password là bắt buộc',
                'password.min' => 'Password phải có ít nhất 6 ký tự',
            ]
        );

        $existEmail = User::where('email', $request->email)->first();
        if ($existEmail) {
            if ($existEmail->isPending()) {
                return redirect()->route('login')->with('toastr', [
                    'type' => 'warning',
                    'message' => 'Email đã tồn tại và đang chờ duyệt!',
                ]);
            }

            return redirect()->route('login')->with('toastr', [
                'type' => 'error',
                'message' => 'Email đã tồn tại!',
            ]);
        }

        $token = Str::random(64);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'status' => 'pending',
            'role_id' => 3,
            'activation_token' => $token,
        ]);

        Mail::to($user->email)->send(new ActivationMail($token, $user));

        return redirect()->route('login')->with('toastr', [
            'type' => 'success',
            'message' => 'Đăng ký thành công! Vui lòng chờ quản trị viên duyệt tài khoản của bạn.',
        ]);
    }

    public function activate($token)
    {
        $user = User::where('activation_token', $token)->first();

        if (!$user) {
            return redirect()->route('login')->with('toastr', [
                'type' => 'error',
                'message' => 'Token không hợp lệ!',
            ]);
        }

        $user->status = 'active';
        $user->activation_token = null;
        $user->save();

        return redirect()->route('login')->with('toastr', [
            'type' => 'success',
            'message' => 'Tài khoản đã được kích hoạt thành công!',
        ]);
    }

    public function Showlogin()
    {
        return view('clients.pages.login');
    }

    public function login(Request $request)
    {
        $request->validate(
            [
                'email' => 'required|email',
                'password' => 'required',
            ],
            [
                'email.required' => 'Email là bắt buộc',
                'email.email' => 'Email phải là một địa chỉ email hợp lệ',
                'password.required' => 'Password là bắt buộc',
            ]
        );

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password, 'status' => 'active'])) {
            if (in_array(Auth::user()->role->name, ['customer'])) {
                $request->session()->regenerate();
                return redirect()->route('home')->with('toastr', [
                    'type' => 'success',
                    'message' => 'Đăng nhập thành công!',
                ]);
            }

            Auth::logout();

            return redirect()->route('login')->with('toastr', [
                'type' => 'error',
                'message' => 'Tài khoản của bạn không có quyền truy cập!',
            ]);
        }

        return redirect()->route('login')->with('toastr', [
            'type' => 'error',
            'message' => 'Thông tin đăng nhập không đúng hoặc tài khoản chưa được kích hoạt!',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')->with('toastr', [
            'type' => 'success',
            'message' => 'Đăng xuất thành công!',
        ]);
    }
}
