<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;

class ApiProductController extends Controller
{
    public function index(Request $request)
    {
        $categoryId = $request->query('category_id');
        
        if ($categoryId) {
            $products = Product::where('category_id', $categoryId)->get();
        } else {
            $products = Product::all();
        }

        return response()->json($products);
    }

    // Get products by category
    public function getByCategory(Category $category)
    {
        $products = $category->products;
        return response()->json($products);
    }

}
