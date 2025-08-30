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

    // Filter berdasarkan jenis iuran
    if ($request->filled('jenis_iuran')) {
        $query->where('jenis_iuran', $request->jenis_iuran);
    }

    // Filter berdasarkan status
    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    // Filter tanggal mulai
    if ($request->filled('tanggal_dari')) {
        $query->where('tanggal', '>=', $request->tanggal_dari);
    }

    // Filter tanggal sampai
    if ($request->filled('tanggal_sampai')) {
        $query->where('tanggal', '<=', $request->tanggal_sampai);
    }

    // Filter berdasarkan nama (form filter manual)
    if ($request->filled('nama')) {
        $query->whereHas('user', function ($q) use ($request) {
            $q->where('name', 'like', '%' . $request->nama . '%');
        });
    }

    // 🔹 Filter search (dari tombol kontribusi)
    if ($request->filled('search')) {
        $search = $request->search;
        $query->whereHas('user', function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('nik', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%");
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

    // 🔹 Total per user + rincian
    $semuaTotalUser = Iuran::where('status', 'diterima')
        ->selectRaw('nik, SUM(nominal) as total')
        ->groupBy('nik')
        ->orderByDesc('total')
        ->with('user')
        ->get();

    foreach ($semuaTotalUser as $userTotal) {
        $userTotal->rincian = Iuran::where('status', 'diterima')
            ->where('nik', $userTotal->nik)
            ->select('jenis_iuran', DB::raw('SUM(nominal) as total'))
            ->groupBy('jenis_iuran')
            ->pluck('total', 'jenis_iuran'); 
            // hasil: ['Iuran Bulanan' => 50000, 'Iuran Pembangunan' => 100000]
    }

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

    // Total per kategori (hanya status diterima biar valid)
$totalPerKategori = Iuran::where('status', 'diterima')
    ->select('jenis_iuran', DB::raw('SUM(nominal) as total'))
    ->groupBy('jenis_iuran')
    ->pluck('total', 'jenis_iuran'); 
    // hasil: ['Dana Kompensasi' => 500000, 'Sumbangan Fraksi' => 250000, ...]


    return view('iuran.admin.index', compact(
        'iurans',
        'totalTerkirim',
        'totalDiterima',
        'totalDitolak',
        'topPenyetor',
        'semuaTotalUser',
        'alokasis',
        'grafikAlokasi',
        'totalPerKategori'
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
    $query = Iuran::with('user')->orderBy('tanggal', 'desc');

    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    if ($request->filled('tanggal_dari')) {
        $query->whereDate('tanggal', '>=', $request->tanggal_dari);
    }

    if ($request->filled('tanggal_sampai')) {
        $query->whereDate('tanggal', '<=', $request->tanggal_sampai);
    }

    if ($request->filled('nama')) {
        $nama = $request->nama;
        $query->whereHas('user', function ($q) use ($nama) {
            $q->where('name', 'like', "%$nama%")
              ->orWhere('nik', 'like', "%$nama%");
        });
    }

    // Hilangkan filter bulan & "terkirim" supaya semua transaksi bisa tampil
    $iurans = $query->get();

    // Tentukan periode untuk ditampilkan di header PDF
    $tanggalDari = $request->input('tanggal_dari');
    $tanggalSampai = $request->input('tanggal_sampai');

    if ($tanggalDari && $tanggalSampai) {
        $periode = \Carbon\Carbon::parse($tanggalDari)->format('d-m-Y') .
                   ' s.d. ' .
                   \Carbon\Carbon::parse($tanggalSampai)->format('d-m-Y');
    } elseif ($tanggalDari) {
        $periode = 'Mulai ' . \Carbon\Carbon::parse($tanggalDari)->format('d-m-Y');
    } elseif ($tanggalSampai) {
        $periode = 'Sampai ' . \Carbon\Carbon::parse($tanggalSampai)->format('d-m-Y');
    } else {
        $periode = 'Semua Waktu';
    }

    $pdf = Pdf::loadView('iuran.admin.pdf', compact('iurans', 'periode'))
              ->setPaper('A4', 'portrait');

    // Aktifkan supaya logo di PDF muncul
    $pdf->getDomPDF()->set_option('isRemoteEnabled', true);

    return $pdf->download('laporan_iuran_' . date('Ymd') . '.pdf');
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
