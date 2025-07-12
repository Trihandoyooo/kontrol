<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        // Filter pencarian
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('nik', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%");
            });
        }

        // Filter role
        if ($request->filled('role') && $request->role !== 'all') {
            $query->where('role', $request->role);
        }

        // Ambil semua kontribusi (tanpa filter status)
        $users = $query->select('users.*')
            ->withCount([
                'rapats as total_rapat' => function ($q) {
                    $q->selectRaw('count(*)')->whereColumn('rapats.nik', 'users.nik');
                },
                'iurans as total_iuran' => function ($q) {
                    $q->selectRaw('count(*)')->whereColumn('iurans.nik', 'users.nik');
                },
                'kaderisasis as total_kaderisasi' => function ($q) {
                    $q->selectRaw('count(*)')->whereColumn('kaderisasis.nik', 'users.nik');
                },
                'outcomes as total_outcome' => function ($q) {
                    $q->selectRaw('count(*)')->whereColumn('outcomes.nik', 'users.nik');
                },
            ])
            ->with([
                'rapats', 'iurans', 'kaderisasis', 'outcomes' // Ini buat modal
            ])
            ->orderBy('name')
            ->paginate(10);

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
            'role' => 'required|in:admin,ketua,user',
            'password' => 'required|string|min:6|confirmed',
        ]);

        User::create([
            'nik' => $request->nik,
            'name' => $request->name,
            'role' => $request->role,
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('admin.users.index')->with('success', 'User berhasil ditambahkan.');
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'nik' => 'required|string|unique:users,nik,' . $user->nik . ',nik',
            'name' => 'required|string|max:255',
            'role' => 'required|in:admin,ketua,user',
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        $user->update([
            'nik' => $request->nik,
            'name' => $request->name,
            'role' => $request->role,
            'password' => $request->password ? Hash::make($request->password) : $user->password,
        ]);

        return redirect()->route('admin.users.index')->with('success', 'User berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'User berhasil dihapus.');
    }
}
