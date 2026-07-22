<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class ProductController extends Controller
{
    public function ShowFormAddProduct()
    {
        $categories = Category::all();
        return view('admin.pages.product-add', compact('categories'));
    }
    public function AddProduct(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required',
            'description' => 'required|string|max:255',
            'price' => 'required|numeric|min:0|max:99999999',
            'stock' => 'required|integer|min:0',
            'unit' => 'required|string|max:255',
            'images' => 'required|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
        ]);
        $slug = Str::slug($request->name) . '-' . time();
        //Create Product
        $product = Product::create([
            'name' => $request->name,
            'slug' => $slug,
            'category_id' => $request->category_id,
            'description' => $request->description,
            'price' => $request->price,
            'stock' => $request->stock ?? 0,
            'unit' => $request->unit ?? 'kg',
            'status' => 'in_stock',
        ]);
        //Handle Images
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $fileName = time() . '-' . uniqid() . '.' . $image->getClientOriginalExtension();
                $path = "upload/products/" . $fileName;
                $image->storeAs('upload/products', $fileName, 'public');
                ProductImage::create([
                    'product_id' => $product->id,
                    'image' => $path,
                ]);
            }
        }
        $product->save();
        return redirect()->route('admin.product.add')->with('success', 'Thêm sản phẩm thành công');
    }
    public function index()
    {
        $products = Product::with('category', 'images')->get();
        $categories = Category::all();
        return view('admin.pages.product', compact('products', 'categories'));
    }
    public function UpdateProduct(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:products,id',
            'name' => 'required|string|max:255',
            'category_id' => 'required',
            'description' => 'required|string|max:255',
            'price' => 'required|numeric|min:0|max:99999999',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
        ]);
        $product = Product::findOrFail($request->id);
        $product->update([
            'name' => $request->name,
            'category_id' => $request->category_id,
            'description' => $request->description,
            'price' => $request->price,
            'stock' => $request->stock ?? 0,
            'unit' => $request->unit ?? 'kg',
            'status' => 'in_stock',
        ]);
        //Handle Images
        if ($request->hasFile('images')) {
            $oldImage = ProductImage::where('product_id', $product->id)->get();
            foreach ($oldImage as $image) {
                Storage::disk('public')->delete($image->image);
            }
            //Delete oldImage record
            ProductImage::where('product_id', $product->id)->delete();
            foreach ($request->file('images') as $image) {
                $fileName = time() . '-' . uniqid() . '.' . $image->getClientOriginalExtension();
                $path = "upload/products/" . $fileName;
                $image->storeAs('upload/products', $fileName, 'public');
                ProductImage::create([
                    'product_id' => $product->id,
                    'image' => $path,
                ]);
            }
        }
        $product->save();
        return response()->json([
            'success' => true,
            'message' => 'Cập nhật sản phẩm thành công',
            'data' => [
                'id' => $product->id,
                'name' => $product->name,
                'slug' => $product->slug,
                'category_name' => $product->category->name,
                'description' => $product->description,
                'price' => $product->price,
                'stock' => $product->stock,
                'unit' => $product->unit,
                'status' => $product->status == 'in_stock' ? 'Còn hàng' : 'Hết hàng',
                'image_url' => $product->image_url,
                'images' => $product->images->map(fn($img) =>asset('storage/'.$img->image)),
            ],
        ]);
    }
    public function DeleteProduct(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);
        $product = Product::findOrFail($request->product_id);

        // Lấy tất cả ảnh liên quan trước khi xóa
        $images = ProductImage::where('product_id', $product->id)->get();

        // Xóa file ảnh khỏi storage
        foreach ($images as $image) {
            Storage::disk('public')->delete($image->image);
        }

        // Xóa record ảnh trong DB
        ProductImage::where('product_id', $product->id)->delete();

        // Xóa sản phẩm
        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'Xóa sản phẩm thành công',
        ]);
    }
}
