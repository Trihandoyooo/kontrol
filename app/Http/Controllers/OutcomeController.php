<?php

namespace App\Http\Controllers;

use App\Models\Outcome;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Facades\DB;

class OutcomeController extends Controller
{
public function index(Request $request)
{
    $user = Auth::user();
    $search = $request->input('search');
    $statusFilter = $request->input('status');

    // ✅ Ambil data "Menunggu Verifikasi"
    $outcomesMenunggu = DB::table('outcomes')
        ->join('users', 'users.nik', '=', 'outcomes.nik')
        ->select('outcomes.*', 'users.name as user_name')
        ->when($user->role !== 'admin', fn($q) => $q->where('outcomes.nik', $user->nik))
        ->when($search, function ($q) use ($search, $user) {
            $q->where(function ($query) use ($search, $user) {
                $query->where('outcomes.judul', 'like', "%{$search}%")
                      ->orWhere('outcomes.nama_kegiatan', 'like', "%{$search}%")
                      ->orWhere('outcomes.dapil', 'like', "%{$search}%");
                if ($user->role === 'admin') {
                    $query->orWhere('users.name', 'like', "%{$search}%");
                }
            });
        })
        ->where('outcomes.status', 'terkirim')
        ->orderBy('outcomes.created_at', 'desc')
        ->get();

    // ✅ Ambil data untuk Riwayat (Diterima/Ditolak)
    $query = DB::table('outcomes')
        ->join('users', 'users.nik', '=', 'outcomes.nik')
        ->select('outcomes.*', 'users.name as user_name')
        ->whereIn('outcomes.status', ['diterima', 'ditolak']);

    if ($user->role !== 'admin') {
        $query->where('outcomes.nik', $user->nik);
    }

    if ($search) {
        $query->where(function ($q) use ($search, $user) {
            $q->where('outcomes.judul', 'like', "%{$search}%")
              ->orWhere('outcomes.nama_kegiatan', 'like', "%{$search}%")
              ->orWhere('outcomes.dapil', 'like', "%{$search}%");
            if ($user->role === 'admin') {
                $q->orWhere('users.name', 'like', "%{$search}%");
            }
        });
    }

    if ($statusFilter) {
        $query->where('outcomes.status', $statusFilter);
    }

    // Export CSV (opsional)
    if ($request->has('export') && $request->export === 'csv') {
        $outcomes = $query->orderBy('outcomes.tanggal', 'desc')->get();
        // Tetap gunakan StreamedResponse seperti sebelumnya
    }

    $outcomes = $query->orderBy('outcomes.tanggal', 'desc')->paginate(10);

    return view('outcome.user.index', compact('outcomes', 'outcomesMenunggu', 'search', 'statusFilter'));
}

    public function create()
    {
        return view('outcome.user.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'tanggal' => 'required|date',
            'nama_kegiatan' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
            'manfaat' => 'nullable|string',
            'dapil' => 'required|string|max:255',
            'dokumentasi.*' => 'nullable|mimes:jpg,jpeg,png,webp,pdf|max:2048',
        ]);

        $files = [];
        if ($request->hasFile('dokumentasi')) {
            foreach ($request->file('dokumentasi') as $file) {
                $files[] = $file->store('outcomes', 'public');
            }
        }

        Outcome::create([
            'nik' => Auth::user()->nik,
            'judul' => $request->judul,
            'tanggal' => $request->tanggal,
            'nama_kegiatan' => $request->nama_kegiatan,
            'keterangan' => $request->keterangan,
            'manfaat' => $request->manfaat,
            'dapil' => $request->dapil,
            'dokumentasi' => json_encode($files),
            'status' => 'terkirim',
        ]);

        return redirect()->route('outcome.user.index')->with('success', 'Outcome berhasil ditambahkan.');
    }

    public function show($id)
    {
        $outcome = Outcome::findOrFail($id);
        return view('outcome.user.show', compact('outcome'));
    }

    public function edit($id)
    {
        $outcome = Outcome::findOrFail($id);
        return view('outcome.user.edit', compact('outcome'));
    }

    public function update(Request $request, $id)
    {
        $outcome = Outcome::findOrFail($id);

        $request->validate([
            'judul' => 'required|string|max:255',
            'tanggal' => 'required|date',
            'nama_kegiatan' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
            'manfaat' => 'nullable|string',
            'dapil' => 'required|string|max:255',
            'dokumentasi.*' => 'nullable|mimes:jpg,jpeg,png,webp,pdf|max:2048',
        ]);

        $files = json_decode($outcome->dokumentasi ?? '[]', true);

        if ($request->hasFile('dokumentasi')) {
            foreach ($request->file('dokumentasi') as $file) {
                $files[] = $file->store('outcomes', 'public');
            }
        }

        $updateData = [
            'judul' => $request->judul,
            'tanggal' => $request->tanggal,
            'nama_kegiatan' => $request->nama_kegiatan,
            'keterangan' => $request->keterangan,
            'manfaat' => $request->manfaat,
            'dapil' => $request->dapil,
            'dokumentasi' => json_encode($files),
            'status' => $outcome->status === 'ditolak' ? 'terkirim' : $outcome->status,
            'alasan_tolak' => $outcome->status === 'ditolak' ? null : $outcome->alasan_tolak,
        ];

        $outcome->update($updateData);

        return redirect()->route('outcome.user.index')->with('success', 'Outcome berhasil diperbarui dan dikirim untuk verifikasi.');
    }
    public function destroy($id)
    {
        $outcome = Outcome::findOrFail($id);

        if ($outcome->dokumentasi) {
            foreach (json_decode($outcome->dokumentasi, true) as $file) {
                Storage::disk('public')->delete($file);
            }
        }

        $outcome->delete();

        return back()->with('success', 'Outcome berhasil dihapus.');
    }

    public function deleteFile($id, $index)
    {
        $outcome = Outcome::findOrFail($id);
        $files = json_decode($outcome->dokumentasi, true);

        if (isset($files[$index])) {
            Storage::disk('public')->delete($files[$index]);
            unset($files[$index]);
            $outcome->dokumentasi = json_encode(array_values($files));
            $outcome->save();
        }

        return back()->with('success', 'Dokumentasi berhasil dihapus.');
    }
}
