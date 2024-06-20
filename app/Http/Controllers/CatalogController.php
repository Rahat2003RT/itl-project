<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::with('children')->whereNull('parent_id')->get();
        $products = Product::with('brand', 'categories', 'images')->paginate(20);

        return view('catalog.index', compact('categories', 'products'));
    }

    public function filter(Request $request)
    {
        $query = Product::with('brand', 'categories', 'images');

        // Фильтрация по категории
        if ($request->has('category_id')) {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->where('id', $request->category_id);
            });
        }

        // Фильтрация по цене
        if ($request->has('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->has('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // Сортировка
        if ($request->has('sort')) {
            switch ($request->sort) {
                case 'price_asc':
                    $query->orderBy('price', 'asc');
                    break;
                case 'price_desc':
                    $query->orderBy('price', 'desc');
                    break;
                case 'popularity':
                    $query->orderBy('popularity', 'desc'); // Убедитесь, что у вас есть столбец 'popularity'
                    break;
                case 'date':
                    $query->orderBy('created_at', 'desc');
                    break;
                default:
                    $query->orderBy('created_at', 'desc');
            }
        }

        $categories = Category::with('children')->whereNull('parent_id')->get();
        $products = $query->paginate(20);

        return view('catalog.index', compact('categories', 'products'));
    }
}
