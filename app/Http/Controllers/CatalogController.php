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
    
            $minProductPrice = Product::whereIn('category_id', $categoryIds)->min('price');
            $maxProductPrice = Product::whereIn('category_id', $categoryIds)->max('price');
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
            $query->whereIn('category_id', $categoryIds);
        }
    
        // Фильтруем продукты по цене
        $query->whereBetween('price', [$minPrice, $maxPrice]);
    
        // Получаем выбранные бренды из запроса
        $selectedBrands = $request->input('brands', []);
        if (!empty($selectedBrands)) {
            $query->whereIn('brand_id', $selectedBrands);
        }
    
        // Фильтр по атрибутам
        $selectedAttributes = $request->input('attributes', []);

        if (!empty($selectedAttributes)) {
            $query->whereHas('attributes', function ($query) use ($selectedAttributes) {
                foreach ($selectedAttributes as $attributeId => $values) {
                    $query->where(function ($query) use ($attributeId, $values) {
                        $query->where('attribute_id', $attributeId)
                              ->whereIn('attribute_value_id', $values);
                    });
                }
            });
        }
    
        // Поиск по ключевому слову
        $searchTerm = $request->input('q');
        if ($searchTerm) {
            $query->where(function ($query) use ($searchTerm) {
                $query->where('name', 'like', '%' . $searchTerm . '%')
                      ->orWhere('description', 'like', '%' . $searchTerm . '%');
            });
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
                case 'popularity':
                    $query->orderBy('popularity', 'desc');
                    break;
                default:
                    $query->orderBy('created_at', 'desc');
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }
    
        // Получаем список всех брендов с количеством продуктов по каждому бренду
        $brands = Brand::withCount(['products' => function ($query) use ($categoryIds) {
            if (!empty($categoryIds)) {
                $query->whereIn('category_id', $categoryIds);
            }
        }])->get();
    
        // Получаем список продуктов с учетом всех фильтров и пагинируем их
        $products = $query->paginate(12);
    
        // Получаем список всех атрибутов и их значений для фильтрации
        if ($category_name !== null) {
            $attributes = Attribute::where('category_id', $category->id)
                                    ->with('values')
                                    ->get();
        } else {
            $attributes = collect();
        }
    
        // Возвращаем представление с результатами
        return view('catalog.index', compact('categories', 'products', 'minProductPrice', 'maxProductPrice', 'minPrice', 'maxPrice', 'category_name', 'brands', 'attributes', 'selectedBrands', 'selectedAttributes', 'searchTerm'));
    }
    
    

    
    
    public function show($product_id)
    {
        $product = Product::with([
            'images', 
            'brand', 
            'reviews.user', 
            'category.parent',
            'attributes' => function ($query) {
                $query->orderBy('type', 'asc'); // Сортируем атрибуты по типу
            },
            'attributes.values'
        ])->findOrFail($product_id);

        $groupedAttributes = $product->attributes->groupBy('type');
        $generalAttributes = $groupedAttributes->get('Общая характеристика');
        $technicalAttributes = $groupedAttributes->get('Дополнительная характеристика');
        $additionalAttributes = $groupedAttributes->get('Техническая характеристика');
    
        $categoryPath = null;
        if ($product->category && $product->category->parent) {
            $categoryPath = $product->category->getPath();
        }

        $relatedProducts = Product::whereHas('collections', function ($query) use ($product) {
            $query->whereIn('collections.id', $product->collections->pluck('id'));
        })
        ->where('id', '!=', $product->id)
        ->take(5)
        ->get();
        
    
        return view('catalog.show', compact('product', 'categoryPath', 'generalAttributes', 'technicalAttributes', 'additionalAttributes', 'relatedProducts'));
    }

}
