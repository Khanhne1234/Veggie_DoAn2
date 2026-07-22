<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckPermisson
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, $permission)
    {
        $user = Auth::guard('admin')->user();

        //kiem tra quyen su dung permission
        if(!$user || !$user->role->permissions->contains('name', $permission)){
            abort(403, 'Bạn không có quyền truy cập tính năng này');
        }
        return $next($request);
    }
}
