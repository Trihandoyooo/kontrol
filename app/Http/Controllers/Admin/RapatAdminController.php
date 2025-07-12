<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Rapat;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class RapatAdminController extends Controller
{
    // Tampilkan halaman utama rapat dengan filter & pencarian
    public function index(Request $request)
    {
        $query = Rapat::with('user')->latest();

        // Filter berdasarkan status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter tanggal dari
        if ($request->filled('tanggal_dari')) {
            $query->whereDate('tanggal', '>=', $request->tanggal_dari);
        }

        // Filter tanggal sampai
        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('tanggal', '<=', $request->tanggal_sampai);
        }

        // Pencarian berdasarkan judul, lokasi, atau nama/nik user
if ($request->filled('search')) {
    $search = $request->search;
    $query->where(function ($q) use ($search) {
        $q->where('judul', 'like', "%$search%")
          ->orWhere('lokasi', 'like', "%$search%")
          ->orWhereHas('user', function ($uq) use ($search) {
              $uq->where('name', 'like', "%$search%") // Tambahkan ini untuk cari nama user
                 ->orWhere('nik', 'like', "%$search%")
                 ->orWhere('jenis_rapat', 'like', "%$search%");
          });
    });
}

        $rapats = $query->paginate(10)->appends($request->query());

        return view('rapat.admin.index', compact('rapats'));
    }

    // Tampilkan detail rapat
    public function show($id)
    {
        $rapat = Rapat::with('user')->findOrFail($id);
        return view('rapat.admin.show', compact('rapat'));
    }

    // Export PDF data rapat
    public function exportPdf(Request $request)
    {
        $query = Rapat::with('user')->orderBy('tanggal', 'desc');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('tanggal_dari')) {
            $query->whereDate('tanggal', '>=', $request->tanggal_dari);
        }

        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('tanggal', '<=', $request->tanggal_sampai);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%$search%")
                  ->orWhere('lokasi', 'like', "%$search%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%$search%")
                         ->orWhere('nik', 'like', "%$search%");
                  });
            });
        }

        $rapats = $query->get();

        $pdf = Pdf::loadView('rapat.admin.pdf', compact('rapats'))
                  ->setPaper('A4', 'portrait');

        return $pdf->download('laporan_rapat_' . date('Ymd') . '.pdf');
    }

    // Update status (terkirim/diterima/ditolak) rapat
    public function updateStatus(Request $request, $id)
    {
        $rules = [
            'status' => 'required|in:terkirim,diterima,ditolak',
        ];

        if ($request->status === 'ditolak') {
            $rules['alasan_tolak'] = 'required|string';
        }

        $validated = $request->validate($rules);

        $rapat = Rapat::findOrFail($id);
        $rapat->status = $validated['status'];
        $rapat->alasan_tolak = $validated['status'] === 'ditolak' ? $validated['alasan_tolak'] : null;
        $rapat->save();

        return redirect()->route('admin.rapat.index')->with('success', 'Status rapat berhasil diperbarui.');
    }

    // Hapus data rapat & dokumentasinya
    public function destroy($id)
    {
        $rapat = Rapat::findOrFail($id);

        if ($rapat->dokumentasi) {
            foreach (json_decode($rapat->dokumentasi) as $file) {
                Storage::disk('public')->delete($file);
            }
        }

        $rapat->delete();

        return redirect()->route('admin.rapat.index')->with('success', 'Data rapat berhasil dihapus.');
    }
}
