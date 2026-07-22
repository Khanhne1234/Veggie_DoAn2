<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    public function ShowFormAddCategory()
    {
        return view('admin.pages.categories-add');
    }
    public function AddCategory(Request $request)
    {
        $request->validate([
            'category-name' => 'required|string|max:255',
            'category-description' => 'nullable|string',
        ]);
        $imagePath = null;
        if ($request->hasFile('category-image')) {
            $file = $request->file('category-image');
            $fileName = now()->timestamp . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $imagePath = $file->storeAs('uploads/categories', $fileName, 'public');
        }
        Category::create([
            'name' => $request->input('category-name'),
            'slug' => Str::slug($request->input('category-name')),
            'description' => $request->input('category-description'),
            'image' => $imagePath,
        ]);
        return redirect()->route('admin.categories.add')->with('toastr', ['type' => 'success', 'message' => 'Danh mục đã được thêm thành công!']);
    }
    public function index()
    {
        $categories = Category::all();
        return view('admin.pages.categories', compact('categories'));
    }
    public function UpdateCategory(Request $request)
    {
        try {
            $category = Category::findOrFail($request->input('category_id'));
            if (!$category) {
                return response()->json([
                    'success' => false,
                    'message' => 'Danh mục không tồn tại',
                ], 404);
            }
            $category->name = $request->input('category-name');
            $category->description = $request->input('category-description');
            if ($request->hasFile("category-image")) {
                if ($category->image) {
                    //xóa ảnh cũ
                    Storage::disk('public')->delete($category->image);
                }
                //tạo ảnh mới
                $file = $request->file("category-image");
                $fileName = now()->timestamp . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $imagePath = $file->storeAs('uploads/categories', $fileName, 'public');
                $category->image = $imagePath;
            }
            $category->save();
            return response()->json([
                'success' => true,
                'message' => 'Danh mục đã được cập nhật thành công!',
                'data' => [
                    'id' => $category->id,
                    'name' => $category->name,
                    'description' => $category->description,
                    'image' => $category->image ? asset('storage/' . $category->image) : null,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Đã có lỗi xảy ra',
            ], 500);
        }
    }
    public function DeleteCategory(Request $request)
    {
        try {
            $category = Category::findOrFail($request->input('category_id'));
            if (!$category) {
                return response()->json([
                    'success' => false,
                    'message' => 'Danh mục không tồn tại',
                ], 404);
            }
            if ($category->image) {
                //xóa ảnh cũ
                Storage::disk('public')->delete($category->image);
            }
            $category->delete();
            return response()->json([
                'success' => true,
                'message' => 'Danh mục đã được xóa thành công!',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Đã có lỗi xảy ra',
            ], 500);
        }
    }
}
