<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'price'];

    // Продукт может принадлежать многим категориям
    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    // Продукт может иметь много изображений
    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    // Продукт может иметь много отзывов
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
}
