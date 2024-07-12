<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Favorite;
use App\Models\Product;


class FavoriteController extends Controller
{
    public function index()
    {
        // Получаем избранные товары текущего пользователя
        $favorites = Favorite::where('user_id', auth()->id())
                             ->orderByDesc('created_at')
                             ->get();

        // Загружаем товары из связанных моделей с нужными параметрами
        $products = $favorites->map(function ($favorite) {
            return Product::with([
                'category',
                'brand',
                'images' => function ($query) {
                    $query->orderBy('order', 'asc');
                }
            ])->find($favorite->product_id);
        });

        return view('favorites.index', compact('products'));
    }
}
