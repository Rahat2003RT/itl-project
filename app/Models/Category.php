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
        return $this->hasMany(Product::class, 'category_id');
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

    public function isDescendantOf(Category $category)
    {
        $parent = $this->parent;

        while ($parent) {
            if ($parent->id === $category->id) {
                return true;
            }
            $parent = $parent->parent;
        }

        return false;
    }

    public function descendants()
    {
        return $this->children()->with('descendants');
    }

    public function getDescendantsAndSelf()
    {
        $categories = collect([$this]);
        foreach ($this->descendants as $descendant) {
            $categories = $categories->merge($descendant->getDescendantsAndSelf());
        }
        return $categories;
    }

    public function countProducts()
    {
        $count = $this->products()->count();
        foreach ($this->children as $child) {
            $count += $child->countProducts();
        }
        return $count;
    }

    public function getPath()
    {
        $path = collect([]);
        $category = $this;

        while ($category) {
            $path->prepend($category);
            $category = $category->parent;
        }

        return $path;
    }

    public function attributes()
    {
        return $this->hasMany(Attribute::class);
    }
    

}
