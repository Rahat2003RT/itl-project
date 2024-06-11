<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = ['product_id', 'user_id', 'rating', 'comment'];

    // Определение отношения "многие к одному" с моделью Product
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Определение отношения "многие к одному" с моделью User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
