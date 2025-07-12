<?php

namespace App\Http\Controllers;

use App\Models\Iuran;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class IuranController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $search = $request->input('search');
        $statusFilter = $request->input('status');

        if ($user->role === 'admin') {
            $users = User::all();

            $query = DB::table('iurans')
                ->join('users', 'users.nik', '=', 'iurans.nik')
                ->select('iurans.*', 'users.name as user_name');

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('iurans.jenis_iuran', 'like', "%{$search}%")
                      ->orWhere('iurans.catatan', 'like', "%{$search}%")
                      ->orWhere('iurans.status', 'like', "%{$search}%")
                      ->orWhere('users.name', 'like', "%{$search}%");
                });
            }

            if ($statusFilter) {
                $query->where('iurans.status', $statusFilter);
            }

            if ($request->filled('nik')) {
                $query->where('iurans.nik', $request->nik);
            }

            $iurans = $query->orderBy('iurans.tanggal', 'desc')->paginate(10);

            return view('iuran.admin.index', compact('users', 'iurans', 'search', 'statusFilter'));
        } else {
            $nik = $user->nik;

            $query = Iuran::where('nik', $nik);

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('jenis_iuran', 'like', "%{$search}%")
                      ->orWhere('catatan', 'like', "%{$search}%")
                      ->orWhere('status', 'like', "%{$search}%");
                });
            }

            if ($statusFilter) {
                $query->where('status', $statusFilter);
            }

            $iurans = $query->orderBy('tanggal', 'desc')->get();

            $totalDisetujui = $iurans->where('status', 'diterima')->sum('nominal');
            $progressPerKategori = $this->hitungProgressPerKategori($iurans);

            return view('iuran.user.index', compact('iurans', 'totalDisetujui', 'progressPerKategori', 'search', 'statusFilter'));
        }
    }

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
            $terkumpul = $iurans
                ->where('jenis_iuran', $kategori)
                ->where('status', 'diterima')
                ->sum('nominal');

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

    public function create()
    {
        if (Auth::user()->role === 'admin') {
            $users = User::whereIn('role', ['user', 'ketua'])->get();
            return view('iuran.admin.create', compact('users'));
        }

        return view('iuran.user.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'jenis_iuran' => 'required|string',
            'nominal' => 'required|numeric|min:1',
            'tanggal' => 'required|date',
            'catatan' => 'nullable|string',
            'dokumentasi.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $validated['nik'] = Auth::user()->nik;
        $validated['status'] = 'terkirim';

        if ($request->hasFile('dokumentasi')) {
            $paths = [];
            foreach ($request->file('dokumentasi') as $file) {
                $paths[] = $file->store('dokumentasi_iuran', 'public');
            }
            $validated['dokumentasi'] = json_encode($paths);
        }

        Iuran::create($validated);

        return redirect()->route('iuran.user.index')->with('success', 'Iuran berhasil ditambahkan dan status terkirim.');
    }

    public function show($id)
    {
        $iuran = Iuran::findOrFail($id);

        if (Auth::user()->role !== 'admin' && $iuran->nik !== Auth::user()->nik) {
            abort(403);
        }

        return view('iuran.user.show', compact('iuran'));
    }

    public function edit($id)
    {
        $iuran = Iuran::where('nik', Auth::user()->nik)->findOrFail($id);
        return view('iuran.user.edit', compact('iuran'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'jenis_iuran' => 'required|string',
            'nominal' => 'required|numeric|min:1',
            'tanggal' => 'required|date',
            'catatan' => 'nullable|string',
            'dokumentasi.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $iuran = Iuran::where('nik', Auth::user()->nik)->findOrFail($id);

        if ($request->hasFile('dokumentasi')) {
            $paths = [];
            foreach ($request->file('dokumentasi') as $file) {
                $paths[] = $file->store('dokumentasi_iuran', 'public');
            }
            $existing = json_decode($iuran->dokumentasi, true) ?? [];
            $validated['dokumentasi'] = json_encode(array_merge($existing, $paths));
        }

        $validated['status'] = 'terkirim';
        $validated['alasan_tolak'] = null;

        $iuran->update($validated);

        return redirect()->route('iuran.user.index')->with('success', 'Data iuran berhasil diperbarui dan status dikirim ulang.');
    }

    public function destroy($id)
    {
        $iuran = Iuran::where('nik', Auth::user()->nik)->findOrFail($id);

        if ($iuran->dokumentasi) {
            $files = json_decode($iuran->dokumentasi, true);
            foreach ($files as $file) {
                if (Storage::disk('public')->exists($file)) {
                    Storage::disk('public')->delete($file);
                }
            }
        }

        $iuran->delete();

        return redirect()->route('iuran.user.index')->with('success', 'Data iuran berhasil dihapus.');
    }

    public function deleteFile(Request $request, $id)
    {
        $iuran = Iuran::where('nik', Auth::user()->nik)->findOrFail($id);
        $fileToDelete = $request->get('file');

        if (!$fileToDelete) {
            return back()->with('error', 'File tidak ditemukan.');
        }

        $files = json_decode($iuran->dokumentasi, true) ?? [];
        if (($key = array_search($fileToDelete, $files)) !== false) {
            unset($files[$key]);
            if (Storage::disk('public')->exists($fileToDelete)) {
                Storage::disk('public')->delete($fileToDelete);
            }
            $iuran->dokumentasi = json_encode(array_values($files));
            $iuran->save();
        }

        return back()->with('success', 'File dokumentasi berhasil dihapus.');
    }

    public function updateStatus(Request $request, $id)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403);
        }

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
