<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function dashboard()
    {
        return view('admin.dashboard');
    }

    public function usersList()
    {
        $users = DB::table('users')->paginate(5);
        return view('admin.users', ['users' => $users]);
    }

    public function updateUserRole(Request $request, $id)
    {
        $role = $request->input('role');
        DB::table('users')
            ->where('id', $id)
            ->update(['role' => $role]);

        return redirect()->back()->with('success', 'User role updated successfully.');
    }
}
