<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kaderisasi;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class KaderisasiAdminController extends Controller
{
    public function index(Request $request)
    {
        $query = Kaderisasi::with('user')->orderBy('tanggal', 'desc');

        // Filter status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter tanggal dari - sampai
        if ($request->filled('tanggal_dari')) {
            $query->where('tanggal', '>=', $request->tanggal_dari);
        }
        if ($request->filled('tanggal_sampai')) {
            $query->where('tanggal', '<=', $request->tanggal_sampai);
        }

        // Search di judul dan peserta
if ($request->filled('search')) {
    $search = $request->search;
    $query->where(function ($q) use ($search) {
        $q->where('judul', 'like', "%$search%")
          ->orWhere('peserta', 'like', "%$search%")
          ->orWhereHas('user', function ($uq) use ($search) {
              $uq->where('name', 'like', "%$search%")
                 ->orWhere('nik', 'like', "%$search%");
          });
    });
}

        $kaderisasi = $query->paginate(10)->withQueryString();

        return view('kaderisasi.admin.index', compact('kaderisasi'));
    }

    public function show($id)
    {
        $kaderisasi = Kaderisasi::with('user')->findOrFail($id);
        return view('kaderisasi.admin.show', compact('kaderisasi'));
    }

    public function exportPdf(Request $request)
{
    $query = Kaderisasi::with('user')->orderBy('tanggal', 'desc');

    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    if ($request->filled('tanggal_dari')) {
        $query->where('tanggal', '>=', $request->tanggal_dari);
    }

    if ($request->filled('tanggal_sampai')) {
        $query->where('tanggal', '<=', $request->tanggal_sampai);
    }

if ($request->filled('search')) {
    $search = $request->search;
    $query->where(function ($q) use ($search) {
        $q->where('judul', 'like', "%$search%")
          ->orWhere('peserta', 'like', "%$search%")
          ->orWhereHas('user', function ($uq) use ($search) {
              $uq->where('name', 'like', "%$search%")
                 ->orWhere('nik', 'like', "%$search%");
          });
    });
}

    $kaderisasi = $query->get();

    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('kaderisasi.admin.pdf', compact('kaderisasi'))
        ->setPaper('A4', 'portrait');

    return $pdf->download('laporan_kaderisasi_' . date('Ymd') . '.pdf');
}


public function updateStatus(Request $request, $id)
{
    $rules = [
        'status' => 'required|in:terkirim,diterima,ditolak',
    ];

    // Wajib isi alasan jika status 'ditolak'
    if ($request->status === 'ditolak') {
        $rules['alasan_tolak'] = 'required|string';
    }

    $validated = $request->validate($rules);

    $kaderisasi = Kaderisasi::findOrFail($id);
    $kaderisasi->status = $validated['status'];
    $kaderisasi->alasan_tolak = $validated['status'] === 'ditolak' ? $validated['alasan_tolak'] : null;
    $kaderisasi->save();

    return redirect()->route('kaderisasi.admin.index')->with('success', 'Status kaderisasi berhasil diperbarui.');
}


    public function destroy($id)
    {
        $kaderisasi = Kaderisasi::findOrFail($id);

        if ($kaderisasi->dokumentasi) {
            foreach (json_decode($kaderisasi->dokumentasi) as $file) {
                \Storage::disk('public')->delete($file);
            }
        }

        $kaderisasi->delete();

        return redirect()->route('kaderisasi.admin.index')->with('success', 'Data kaderisasi berhasil dihapus.');
    }
}
