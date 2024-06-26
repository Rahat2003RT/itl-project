<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
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
        //dump($request->boolean('remember'));
        //dd($request)->all();
        
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
        return view('user.profile', compact('user'));
        //return view('user.profile');
    }

    public function edit(){
        $user = Auth::user();
        return view('user.edit', compact('user'));
    }

    public function updateProfile(Request $request){

        $request->validate([
            'name' => 'required|string|max:255',
            'delivery_address' => 'nullable|string|max:255',
        ]);

        Auth::user()->update($request->only('name','delivery_address'));

        return redirect()->route('profile')->with('success', 'Profile updated successfully.');
    }


}
