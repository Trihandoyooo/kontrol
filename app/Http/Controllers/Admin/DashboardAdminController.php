<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Kaderisasi;
use App\Models\Iuran;
use App\Models\Rapat;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardAdminController extends Controller
{
    public function index()
    {
        // Contoh data total
        $user_total = User::count();
        $rapat_total = Rapat::count();
        $iuran_total = Iuran::count();
        $kaderisasi_total = Kaderisasi::count();

        // Data untuk grafik per minggu
        $mingguLabels = [];
        $dataRapat = [];
        $dataIuran = [];
        $dataKaderisasi = [];

        // Ambil data 4 minggu terakhir
        for ($i = 3; $i >= 0; $i--) {
            $startOfWeek = now()->subWeeks($i)->startOfWeek();
            $endOfWeek = now()->subWeeks($i)->endOfWeek();
            $weekLabel = "Minggu {$startOfWeek->format('d M')} - {$endOfWeek->format('d M')}";

            $mingguLabels[] = $weekLabel;
            $dataRapat[] = Rapat::whereBetween('created_at', [$startOfWeek, $endOfWeek])->count();
            $dataIuran[] = Iuran::whereBetween('created_at', [$startOfWeek, $endOfWeek])->count();
            $dataKaderisasi[] = Kaderisasi::whereBetween('created_at', [$startOfWeek, $endOfWeek])->count();
        }

        // Data untuk grafik per bulan
        $bulanLabels = [];
        $dataRapatBulan = [];
        $dataIuranBulan = [];
        $dataKaderisasiBulan = [];

        for ($i = 3; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $bulanLabels[] = $month->format('F Y');
            $dataRapatBulan[] = Rapat::whereYear('created_at', $month->year)
                                    ->whereMonth('created_at', $month->month)
                                    ->count();
            $dataIuranBulan[] = Iuran::whereYear('created_at', $month->year)
                                    ->whereMonth('created_at', $month->month)
                                    ->count();
            $dataKaderisasiBulan[] = Kaderisasi::whereYear('created_at', $month->year)
                                        ->whereMonth('created_at', $month->month)
                                        ->count();
        }

        // Data untuk grafik per hari
        $hariLabels = [];
        $dataRapatHari = [];
        $dataIuranHari = [];
        $dataKaderisasiHari = [];

        for ($i = 6; $i >= 0; $i--) {
            $day = now()->subDays($i);
            $hariLabels[] = $day->format('d M');
            $dataRapatHari[] = Rapat::whereDate('created_at', $day)->count();
            $dataIuranHari[] = Iuran::whereDate('created_at', $day)->count();
            $dataKaderisasiHari[] = Kaderisasi::whereDate('created_at', $day)->count();
        }

        return view('admin.welcome', compact(
            'user_total', 'rapat_total', 'iuran_total', 'kaderisasi_total',
            'mingguLabels', 'dataRapat', 'dataIuran', 'dataKaderisasi',
            'bulanLabels', 'dataRapatBulan', 'dataIuranBulan', 'dataKaderisasiBulan',
            'hariLabels', 'dataRapatHari', 'dataIuranHari', 'dataKaderisasiHari'
        ));
    }
}