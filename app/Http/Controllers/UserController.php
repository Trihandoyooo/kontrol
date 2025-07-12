<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
   public function index(Request $request)
{
    $query = \App\Models\User::query();

    // Pencarian nama/nik/email
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('nik', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%");
        });
    }

    // Filter role
    if ($request->filled('role') && $request->role !== 'all') {
        $query->where('role', $request->role);
    }

    $users = $query->orderBy('name')->paginate(10)->withQueryString();

    return view('admin.users.index', compact('users'));
}

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nik' => 'required|string|unique:users,nik|max:20',
            'name' => 'required|string|max:255',
            'gelar_depan' => 'nullable|string|max:50',
            'gelar_belakang' => 'nullable|string|max:50',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|in:admin,ketua,user',

            // Uploads
            'foto_ktp' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'foto_kta' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'foto_profil' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',

            // Data pribadi
            'nomor_kta' => 'nullable|string|max:100',
            'tempat_lahir' => 'nullable|string|max:100',
            'tanggal_lahir' => 'nullable|date',
            'usia' => 'nullable|integer|min:0',
            'jenis_kelamin' => 'nullable|in:L,P',
            'agama' => 'nullable|string|max:50',
            'status_perkawinan' => 'nullable|string|max:100',

            // Alamat
            'alamat_ktp' => 'nullable|string',
            'rt' => 'nullable|string|max:5',
            'rw' => 'nullable|string|max:5',
            'kecamatan' => 'nullable|string|max:100',
            'kelurahan_desa' => 'nullable|string|max:100',

            // Khusus user
            'dapil' => 'nullable|string|max:100',
            'jumlah_suara' => 'nullable|integer|min:0',
            'jumlah_suara_sebelumnya' => 'nullable|integer|min:0',
            'jumlah_tim' => 'nullable|integer|min:0',
        ]);

        // Simpan file jika ada
        $fotoKtp = $request->file('foto_ktp')?->store('ktp', 'public');
        $fotoKta = $request->file('foto_kta')?->store('kta', 'public');
        $fotoProfil = $request->file('foto_profil')?->store('profil', 'public');

        User::create([
            'nik' => $request->nik,
            'name' => $request->name,
            'gelar_depan' => $request->gelar_depan,
            'gelar_belakang' => $request->gelar_belakang,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,

            // Uploads
            'foto_ktp' => $fotoKtp,
            'foto_kta' => $fotoKta,
            'foto_profil' => $fotoProfil,

            // Data pribadi
            'nomor_kta' => $request->nomor_kta,
            'tempat_lahir' => $request->tempat_lahir,
            'tanggal_lahir' => $request->tanggal_lahir,
            'usia' => $request->usia,
            'jenis_kelamin' => $request->jenis_kelamin,
            'agama' => $request->agama,
            'status_perkawinan' => $request->status_perkawinan,

            // Alamat
            'alamat_ktp' => $request->alamat_ktp,
            'rt' => $request->rt,
            'rw' => $request->rw,
            'kecamatan' => $request->kecamatan,
            'kelurahan_desa' => $request->kelurahan_desa,

            // Default value
            'kabupaten' => 'Bengkalis',

            // Khusus user
            'dapil' => $request->dapil,
            'jumlah_suara' => $request->jumlah_suara ?? 0,
            'jumlah_suara_sebelumnya' => $request->jumlah_suara_sebelumnya ?? 0,
            'jumlah_tim' => $request->jumlah_tim ?? 0,
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
            'nik' => 'required|string|max:20|unique:users,nik,' . $nik . ',nik',
            'name' => 'required|string|max:255',
            'gelar_depan' => 'nullable|string|max:50',
            'gelar_belakang' => 'nullable|string|max:50',
            'email' => 'required|email|unique:users,email,' . $nik . ',nik',
            'password' => 'nullable|string|min:6|confirmed',
            'role' => 'required|in:admin,ketua,user',

            // Uploads
            'foto_ktp' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'foto_kta' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'foto_profil' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',

            // Data pribadi & alamat
            'nomor_kta' => 'nullable|string|max:100',
            'tempat_lahir' => 'nullable|string|max:100',
            'tanggal_lahir' => 'nullable|date',
            'usia' => 'nullable|integer|min:0',
            'jenis_kelamin' => 'nullable|in:L,P',
            'agama' => 'nullable|string|max:50',
            'status_perkawinan' => 'nullable|string|max:100',
            'alamat_ktp' => 'nullable|string',
            'rt' => 'nullable|string|max:5',
            'rw' => 'nullable|string|max:5',
            'kecamatan' => 'nullable|string|max:100',
            'kelurahan_desa' => 'nullable|string|max:100',
            'dapil' => 'nullable|string|max:100',
            'jumlah_suara' => 'nullable|integer|min:0',
            'jumlah_suara_sebelumnya' => 'nullable|integer|min:0',
            'jumlah_tim' => 'nullable|integer|min:0',
        ]);

        // Update foto jika ada
        if ($request->hasFile('foto_ktp')) {
            if ($user->foto_ktp) Storage::disk('public')->delete($user->foto_ktp);
            $user->foto_ktp = $request->file('foto_ktp')->store('ktp', 'public');
        }

        if ($request->hasFile('foto_kta')) {
            if ($user->foto_kta) Storage::disk('public')->delete($user->foto_kta);
            $user->foto_kta = $request->file('foto_kta')->store('kta', 'public');
        }

        if ($request->hasFile('foto_profil')) {
            if ($user->foto_profil) Storage::disk('public')->delete($user->foto_profil);
            $user->foto_profil = $request->file('foto_profil')->store('profil', 'public');
        }

        $user->fill([
            'nik' => $request->nik,
            'name' => $request->name,
            'gelar_depan' => $request->gelar_depan,
            'gelar_belakang' => $request->gelar_belakang,
            'email' => $request->email,
            'role' => $request->role,

            'nomor_kta' => $request->nomor_kta,
            'tempat_lahir' => $request->tempat_lahir,
            'tanggal_lahir' => $request->tanggal_lahir,
            'usia' => $request->usia,
            'jenis_kelamin' => $request->jenis_kelamin,
            'agama' => $request->agama,
            'status_perkawinan' => $request->status_perkawinan,

            'alamat_ktp' => $request->alamat_ktp,
            'rt' => $request->rt,
            'rw' => $request->rw,
            'kecamatan' => $request->kecamatan,
            'kelurahan_desa' => $request->kelurahan_desa,
            'kabupaten' => 'Bengkalis',

            'dapil' => $request->dapil,
            'jumlah_suara' => $request->jumlah_suara ?? 0,
            'jumlah_suara_sebelumnya' => $request->jumlah_suara_sebelumnya ?? 0,
            'jumlah_tim' => $request->jumlah_tim ?? 0,
        ]);

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->route('admin.users.index')->with('success', 'User berhasil diperbarui');
    }

    public function show($id)
{
    $user = User::findOrFail($id);
    return view('admin.users.show', compact('user'));
}

    public function destroy($nik)
    {
        $user = User::findOrFail($nik);

        if ($user->foto_ktp) Storage::disk('public')->delete($user->foto_ktp);
        if ($user->foto_kta) Storage::disk('public')->delete($user->foto_kta);
        if ($user->foto_profil) Storage::disk('public')->delete($user->foto_profil);

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'User berhasil dihapus');
    }

    
}
