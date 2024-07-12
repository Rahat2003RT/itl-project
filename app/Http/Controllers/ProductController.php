<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Review;

use App\Models\Favorite;


use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{



    public function storeReview(Request $request, $productId)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|max:500'
        ]);
    
        $product = Product::findOrFail($productId);
    
        $review = new Review();
        $review->user_id = auth()->id();
        $review->product_id = $product->id;
        $review->rating = $request->input('rating');
        $review->comment = $request->input('comment');
        $review->save();
    
        return redirect()->route('catalog.product.show', $product->id)->with('success', 'Review added successfully!');
    }





    public function toggleFavorite(Product $product)
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        if ($user->favorites->where('product_id', $product->id)->exists()) {
            $user->favorites->detach($product->id);
            return response()->json(['success' => true, 'message' => 'Product removed from favorites']);
        } else {
            $user->favorites->attach($product->id);
            return response()->json(['success' => true, 'message' => 'Product added to favorites']);
        }
    }

    public function addToFavorites($id)
    {
        $product = Product::findOrFail($id);
    
        $favorite = new Favorite();
        $favorite->user_id = Auth::id(); // Или Auth::user()->id
        $favorite->product_id = $product->id;
        $favorite->save();
    
        return redirect()->back();
    }
    

    public function removeFromFavorites($id)
    {
        // Находим запись из таблицы favorites, которая соответствует текущему пользователю и указанному продукту
        $favorite = Favorite::where('product_id', $id)->where('user_id', auth()->id())->first();
    
        // Проверяем, была ли найдена такая запись
        if ($favorite) {
            // Удаляем запись из таблицы favorites
            $favorite->delete();
        }
    
        return redirect()->back();
    }


}
