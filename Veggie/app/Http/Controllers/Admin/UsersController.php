<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('role');

        // Tìm kiếm theo từ khóa (tên, email, số điện thoại)
        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'LIKE', "%{$keyword}%")
                  ->orWhere('email', 'LIKE', "%{$keyword}%")
                  ->orWhere('phone_number', 'LIKE', "%{$keyword}%");
            });
        }

        // Lọc theo vai trò
        if ($request->filled('role_id')) {
            $query->where('role_id', $request->role_id);
        }

        // Lọc theo trạng thái
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $users = $query->paginate(6)->appends($request->query());
        $roles = Role::all();

        return view('admin.pages.users', compact('users', 'roles'));
    }
    public function upgrade(Request $request)
    {
        $userId = $request->user_id;
        $user = User::find($userId);
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy người dùng']);
        }
        $user->role_id = 2;
        $user->save();
        return response()->json(['success' => true, 'message' => 'Đã nâng cấp người dùng thành công']);
    }
    public function updateStatus(Request $request)
    {
        $userId = $request->user_id;
        $status = $request->status;
        $user = User::find($userId);
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy người dùng']);
        }
        $user->status = $status;
        $user->save();
        return response()->json(['success' => true, 'message' => 'Đã cập nhật trạng thái người dùng thành công']);
    }
}
