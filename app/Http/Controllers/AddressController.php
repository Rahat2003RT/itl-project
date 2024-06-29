<?php

namespace App\Http\Controllers;

use App\Models\Address;
use Illuminate\Http\Request;


class AddressController extends Controller
{
    public function index()
    {
        // Метод для вывода списка адресов (если нужно) 
    }

    public function edit()
    {
        // Метод для отображения формы создания нового адреса
        $user = auth()->user();
        $address = Address::where('user_id', $user->id)->first();
        return view('addresses.form', compact('address'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();
    
        // Попытка найти адрес пользователя
        $address = Address::where('user_id', $user->id)->first();
    
        // Если адрес уже существует, обновляем его, иначе создаем новый
        if ($address) {
            $address->update($request->all());
        } else {
            $address = Address::create($request->all() + ['user_id' => $user->id]);
        }
    
        // Возвращаем на страницу профиля пользователя
        return redirect()->route('profile')->with('success', 'Адрес успешно обновлен.');
    }
}
