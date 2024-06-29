<?php

namespace App\Http\Controllers;

use App\Models\Address;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserCard;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;


class UserController extends Controller
{
    public function index()
    {
        $users = User::paginate();
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('user.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed']
        ]);

        $user = User::create($request->all());
        //Важно убрать после разработки
        if (User::count() === 1) {
            $user->role = 'admin';
            $user->save();
        }
        event(new Registered($user));
        Auth::login($user);

        return redirect()->route('verification.notice');
    }

    public function update(Request $request, $id)
    {
        // Получаем данные из запроса
        $role = $request->input('role');
    
        // Находим пользователя по ID
        $user = User::find($id);
    
        if (!$user) {
            return redirect()->back()->with('error', 'User not found.');
        }
    
        // Обновляем роль пользователя
        $user->role = $role;
        $user->save();
    
        return redirect()->back()->with('success', 'User role updated successfully.');
    }
    

    public function login()
    {
        return view('user.login');
    }

    public function loginAuth(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required',],
        ]);

        if(Auth::attempt($credentials, $request->boolean('remember'))){
            $request->session()->regenerate();
            return redirect()->intended('dashboard')->with('success', 'Welcome, ' . Auth::user()->name . '!');
        }

        return back()->withErrors([
            'email' => 'Wrong email or password',
        ]);
    }


    public function logout()
    {
        Auth::logout();
        return redirect()->route('login');
    }

    public function dashboard(){
        return view('user.dashboard');
    }

    public function profile(){
        $user = Auth::user();
        $address = Address::where('user_id', $user->id)->first();
        $cards = $user->cards;
        return view('user.profile', compact('user', 'address', 'cards'));
    }

    public function edit(){
        $user = Auth::user();
        return view('user.edit', compact('user'));
    }

    public function updateProfile(Request $request){

        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        Auth::user()->update($request->only('name'));

        return redirect()->route('profile')->with('success', 'Profile updated successfully.');
    }

    public function storeCard(Request $request)
    {
        $request->validate([
            'card_number' => 'required|string|max:19',
            'card_holder' => 'required|string|max:255',
            'expiry_date' => 'required|string|max:5',
            'cvv' => 'required|string|max:4',
        ]);

        $user = auth()->user();
        $user->cards()->create($request->all());

        return redirect()->route('profile')->with('success', 'Карта добавлена.');
    }

    public function destroyCard($id)
    {
        $card = UserCard::findOrFail($id);
        $card->delete();

        return redirect()->route('profile')->with('success', 'Карта удалена.');
    }


}
