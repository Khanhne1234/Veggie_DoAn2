<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class AccountController extends Controller
{
    public function index()
    {
        $user = Auth::guard('admin')->user();
        return view('admin.pages.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        /** @var User $user */
        $user = Auth::guard('admin')->user();
        if ($request->type === "profile") {
            $request->validate([
                'name' => 'required|string|min:3',
                'email' => 'required',
                'phone' => 'nullable|string|max:15',
                'address' => 'required|string|min:3',
            ]);
            $user->update([
                'name' => $request->name,
                'email' => $request->email,
                'phone_number' => $request->phone,
                'address' => $request->address,
            ]);
            return response()->json(['status' => true, 'message' => 'Cập nhật thông tin thành công']);
        } elseif ($request->type === "password") {
            $request->validate([
                'old_password' => 'required|string',
                'new_password' => 'required|string|min:6',
                'password_confirmation' => 'required|string|min:6',
            ]);
            if (!Hash::check($request->old_password, $user->password)) {
                return response()->json(['status' => false, 'message' => 'Sai mật khẩu cũ']);
            }
            if ($request->new_password !== $request->password_confirmation) {
                return response()->json(['status' => false, 'message' => 'Mật khẩu mới không khớp']);
            }
            $user->update([
                "password" => Hash::make($request->new_password)
            ]);
            return response()->json(['status' => true, 'message' => 'Cập nhật mật khẩu thành công']);
        } elseif ($request->type === "avatar" && $request->hasFile('avatar')) {
            //delete old_avatar
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
            //add new_avatar
            $file = $request->file("avatar");
            $fileName = now()->timestamp . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $imagePath = $file->storeAs('uploads/users', $fileName, 'public');
            $user->avatar = $imagePath;
            $user->save();
            return response()->json([
                'status' => true,
                'message' => 'Cập nhật ảnh đại diện thành công!',
                'data' => [
                    'id' => $user->id,
                    'avatar' => $user->avatar ? asset('storage/' . $user->avatar) : null,
                ]
            ]);
        }
        return response()->json(['status' => false, 'message' => 'Có lỗi xảy ra']);
    }
}
