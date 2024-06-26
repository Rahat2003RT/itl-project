<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Attribute;
use Illuminate\Http\Request;

class CatalogController extends Controller
{

    public function index(Request $request, $category_name = null)
    {
        // Получаем все категории для навигации
        $categories = Category::with('children')->whereNull('parent_id')->get();
    
        // Инициализируем переменные для минимальной и максимальной цен продуктов
        $minProductPrice = Product::min('price');
        $maxProductPrice = Product::max('price');
    
        // Массив ID категорий для фильтрации продуктов
        $categoryIds = [];
    
        if ($category_name) {
            $category = Category::where('name', $category_name)->firstOrFail();
            $categoryIds = $category->getDescendantsAndSelf()->pluck('id')->toArray();
    
            $minProductPrice = Product::whereHas('categories', function ($query) use ($categoryIds) {
                $query->whereIn('categories.id', $categoryIds);
            })->min('price');
    
            $maxProductPrice = Product::whereHas('categories', function ($query) use ($categoryIds) {
                $query->whereIn('categories.id', $categoryIds);
            })->max('price');
        }
    
        // Получаем значение минимальной и максимальной цены из запроса, учитывая ограничения
        $minPrice = $request->input('min_price', $minProductPrice);
        $maxPrice = $request->input('max_price', $maxProductPrice);
    
        $minPrice = max($minPrice, $minProductPrice);
        $maxPrice = min($maxPrice, $maxProductPrice);
    
        if ($maxPrice < $minPrice) {
            $maxPrice = $minPrice;
        }
    
        // Формируем базовый запрос продуктов
        $query = Product::query();
    
        // Если указана категория, фильтруем продукты по ней и её потомкам
        if ($category_name) {
            $query->whereHas('categories', function ($query) use ($categoryIds) {
                $query->whereIn('categories.id', $categoryIds);
            });
        }
    
        // Фильтруем продукты по цене
        $query->whereBetween('price', [$minPrice, $maxPrice]);
    
        // Получаем выбранные бренды из запроса
        $selectedBrands = $request->input('brands', []);
        if (!empty($selectedBrands)) {
            $query->whereIn('brand_id', $selectedBrands);
        }
    
        // Применяем сортировку
        if ($request->has('sort')) {
            switch ($request->sort) {
                case 'price_asc':
                    $query->orderBy('price', 'asc');
                    break;
                case 'price_desc':
                    $query->orderBy('price', 'desc');
                    break;
                // Другие варианты сортировки, если необходимо
                default:
                    $query->orderBy('created_at', 'desc');
            }
        } else {
            // Сортировка по умолчанию
            $query->orderBy('created_at', 'desc');
        }
    
        // Получаем список всех брендов с количеством продуктов по каждому бренду
        $brands = Brand::withCount(['products' => function ($query) use ($categoryIds) {
            if (!empty($categoryIds)) {
                $query->whereHas('categories', function ($query) use ($categoryIds) {
                    $query->whereIn('categories.id', $categoryIds);
                });
            }
        }])->get();
    
        // Пагинируем результаты запроса (по 12 продуктов на странице)
        $products = $query->paginate(12);

        // $attributes = [];
        // if ($category_name === 'мониторы') {
        //     $attributes = Attribute::whereIn('name', ['размер монитора', 'частота кадров'])->get();
        // }

        // // Применяем фильтры для атрибутов
        // foreach ($attributes as $attribute) {
        //     if ($request->has($attribute->name)) {
        //         $query->whereHas('attributes', function ($q) use ($request, $attribute) {
        //             $q->where('attributes.id', $attribute->id)
        //             ->where('product_attributes.value', $request->input($attribute->name));
        //         });
        //     }
        // }
    
        // Возвращаем представление с передачей необходимых данных
        return view('catalog.index', compact('categories', 'products', 'minProductPrice', 'maxProductPrice', 'minPrice', 'maxPrice', 'category_name', 'brands', 'selectedBrands', 'attributes'));
    }
    
    
    public function show($product_id)
    {
        $product = Product::with(['images', 'brand', 'reviews.user', 'categories.parent'])->findOrFail($product_id);

        $categoryPath = $product->categories->first()->getPath();

        return view('catalog.show', compact('product', 'categoryPath'));
    }


}
