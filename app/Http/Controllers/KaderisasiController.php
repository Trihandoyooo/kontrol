<?php

namespace App\Http\Controllers;

use App\Models\Kaderisasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class KaderisasiController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $search = $request->input('search');
        $statusFilter = $request->input('status');

        // Data Menunggu Verifikasi
        $kaderisasisMenunggu = DB::table('kaderisasi')
            ->join('users', 'users.nik', '=', 'kaderisasi.nik')
            ->select('kaderisasi.*', 'users.name as user_name')
            ->when($user->role !== 'admin', fn($q) => $q->where('kaderisasi.nik', $user->nik))
            ->when($search, function ($q) use ($search, $user) {
                $q->where(function ($query) use ($search, $user) {
                    $query->where('kaderisasi.judul', 'like', "%{$search}%")
                          ->orWhere('kaderisasi.peserta', 'like', "%{$search}%")
                          ->orWhere('kaderisasi.status', 'like', "%{$search}%");
                    if ($user->role === 'admin') {
                        $query->orWhere('users.name', 'like', "%{$search}%");
                    }
                });
            })
            ->where('kaderisasi.status', 'terkirim')
            ->orderBy('kaderisasi.tanggal', 'desc')
            ->get();

        // Data Riwayat (Diterima/Ditolak)
        $query = DB::table('kaderisasi')
            ->join('users', 'users.nik', '=', 'kaderisasi.nik')
            ->select('kaderisasi.*', 'users.name as user_name')
            ->whereIn('kaderisasi.status', ['diterima', 'ditolak']);

        if ($user->role !== 'admin') {
            $query->where('kaderisasi.nik', $user->nik);
        }

        if ($search) {
            $query->where(function ($q) use ($search, $user) {
                $q->where('kaderisasi.judul', 'like', "%{$search}%")
                  ->orWhere('kaderisasi.peserta', 'like', "%{$search}%")
                  ->orWhere('kaderisasi.status', 'like', "%{$search}%");
                if ($user->role === 'admin') {
                    $q->orWhere('users.name', 'like', "%{$search}%");
                }
            });
        }

        if ($statusFilter) {
            $query->where('kaderisasi.status', $statusFilter);
        }

        $kaderisasis = $query->orderBy('kaderisasi.tanggal', 'desc')->paginate(10);

        return view('kaderisasi.user.index', compact('kaderisasis', 'kaderisasisMenunggu', 'search', 'statusFilter'));
    }

    public function create()
    {
        return view('kaderisasi.user.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nik' => 'required|string|max:20',
            'judul' => 'required|string|max:255',
            'tanggal' => 'required|date',
            'dokumentasi.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'peserta' => 'nullable|string',
            'catatan' => 'nullable|string',
            'status' => 'required|in:terkirim,diterima,ditolak',
            'alasan_tolak' => 'nullable|string',
        ]);

        $files = [];
        if ($request->hasFile('dokumentasi')) {
            foreach ($request->file('dokumentasi') as $file) {
                $files[] = $file->store('kaderisasi_dokumentasi', 'public');
            }
        }

        $validated['dokumentasi'] = json_encode($files);

        Kaderisasi::create($validated);

        return redirect()->route('kaderisasi.user.index')->with('success', 'Data kaderisasi berhasil disimpan.');
    }

    public function show(Kaderisasi $kaderisasi)
    {
        return view('kaderisasi.user.show', compact('kaderisasi'));
    }

    public function edit(Kaderisasi $kaderisasi)
    {
        return view('kaderisasi.user.edit', compact('kaderisasi'));
    }

    public function update(Request $request, Kaderisasi $kaderisasi)
    {
        if ($kaderisasi->nik !== Auth::user()->nik) {
            abort(403);
        }

        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'tanggal' => 'required|date',
            'peserta' => 'nullable|string',
            'catatan' => 'nullable|string',
            'dokumentasi.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $existingFiles = json_decode($kaderisasi->dokumentasi, true) ?? [];
        $newFiles = [];

        if ($request->hasFile('dokumentasi')) {
            foreach ($request->file('dokumentasi') as $file) {
                $newFiles[] = $file->store('kaderisasi_dokumentasi', 'public');
            }
        }

        $combinedFiles = array_merge($existingFiles, $newFiles);
        $validated['dokumentasi'] = count($combinedFiles) ? json_encode($combinedFiles) : null;

        if ($kaderisasi->status === 'ditolak') {
            $validated['status'] = 'terkirim';
            $validated['alasan_tolak'] = null;
        }

        $kaderisasi->update($validated);

        return redirect()->route('kaderisasi.user.index')->with('success', 'Data kaderisasi berhasil diperbarui.');
    }

    public function deleteFile($id, $index)
    {
        $kaderisasi = Kaderisasi::where('nik', auth()->user()->nik)->findOrFail($id);
        $files = json_decode($kaderisasi->dokumentasi, true) ?? [];

        if (isset($files[$index])) {
            if (\Storage::disk('public')->exists($files[$index])) {
                \Storage::disk('public')->delete($files[$index]);
            }

            unset($files[$index]);
            $kaderisasi->dokumentasi = count($files) ? json_encode(array_values($files)) : null;
            $kaderisasi->save();
        }

        return back()->with('success', 'Satu dokumentasi berhasil dihapus.');
    }

    public function destroy(Kaderisasi $kaderisasi)
    {
        $files = json_decode($kaderisasi->dokumentasi, true);
        if ($files) {
            foreach ($files as $file) {
                if (\Storage::disk('public')->exists($file)) {
                    \Storage::disk('public')->delete($file);
                }
            }
        }

        $kaderisasi->delete();

        return redirect()->route('kaderisasi.user.index')->with('success', 'Data kaderisasi berhasil dihapus.');
    }
}
