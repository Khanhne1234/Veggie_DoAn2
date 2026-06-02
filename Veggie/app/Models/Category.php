<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Category extends Model
{
    protected $fillable = ['name','slug', 'description', 'image'];
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function getImageUrlAttribute()
    {
        return $this->image && Storage::disk('public')->exists($this->image)
            ? Storage::url($this->image)
            : asset('assets/clients/img/icons/icon-img/category-1.png');
    }
}
