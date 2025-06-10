<?php

namespace App\Http\Controllers;

use App\Models\Kaderisasi;
use Illuminate\Http\Request;

class KaderisasiController extends Controller
{
    public function index(Request $request)
    {
        $userNik = auth()->user()->nik;
        $search = $request->input('search');

        // Kaderisasi yang sudah diverifikasi (diterima atau ditolak)
        $query = Kaderisasi::where('nik', $userNik)
            ->whereIn('status', ['diterima', 'ditolak']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', '%' . $search . '%')
                  ->orWhere('peserta', 'like', '%' . $search . '%')
                  ->orWhere('status', 'like', '%' . $search . '%');
            });
        }

        $kaderisasis = $query->orderBy('tanggal', 'desc')->paginate(10);

        // Kaderisasi yang belum diverifikasi (status = terkirim)
        $kaderisasisMenunggu = Kaderisasi::where('nik', $userNik)
            ->where('status', 'terkirim')
            ->orderBy('tanggal', 'desc')
            ->get();

        return view('kaderisasi.user.index', compact('kaderisasis', 'search', 'kaderisasisMenunggu'));
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
            'dokumentasi.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf',
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
        $validated = $request->validate([
            'nik' => 'required|string|max:20',
            'judul' => 'required|string|max:255',
            'tanggal' => 'required|date',
            'dokumentasi.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf',
            'peserta' => 'nullable|string',
            'catatan' => 'nullable|string',
            'status' => 'required|in:terkirim,diterima,ditolak',
            'alasan_tolak' => 'nullable|string',
        ]);

        $existingFiles = json_decode($kaderisasi->dokumentasi) ?: [];
        $newFiles = [];

        if ($request->hasFile('dokumentasi')) {
            foreach ($request->file('dokumentasi') as $file) {
                $newFiles[] = $file->store('kaderisasi_dokumentasi', 'public');
            }
        }

        $allFiles = array_merge($existingFiles, $newFiles);
        $validated['dokumentasi'] = json_encode($allFiles);

        $kaderisasi->update($validated);

        return redirect()->route('kaderisasi.user.index')->with('success', 'Data kaderisasi berhasil diperbarui.');
    }

    public function updateStatus(Request $request, $id)
    {
        $kaderisasi = Kaderisasi::findOrFail($id);

        $request->validate([
            'status' => 'required|in:terkirim,diterima,ditolak',
            'catatan' => 'nullable|string',
        ]);

        $kaderisasi->status = $request->input('status');

        if ($request->input('status') === 'ditolak') {
            $kaderisasi->alasan_tolak = $request->input('catatan');
            $kaderisasi->catatan = null;
        } else {
            $kaderisasi->alasan_tolak = null;
            $kaderisasi->catatan = $request->input('catatan');
        }

        $kaderisasi->save();

        return redirect()->route('admin.kaderisasi.index')->with('success', 'Status kaderisasi berhasil diperbarui.');
    }

    public function destroy(Kaderisasi $kaderisasi)
    {
        $files = json_decode($kaderisasi->dokumentasi);
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

    // Fitur export CSV
    public function export(Request $request)
    {
        $search = $request->input('search');

        $query = Kaderisasi::where('status', '!=', 'menunggu'); // exclude status menunggu

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', '%' . $search . '%')
                  ->orWhere('peserta', 'like', '%' . $search . '%')
                  ->orWhere('status', 'like', '%' . $search . '%');
            });
        }

        $kaderisasis = $query->orderBy('tanggal', 'desc')->get();

        $filename = "riwayat_kaderisasi_" . date('Ymd_His') . ".csv";

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename={$filename}",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['Tanggal', 'Peserta', 'Status', 'Catatan'];

        $callback = function () use ($kaderisasis, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($kaderisasis as $item) {
                fputcsv($file, [
                    $item->judul,
                    $item->tanggal,
                    $item->peserta,
                    $item->status,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
