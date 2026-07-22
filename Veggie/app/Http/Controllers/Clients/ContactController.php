<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Contact;
use App\Models\Notification;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;

class ContactController extends Controller
{
    public function index()
    {
        return view('clients.pages.contact');
    }

    public function sendContact(Request $request)
    {
        $request->validate(
            [
                'name' => 'required|string|max:255',
                'email' => 'required|string|max:255|email',
                'phone' => 'required|string|max:20',
            ],
            [
                'name.required' => 'Name là bắt buộc',
                'email.required' => 'Email là bắt buộc',
                'phone.required' => 'Số điện thoại là bắt buộc',
            ]
        );
        try {
            Contact::create([
                'full_name' => $request->name,
                'phone_number' => $request->phone,
                'email' => $request->email,
                'message' => $request->message,
                'is_replied' => false,
            ]);
        } catch (QueryException $e) {
            // Handle duplicate/DB errors gracefully
            $message = 'Lưu liên hệ thất bại.';
            //Lỗi 1062 là lỗi trùng lặp, có thể do email hoặc số điện thoại đã tồn tại
            if (isset($e->errorInfo[1]) && $e->errorInfo[1] == 1062) {
                $message = 'Email hoặc số điện thoại đã tồn tại.';
            }

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['message' => $message], 422);
            }

            return redirect()->back()->with('error', $message);
        }
        $user = Auth::user();
        if (Auth::check()) {
            Notification::create([
                'user_id' => $user->id,
                'type' => 'contact',
                'message' => 'Có liên hệ mới từ ' . $request->email,
                'link' => '/contact',
                'is_read' => false,
            ]);
        } else {
            Notification::create([
                'user_id' => null,
                'type' => 'contact',
                'message' => 'Có liên hệ mới từ ' . $request->email,
                'link' => '/contact',
                'is_read' => false,
            ]);
        }

        // nếu gửi thành công, trả về phản hồi phù hợp
        if ($request->ajax() || $request->wantsJson()) {
            return response('Liên hệ đã được gửi thành công!', 200);
        }
        return redirect()->back()->with('success', 'Liên hệ đã được gửi thành công!');
    }
}
