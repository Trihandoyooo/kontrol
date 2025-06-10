<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nik' => 'required|string|unique:users,nik',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|in:admin,ketua,user',
        ]);

        User::create([
            'nik' => $request->nik,
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        return redirect()->route('admin.users.index')->with('success', 'User berhasil ditambahkan');
    }

    public function edit($nik)
    {
        $user = User::findOrFail($nik);
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, $nik)
    {
        $user = User::findOrFail($nik);

        $request->validate([
            'nik' => 'required|string|unique:users,nik,' . $user->nik . ',nik',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->nik . ',nik',
            'password' => 'nullable|string|min:6|confirmed',
            'role' => 'required|in:admin,ketua,user',
        ]);

        $user->nik = $request->nik;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->role = $request->role;

        if ($request->password) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->route('admin.users.index')->with('success', 'User berhasil diupdate');
    }

    public function destroy($nik)
    {
        $user = User::findOrFail($nik);
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'User berhasil dihapus');
    }
}
