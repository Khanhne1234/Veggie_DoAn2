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
        } elseif (!Auth::guard('web')->check()) {
            return redirect()->route('login')->with('toastr', [
                'type' => 'error',
                'message' => 'Vui lòng đăng nhập để truy cập trang này',
            ]);
        }
        return $next($request);
    }
}
