<?php

namespace App\Http\Controllers;

use App\Models\Rapat;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class RapatController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $search = $request->input('search');
        $statusFilter = $request->input('status');
        $tanggalDari = $request->input('tanggal_dari');
        $tanggalSampai = $request->input('tanggal_sampai');

        if ($user->role === 'admin') {
            $users = User::all();

            $query = DB::table('rapats')
                ->join('users', 'users.nik', '=', 'rapats.nik')
                ->select('rapats.*', 'users.name as user_name');

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('rapats.judul', 'like', "%{$search}%")
                      ->orWhere('rapats.jenis_rapat', 'like', "%{$search}%")
                      ->orWhere('rapats.peserta', 'like', "%{$search}%")
                      ->orWhere('rapats.status', 'like', "%{$search}%")
                      ->orWhere('users.name', 'like', "%{$search}%");
                });
            }

            if ($statusFilter) {
                $query->where('rapats.status', $statusFilter);
            }

            if ($request->filled('nik')) {
                $query->where('rapats.nik', $request->nik);
            }

            if ($tanggalDari && $tanggalSampai) {
                $query->whereBetween('rapats.tanggal', [$tanggalDari, $tanggalSampai]);
            }

            $rapats = $query->orderBy('rapats.tanggal', 'desc')->paginate(10);

            return view('rapat.admin.index', compact('users', 'rapats', 'search', 'statusFilter', 'tanggalDari', 'tanggalSampai'));
        } else {
            $nik = $user->nik;

            $query = Rapat::where('nik', $nik);

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('judul', 'like', "%{$search}%")
                      ->orWhere('jenis_rapat', 'like', "%{$search}%")
                      ->orWhere('peserta', 'like', "%{$search}%")
                      ->orWhere('status', 'like', "%{$search}%");
                });
            }

            if ($statusFilter) {
                $query->where('status', $statusFilter);
            }

            if ($tanggalDari && $tanggalSampai) {
                $query->whereBetween('tanggal', [$tanggalDari, $tanggalSampai]);
            }

            $rapats = $query->orderBy('tanggal', 'desc')->paginate(10);

            $rapatsMenunggu = $rapats->where('status', 'terkirim');
            $rapatsVerifikasi = $rapats->whereIn('status', ['diterima', 'ditolak']);

            return view('rapat.user.index', compact('rapats', 'rapatsMenunggu', 'rapatsVerifikasi', 'search', 'statusFilter', 'tanggalDari', 'tanggalSampai'));
        }
    }

    public function create()
    {
        return view('rapat.user.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'jenis_rapat' => 'required|string',
            'judul' => 'required|string|max:255',
            'tanggal' => 'required|date',
            'peserta' => 'required|string|max:255',
            'dokumentasi.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'notulen' => 'nullable|string',
        ]);

        $validated['nik'] = Auth::user()->nik;
        $validated['status'] = 'terkirim';

        if ($request->hasFile('dokumentasi')) {
            $paths = [];
            foreach ($request->file('dokumentasi') as $file) {
                $paths[] = $file->store('dokumentasi_rapat', 'public');
            }
            $validated['dokumentasi'] = json_encode($paths);
        }

        Rapat::create($validated);

        return redirect()->route('rapat.user.index')->with('success', 'Data rapat berhasil ditambahkan dan menunggu verifikasi.');
    }

    public function show($id)
    {
        $rapat = Rapat::findOrFail($id);

        if (Auth::user()->role !== 'admin' && $rapat->nik !== Auth::user()->nik) {
            abort(403);
        }

        return view('rapat.user.show', compact('rapat'));
    }

    public function edit($id)
    {
        $rapat = Rapat::where('nik', Auth::user()->nik)->findOrFail($id);
        return view('rapat.user.edit', compact('rapat'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'jenis_rapat' => 'required|string',
            'judul' => 'required|string|max:255',
            'tanggal' => 'required|date',
            'peserta' => 'nullable|string|max:255',
            'notulen' => 'nullable|string',
            'dokumentasi.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $rapat = Rapat::where('nik', Auth::user()->nik)->findOrFail($id);

        $existingFiles = json_decode($rapat->dokumentasi, true) ?? [];

        if ($request->hasFile('dokumentasi')) {
            foreach ($request->file('dokumentasi') as $file) {
                $existingFiles[] = $file->store('dokumentasi_rapat', 'public');
            }
        }

        $updateData = [
            'jenis_rapat' => $validated['jenis_rapat'],
            'judul' => $validated['judul'],
            'tanggal' => $validated['tanggal'],
            'peserta' => $validated['peserta'] ?? null,
            'notulen' => $validated['notulen'] ?? null,
            'dokumentasi' => count($existingFiles) ? json_encode($existingFiles) : null,
            'status' => 'terkirim',
            'alasan_tolak' => null,
        ];

        $rapat->update($updateData);

        return redirect()->route('rapat.user.index')->with('success', 'Data rapat berhasil diperbarui dan dikirim ulang untuk verifikasi.');
    }

    public function deleteFile($id, $index)
    {
        $rapat = Rapat::where('nik', Auth::user()->nik)->findOrFail($id);

        $files = json_decode($rapat->dokumentasi, true) ?? [];

        if (isset($files[$index])) {
            Storage::disk('public')->delete($files[$index]);
            unset($files[$index]);
            $rapat->dokumentasi = count($files) ? json_encode(array_values($files)) : null;
            $rapat->save();
        }

        return back()->with('success', 'File dokumentasi berhasil dihapus.');
    }

    public function destroy($id)
    {
        $rapat = Rapat::where('nik', Auth::user()->nik)->findOrFail($id);

        if ($rapat->dokumentasi) {
            $files = json_decode($rapat->dokumentasi, true);
            foreach ($files as $file) {
                if (Storage::disk('public')->exists($file)) {
                    Storage::disk('public')->delete($file);
                }
            }
        }

        $rapat->delete();

        return redirect()->route('rapat.user.index')->with('success', 'Data rapat berhasil dihapus.');
    }
}
