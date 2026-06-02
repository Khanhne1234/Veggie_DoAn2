<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class AdminAuthController extends Controller
{
    public function ShowLoginForm(Request $request)
    {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return view('admin.pages.login');
    }
    public function Login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);
        if (Auth::guard('admin')->attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::guard('admin')->user();
            //check permission
            if ($user->role && in_array($user->role->name, ['admin', 'staff'])) { //only allow admin and manager
                $request->session()->regenerate();
                return redirect()->intended(route('admin.dashboard'))->with('toastr', [
                    'type' => 'success',
                    'message' => 'Đăng nhập admin thành công',
                ]);
            }
            Auth::guard('admin')->logout();
            return back()->with('toastr', [
                'type' => 'error',
                'message' => 'Bạn không có quyền truy cập admin',
            ]);
        }
        return back()->withInput()->with('toastr', [
            'type' => 'error',
            'message' => 'Email hoặc mật khẩu không đúng',
        ]);
    }
}
