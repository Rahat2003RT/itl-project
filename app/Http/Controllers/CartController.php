<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CartItem;
use App\Models\PickupPoint;
use App\Models\UserCard;
use Illuminate\Support\Facades\Auth;


class CartController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $cartItems = CartItem::where('user_id', Auth::id())->get();
        $pickupPoints = PickupPoint::all();
        $userCards = UserCard::where('user_id', auth()->id())->get();
        return view('cart.index', compact('cartItems', 'pickupPoints', 'userCards', 'user'));
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $cartItem = CartItem::updateOrCreate(
            ['user_id' => Auth::id(), 'product_id' => $request->product_id],
            ['quantity' => \DB::raw('quantity + ' . $request->quantity)]
        );

        return redirect()->route('cart.index')->with('success', 'Product added to cart!');
    }

    public function remove($id)
    {
        $cartItem = CartItem::findOrFail($id);
        $cartItem->delete();
    
        return redirect()->back()->with('success', 'Item removed from cart.');
    }
}
