<?php

namespace App\Http\Controllers;

use App\Models\Iuran;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class IuranController extends Controller
{
    // Tampilkan daftar iuran sesuai role
    public function index(Request $request)
    {
        if (Auth::user()->role === 'admin') {
            $users = User::all();

            $query = Iuran::with('user')->orderBy('tanggal', 'desc');

            if ($request->filled('nik')) {
                $query->where('nik', $request->nik);
            }

            $iurans = $query->paginate(10);

            return view('iuran.admin.index', compact('users', 'iurans'));
        } else {
            $nik = Auth::user()->nik;
            $iurans = Iuran::where('nik', $nik)->orderBy('tanggal', 'desc')->get();

            $totalKeseluruhan = $iurans->sum('nominal');
            $progressPerKategori = $this->hitungProgressPerKategori($iurans);

            return view('iuran.user.index', compact('iurans', 'totalKeseluruhan', 'progressPerKategori'));
        }
    }

    // Fungsi hitung progress per kategori untuk user
    private function hitungProgressPerKategori($iurans)
    {
        $kategoriList = [
            'Iuran Bulanan', 'Sumbangan Fraksi', 'Dana Infaq Shadaqoh dan Zakat (ZIS)',
            'Dana Khitmat', 'Dana Kompensasi Kepada Caleg', 'Dana Insidensial', 'Dana Lainnya'
        ];

        $targetPerKategori = [
            'Iuran Bulanan' => 1000000,
            'Sumbangan Fraksi' => 750000,
            'Dana Infaq Shadaqoh dan Zakat (ZIS)' => 500000,
            'Dana Khitmat' => 300000,
            'Dana Kompensasi Kepada Caleg' => 400000,
            'Dana Insidensial' => 200000,
            'Dana Lainnya' => 250000
        ];

        $progressPerKategori = [];

        foreach ($kategoriList as $kategori) {
            $terkumpul = $iurans->where('jenis_iuran', $kategori)->sum('nominal');
            $target = $targetPerKategori[$kategori] ?? 1;
            $persentase = round(($terkumpul / $target) * 100, 2);

            $progressPerKategori[$kategori] = [
                'terkumpul' => $terkumpul,
                'target' => $target,
                'persentase' => $persentase,
            ];
        }

        return $progressPerKategori;
    }

    // Form tambah iuran (user)
    public function create()
{
    if (Auth::user()->role === 'admin') {
        $users = User::where('role', 'user')->orWhere('role', 'ketua')->get();
        return view('iuran.admin.create', compact('users'));
    }

    return view('iuran.user.create');
}


    // Simpan iuran (user)
    public function store(Request $request)
    {
        $validated = $request->validate([
            'jenis_iuran' => 'required|string',
            'nominal' => 'required|numeric|min:1',
            'tanggal' => 'required|date',
            'catatan' => 'nullable|string',
            'dokumentasi' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $validated['nik'] = Auth::user()->nik;
        $validated['status'] = 'terkirim'; // default status saat input

        if ($request->hasFile('dokumentasi')) {
            $validated['dokumentasi'] = $request->file('dokumentasi')->store('dokumentasi_iuran', 'public');
        }

        Iuran::create($validated);

        return redirect()->route('iuran.user.index')->with('success', 'Iuran berhasil ditambahkan dan status terkirim.');
    }

    // Show detail (user/admin) iuran
    public function show($id)
    {
        $iuran = Iuran::findOrFail($id);

        // User cuma boleh lihat iurannya sendiri
        if (Auth::user()->role !== 'admin' && $iuran->nik !== Auth::user()->nik) {
            abort(403);
        }

        return view('iuran.user.show', compact('iuran'));
    }

    // Edit iuran (user)
    public function edit($id)
    {
        $iuran = Iuran::where('nik', Auth::user()->nik)->findOrFail($id);
        return view('iuran.user.edit', compact('iuran'));
    }

    // Update iuran (user)
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'jenis_iuran' => 'required|string',
            'nominal' => 'required|numeric|min:1',
            'tanggal' => 'required|date',
            'catatan' => 'nullable|string',
            'dokumentasi' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $iuran = Iuran::where('nik', Auth::user()->nik)->findOrFail($id);

        if ($request->hasFile('dokumentasi')) {
            if ($iuran->dokumentasi && Storage::disk('public')->exists($iuran->dokumentasi)) {
                Storage::disk('public')->delete($iuran->dokumentasi);
            }
            $validated['dokumentasi'] = $request->file('dokumentasi')->store('dokumentasi_iuran', 'public');
        }

        // Reset status jadi terkirim kalau diedit ulang
        $validated['status'] = 'terkirim';
        $validated['alasan_tolak'] = null;

        $iuran->update($validated);

        return redirect()->route('iuran.user.index')->with('success', 'Data iuran berhasil diperbarui dan status dikirim ulang.');
    }

    // Hapus iuran (user)
    public function destroy($id)
    {
        $iuran = Iuran::where('nik', Auth::user()->nik)->findOrFail($id);

        if ($iuran->dokumentasi && Storage::disk('public')->exists($iuran->dokumentasi)) {
            Storage::disk('public')->delete($iuran->dokumentasi);
        }

        $iuran->delete();

        return redirect()->route('iuran.user.index')->with('success', 'Data iuran berhasil dihapus.');
    }

    // Update status iuran (admin)
    public function updateStatus(Request $request, $id)
    {
        if (Auth::user()->role !== 'admin') abort(403);

        $validated = $request->validate([
            'status' => 'required|in:terkirim,diterima,ditolak',
            'alasan_tolak' => 'nullable|string|required_if:status,ditolak',
        ]);

        $iuran = Iuran::findOrFail($id);

        $iuran->status = $validated['status'];
        $iuran->alasan_tolak = $validated['status'] === 'ditolak' ? $validated['alasan_tolak'] : null;
        $iuran->save();

        return redirect()->back()->with('success', 'Status iuran berhasil diperbarui.');
    }
}
