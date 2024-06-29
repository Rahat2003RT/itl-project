<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Product extends Model
{
    use HasFactory;

    use Searchable;

    protected $fillable = ['name', 'description', 'price', 'category_id', 'brand_id', 'created_by'];

    public function toSearchableArray()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            // Добавьте другие поля, которые вы хотите индексировать
        ];
    }


    // Продукт может принадлежать многим категориям
    public function category()
    {
        return $this->belongsTo(Category::class);
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

    public function averageRating()
    {
        $totalReviews = $this->reviews()->count();
        if ($totalReviews === 0) {
            return 0;
        }

        $totalRating = $this->reviews()->sum('rating');
        return $totalRating / $totalReviews;
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function attributes()
    {
        return $this->belongsToMany(Attribute::class, 'product_attributes')
                    ->withPivot('attribute_value_id');
    }
    
    public function collections()
    {
        return $this->belongsToMany(Collection::class, 'collection_product');
    }
}
