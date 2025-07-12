<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Iuran;
use App\Models\AlokasiDana;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class IuranAdminController extends Controller
{
    public function index(Request $request)
    {
        $query = Iuran::with('user');

        if ($request->filled('jenis_iuran')) {
            $query->where('jenis_iuran', $request->jenis_iuran);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('tanggal_dari')) {
            $query->where('tanggal', '>=', $request->tanggal_dari);
        }

        if ($request->filled('tanggal_sampai')) {
            $query->where('tanggal', '<=', $request->tanggal_sampai);
        }

        if ($request->filled('nama')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->nama . '%');
            });
        }

        $iurans = $query->paginate(10);

        // Statistik
        $totalTerkirim = Iuran::where('status', 'terkirim')->sum('nominal');
        $totalDiterima = Iuran::where('status', 'diterima')->sum('nominal');
        $totalDitolak  = Iuran::where('status', 'ditolak')->sum('nominal');

        $topPenyetor = Iuran::where('status', 'diterima')
            ->selectRaw('nik, SUM(nominal) as total')
            ->groupBy('nik')
            ->orderByDesc('total')
            ->with('user')
            ->take(5)
            ->get();

        $semuaTotalUser = Iuran::where('status', 'diterima')
            ->selectRaw('nik, SUM(nominal) as total')
            ->groupBy('nik')
            ->orderByDesc('total')
            ->with('user')
            ->get();

        // Alokasi dan data grafik pie
        $alokasis = AlokasiDana::latest()->get();
        $totalDialokasikan = AlokasiDana::sum('jumlah');
        $totalSisa = $totalDiterima - $totalDialokasikan;

        $grafikAlokasi = AlokasiDana::select('nama_kegiatan', DB::raw('SUM(jumlah) as total'))
            ->groupBy('nama_kegiatan')
            ->get()
            ->map(function ($item) {
                return [
                    'label' => $item->nama_kegiatan,
                    'value' => $item->total,
                ];
            })
            ->toArray();

        if ($totalSisa > 0) {
            $grafikAlokasi[] = [
                'label' => 'Sisa Dana',
                'value' => $totalSisa,
            ];
        }

        return view('iuran.admin.index', compact(
            'iurans',
            'totalTerkirim',
            'totalDiterima',
            'totalDitolak',
            'topPenyetor',
            'semuaTotalUser',
            'alokasis',
            'grafikAlokasi'
        ));
    }

    public function show($id)
    {
        $iuran = Iuran::with('user')->findOrFail($id);
        return view('iuran.admin.show', compact('iuran'));
    }

    public function edit($id)
    {
        $iuran = Iuran::with('user')->findOrFail($id);
        $statuses = ['terkirim', 'diterima', 'ditolak'];
        return view('iuran.admin.edit', compact('iuran', 'statuses'));
    }

    public function destroy($id)
    {
        $iuran = Iuran::findOrFail($id);

        if ($iuran->dokumentasi) {
            $files = json_decode($iuran->dokumentasi, true);
            foreach ($files as $file) {
                \Storage::disk('public')->delete($file);
            }
        }

        $iuran->delete();

        return redirect()->route('admin.iuran.index')->with('success', 'Data iuran berhasil dihapus.');
    }

    public function exportPdf(Request $request)
    {
        $query = Iuran::with('user');

        if ($request->filled('tanggal_dari')) {
            $query->where('tanggal', '>=', $request->tanggal_dari);
        }

        if ($request->filled('tanggal_sampai')) {
            $query->where('tanggal', '<=', $request->tanggal_sampai);
        }

        if ($request->filled('nama')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->nama . '%');
            });
        }

        $query->where('status', '!=', 'terkirim');
        $iurans = $query->get();

        $tanggalDari = $request->input('tanggal_dari');
        $tanggalSampai = $request->input('tanggal_sampai');

        if ($tanggalDari && $tanggalSampai) {
            $periode = Carbon::parse($tanggalDari)->format('d-m-Y') . ' s.d. ' . Carbon::parse($tanggalSampai)->format('d-m-Y');
        } elseif ($tanggalDari) {
            $periode = 'Mulai ' . Carbon::parse($tanggalDari)->format('d-m-Y');
        } elseif ($tanggalSampai) {
            $periode = 'Sampai ' . Carbon::parse($tanggalSampai)->format('d-m-Y');
        } else {
            $periode = 'Semua Waktu';
        }

        $bulan = $request->input('bulan') ?? now()->format('Y-m');
        $pdf = PDF::loadView('iuran.admin.pdf', compact('iurans', 'periode', 'bulan'));

        return $pdf->stream('laporan-iuran.pdf');
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:terkirim,diterima,ditolak',
            'alasan_tolak' => 'nullable|string|required_if:status,ditolak',
        ]);

        $iuran = Iuran::findOrFail($id);
        $iuran->status = $request->status;

        if ($request->status == 'ditolak') {
            $iuran->alasan_tolak = $request->alasan_tolak;
        } else {
            $iuran->alasan_tolak = null;
        }

        $iuran->save();

        return redirect()->route('admin.iuran.index')->with('success', 'Status berhasil diperbarui.');
    }
}
