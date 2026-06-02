<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'slug', 'category_id', 'description', 'price', 'stock', 'status', 'unit'];

    protected $appends = ['average_rating', 'image_url'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }
    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }
    public function firstImage()
    {
        return $this->hasOne(ProductImage::class)->orderBy('id', 'asc');
    }
    public function getImageUrlAttribute()
    {
        return $this->firstImage?->image
            ? asset('storage/uploads/products/' . $this->firstImage->image)
            : asset('storage/uploads/products/default_product.webp');
    }
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function getAverageRatingAttribute()
    {
        return $this->reviews()->avg('rating') ?? 0;
    }
}
