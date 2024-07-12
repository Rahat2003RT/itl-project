<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\UserCard;
use App\Models\PickupPoint;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

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

        // Создание уведомления
        $notificationData = [
            'type' => 'order_created',
            'data' => 'Ваш заказ успешно оформлен.',
        ];

        Notification::create([
            'user_id' => auth()->id(),
            'type' => $notificationData['type'],
            'data' => $notificationData['data'],
        ]);

        return redirect()->route('home')->with('success', 'Заказ успешно оформлен.');
    }



    public function index(Request $request)
    {
        // Get the status filter if it exists
        $status = $request->get('status');
        
        // Retrieve orders, optionally filtered by status, and eager load the order items
        $orders = Order::when($status, function ($query, $status) {
            return $query->where('status', $status);
        })->with('orderItems.product')->get();

        return view('admin.orders.index', compact('orders', 'status'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        // Validate the request
        $request->validate([
            'status' => 'required|in:pending,processing,completed,cancelled',
        ]);

        // Update the order status
        $order->status = $request->input('status');
        $order->save();

        return redirect()->route('admin.orders.index')->with('success', 'Order status updated successfully');
    }
}
