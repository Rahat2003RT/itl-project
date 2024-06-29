<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\UserCard;
use App\Models\PickupPoint;
use App\Models\Cart;
use App\Models\CartItem;

class OrderController extends Controller
{


    public function store(Request $request)
    {
        $request->validate([
            'pickup_point' => 'required|exists:pickup_points,id',
            'payment_method' => 'required',
            'card_id' => 'nullable|exists:user_cards,id',
        ]);

        $order = new Order();
        $order->user_id = auth()->id();
        $order->pickup_point_id = $request->pickup_point;
        $order->address_id = auth()->user()->address->id; // Предполагаем, что у пользователя есть адрес
        $order->payment_method = $request->payment_method;
        if ($request->payment_method == 'credit_card') {
            $order->card_id = $request->card_id;
        }
        // Получаем все записи корзины для текущего пользователя
        $cartItems = CartItem::where('user_id', auth()->id())->get();

        // Вычисляем общую сумму заказа
        $totalAmount = $cartItems->sum(function($cartItem) {
            return $cartItem->quantity * $cartItem->product->price;
        });

        // Присваиваем общую сумму заказа
        $order->total_amount = $totalAmount;
        $order->save();

        foreach (CartItem::where('user_id', auth()->id())->get() as $cartItem) {
            $order->items()->create([
                'product_id' => $cartItem->product_id,
                'quantity' => $cartItem->quantity,
                'price' => $cartItem->product->price,
            ]);
        }

        CartItem::where('user_id', auth()->id())->delete();

        return redirect()->route('home')->with('success', 'Заказ успешно оформлен.');
    }
}
