<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attribute extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'type', 'category_id'];

    public function values()
    {
        return $this->hasMany(AttributeValue::class);
    }


    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_attributes')
                    ->withPivot('value');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

}
