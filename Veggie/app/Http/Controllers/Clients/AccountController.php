<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\Controller;
use App\Models\ShippingAddress;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;

class AccountController extends Controller
{
    public function index()
    {
        $user = User::findOrFail(Auth::id());
        $addresses = ShippingAddress::where('user_id', Auth::id())->get();
        $orders = Order::where('user_id', $user->id)->orderBy('created_at', 'desc')->get();
        return view('clients.pages.account', compact('user', 'addresses', 'orders'));
    }
    public function update(Request $request)
    {
        $request->validate([
            'ltn_name' => 'required|string|max:255',
            'ltn_phone' => 'required|string|max:20',
            'ltn_address' => 'required|string|max:255',
            'ltn_email' => 'required|email|max:255|unique:users,email,' . Auth::id(),
            'ltn_avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = User::findOrFail(Auth::id());
        $user->name = $request->input('ltn_name');
        $user->phone_number = $request->input('ltn_phone');
        $user->address = $request->input('ltn_address');

        // Handle avatar upload.
        if ($request->hasFile('ltn_avatar')) {
            // Xóa avatar cũ nếu có
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
            // Lưu avatar mới
            $file = $request->file('ltn_avatar');
            $filename = time() . '_' . uniqid() . '_' . $file->getClientOriginalName();
            // Lưu file vào thư mục 'avatars' trong storage/app/public
            $path = $file->storeAs('avatars', $filename, 'public');
            $user->avatar = $path;
        }

        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật thông tin tài khoản thành công.',
            'avatar' => asset('storage/' . $user->avatar) // Trả về URL của avatar mới nếu có,
        ]);
    }
    public function showChangePasswordForm()
    {
        return view('clients.pages.change-password');
    }
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);

        $userId = Auth::id();
        if (!$userId) {
            return redirect()->route('login');
        }

        $user = User::findOrFail($userId);

        // Kiểm tra mật khẩu hiện tại
        if (!Hash::check($request->current_password, $user->password)) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Mật khẩu hiện tại không đúng.',
                ], 422);
            }

            return back()->withErrors(['current_password' => 'Mật khẩu hiện tại không đúng.']);
        }

        // Cập nhật mật khẩu mới
        $user->password = Hash::make($request->new_password);
        $user->save();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Mật khẩu đã được đổi thành công.',
            ]);
        }

        return redirect()->route('account')->with('success', 'Mật khẩu đã được đổi thành công.');
    }

    public function addAddress(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'default' => 'nullable|boolean',
        ]);

        //if the new address is set as default, update the order address default = 0
        if ($request->boolean('default')) {
            ShippingAddress::where('user_id', Auth::id())->update(['default' => false]);
        }
        ShippingAddress::create([
            'user_id' => Auth::id(),
            'full_name' => $request->input('full_name'),
            'phone' => $request->input('phone'),
            'address' => $request->input('address'),
            'city' => $request->input('city'),
            'default' => $request->boolean('default'),
        ]);
        return response()->json(['success' => true, 'message' => 'Địa chỉ đã được thêm thành công.']);
    }

    public function updateAddress(Request $request, $id)
    {
        //find address
        $address = ShippingAddress::where('user_id', Auth::id())->findOrFail($id);

        //set all address default = 0 if the new address is set as default
        ShippingAddress::where('user_id', Auth::id())->update(['default' => false]);

        //update address set default = 1
        $address->update(['default' => true]);
        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Địa chỉ đã được cập nhật thành công.']);
        }

        return redirect()->route('account')->with('success', 'Đã đặt địa chỉ mặc định thành công.');
    }

    public function deleteAddress(Request $request, $id)
    {
        $address = ShippingAddress::where('user_id', Auth::id())->findOrFail($id);
        $address->delete();

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Địa chỉ đã được xóa thành công.']);
        }

        return redirect()->route('account')->with('success', 'Địa chỉ đã được xóa thành công.');
    }
}
