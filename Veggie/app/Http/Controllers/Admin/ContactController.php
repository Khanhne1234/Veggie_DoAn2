<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index()
    {
        $contacts = Contact::orderBy('is_replied', 'asc')->orderBy('created_at', 'desc')->get();
        return view('admin.pages.contact', compact('contacts'));
    }

    public function reply(Request $request)
    {
        $id = $request->contact_id;
        $email = $request->email;
        $messageContent = $request->message;
        try {
            Mail::send('admin.email.contact_reply', ['messageContent' => $messageContent], function ($mail) use ($email) {
                $mail->to($email);
                $mail->subject('Phản hồi liên hệ từ Veggie Store');
            });
            Contact::where('id', $id)->update(['is_replied' => 1]);
            return response()->json([
                'success' => true,
                'message' => 'Email đã được gửi thành công'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Đã có lỗi xảy ra'
            ]);
        }
    }
}
