<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfNotAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->is('admin') || $request->is('admin/*')) {
            if (!Auth::guard('admin')->check()) {
                return redirect()->route('admin.login')->with('toastr', [
                    'type' => 'error',
                    'message' => 'Vui lòng đăng nhập để truy cập trang quản trị',
                ]);
            }
            $admin = Auth::guard('admin')->user()->fresh(['role']);
            if (!$admin || !in_array($admin->role?->name, ['admin', 'staff'], true)
                || in_array($admin->status, ['banned', 'deleted', 'suspended'], true)) {
                Auth::guard('admin')->logout();
                return redirect()->route('admin.login')->with('toastr', [
                    'type' => 'error',
                    'message' => 'Tài khoản không còn quyền truy cập trang quản trị.',
                ]);
            }
        } elseif (!Auth::guard('web')->check()) {
            return redirect()->route('login')->with('toastr', [
                'type' => 'error',
                'message' => 'Vui lòng đăng nhập để truy cập trang này',
            ]);
        }
        return $next($request);
    }
}
