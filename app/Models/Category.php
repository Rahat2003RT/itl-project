<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'parent_id'];

    // Категория может иметь много продуктов
    public function products()
    {
        return $this->belongsToMany(Product::class);
    }

    // Категория может иметь подкатегории
    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    // Категория может принадлежать родительской категории
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    // Получить только родительские категории
    public static function getParentCategories()
    {
        return self::whereNull('parent_id')->get();
    }

    // Получить только дочерние категории
    public static function getChildCategories()
    {
        return self::whereNotNull('parent_id')->get();
    }
}
