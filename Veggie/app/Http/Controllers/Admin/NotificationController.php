<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Notification;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Notification::where('is_read', 0)->latest('created_at')->get();
        foreach ($notifications as $noti) {
            if ($noti->type == 'order') {
                $noti->title = 'Thông báo đơn hàng mới';
            } else if ($noti->type == 'contact') {
                $noti->title = 'Thông báo liên hệ mới';
            } else if ($noti->type == 'wishlist') {
                $noti->title = 'Thông báo yêu thích mới';
            } else if ($noti->type == 'comment') {
                $noti->title = 'Thông báo bình luận mới';
            }
        }
        return view('admin.pages.notifications', compact('notifications'));
    }
    public function updateNotification(Request $request)
    {
        $notification = Notification::find($request->id);
        $notification->is_read = 1;
        $notification->save();
        return response()->json([
            'status' => true,
            'message' => 'Đã cập nhật thông báo',
        ]);
    }
}
