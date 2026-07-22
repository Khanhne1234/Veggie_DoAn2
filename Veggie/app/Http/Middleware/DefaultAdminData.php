<?php

namespace App\Http\Middleware;

use App\Models\Contact;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Notification;

class DefaultAdminData
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::guard('admin')->user();
        if ($user) {
            $message = Contact::where('is_replied', 0)->latest()->get();
            $notifications = Notification::where('is_read', 0)->latest('created_at')->get();

        } else {
            $message = [];
            $notifications = [];
        }
        view()->share('messages', $message);
        view()->share('user', $user);
        view()->share('notifications', $notifications);
        return $next($request);
    }
}
