<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ProductImage extends Model
{
    use HasFactory;

    protected $fillable = ['product_id', 'image_url', 'order'];

    // Определение отношения "многие к одному" с моделью Product
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public static function boot()
    {
        parent::boot();

        static::deleting(function ($productImage) {
            // Удаление файла изображения из папки storage/app/public/product_images
            Storage::disk('public')->delete($productImage->image_url);
        });
    }

}
