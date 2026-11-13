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
    public function updateRole(Request $request)
    {
        $data = $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'role' => ['required', 'in:staff,customer'],
        ]);

        $user = User::with('role')->findOrFail($data['user_id']);
        if ($user->role?->name === 'admin' || $user->id === $request->user('admin')?->id) {
            return response()->json(['success' => false, 'message' => 'Không thể đổi vai trò tài khoản quản trị.'], 403);
        }
        if (!in_array($user->role?->name, ['customer', 'staff'], true) || $user->status === 'deleted') {
            return response()->json(['success' => false, 'message' => 'Tài khoản không thể đổi vai trò lúc này.'], 422);
        }

        $role = Role::where('name', $data['role'])->firstOrFail();
        $user->update(['role_id' => $role->id]);

        return response()->json([
            'success' => true,
            'message' => $role->name === 'staff' ? 'Đã nâng lên nhân viên.' : 'Đã hạ xuống khách hàng.',
        ]);
    }
    public function updateStatus(Request $request)
    {
        $data = $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'status' => ['required', 'in:active,banned,deleted'],
        ]);

        $user = User::with('role')->findOrFail($data['user_id']);
        if ($user->role?->name === 'admin' || $user->id === $request->user('admin')?->id) {
            return response()->json(['success' => false, 'message' => 'Không thể thay đổi trạng thái tài khoản quản trị.'], 403);
        }
        if (!in_array($user->role?->name, ['customer', 'staff'], true)) {
            return response()->json(['success' => false, 'message' => 'Vai trò tài khoản không hợp lệ.'], 422);
        }

        $user->update(['status' => $data['status']]);

        return response()->json(['success' => true, 'message' => 'Đã cập nhật trạng thái người dùng.']);
    }
}
