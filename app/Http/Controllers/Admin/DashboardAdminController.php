<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Rapat;
use App\Models\Iuran;
use App\Models\Kaderisasi;
use App\Models\Outcome;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use DB;

class DashboardAdminController extends Controller
{
    public function index()
    {
        // Statistik Utama
        $user_total = User::where('role', 'user')->count();
        $rapat_total = Rapat::where('status', 'diterima')->count();
        $iuran_total = Iuran::where('status', 'diterima')->count();
        $kaderisasi_total = Kaderisasi::where('status', 'diterima')->count();
        $outcome_total = Outcome::where('status', 'diterima')->count();

        // Statistik Harian (7 hari terakhir)
        $hariLabels = collect(range(0, 6))
            ->map(fn($i) => now()->subDays($i)->toDateString())
            ->reverse()
            ->values();

        $dataRapatHari = $hariLabels->map(fn($date) =>
            Rapat::whereDate('tanggal', $date)->where('status', 'diterima')->count()
        );
        $dataIuranHari = $hariLabels->map(fn($date) =>
            Iuran::whereDate('created_at', $date)->where('status', 'diterima')->count()
        );
        $dataKaderisasiHari = $hariLabels->map(fn($date) =>
            Kaderisasi::whereDate('tanggal', $date)->where('status', 'diterima')->count()
        );
        $dataOutcomeHari = $hariLabels->map(fn($date) =>
            Outcome::whereDate('created_at', $date)->where('status', 'diterima')->count()
        );
        $hariLabels = $hariLabels->map(fn($date) =>
            Carbon::parse($date)->format('d M')
        );

        // Statistik Mingguan (4 minggu terakhir)
        $mingguLabels = collect(range(0, 3))
            ->map(function ($i) {
                $start = now()->subWeeks($i)->startOfWeek()->toDateString();
                $end = now()->subWeeks($i)->endOfWeek()->toDateString();
                return [$start, $end];
            })
            ->reverse()
            ->values();

        $dataRapat = $mingguLabels->map(fn($range) =>
            Rapat::whereBetween('tanggal', $range)->where('status', 'diterima')->count()
        );
        $dataIuran = $mingguLabels->map(fn($range) =>
            Iuran::whereBetween('created_at', $range)->where('status', 'diterima')->count()
        );
        $dataKaderisasi = $mingguLabels->map(fn($range) =>
            Kaderisasi::whereBetween('tanggal', $range)->where('status', 'diterima')->count()
        );
        $dataOutcome = $mingguLabels->map(fn($range) =>
            Outcome::whereBetween('created_at', $range)->where('status', 'diterima')->count()
        );
        $mingguLabels = $mingguLabels->map(fn($range) =>
            Carbon::parse($range[0])->format('d M') . ' - ' . Carbon::parse($range[1])->format('d M')
        );

        // Statistik Bulanan (4 bulan terakhir)
        $bulanObjects = collect(range(0, 3))
            ->map(fn($i) => now()->subMonths($i))
            ->reverse()
            ->values();

        $bulanLabels = $bulanObjects->map(fn($date) => $date->format('M Y'));

        $dataRapatBulan = $bulanObjects->map(fn($date) =>
            Rapat::whereMonth('tanggal', $date->month)
                ->whereYear('tanggal', $date->year)
                ->where('status', 'diterima')
                ->count()
        );
        $dataIuranBulan = $bulanObjects->map(fn($date) =>
            Iuran::whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year)
                ->where('status', 'diterima')
                ->count()
        );
        $dataKaderisasiBulan = $bulanObjects->map(fn($date) =>
            Kaderisasi::whereMonth('tanggal', $date->month)
                ->whereYear('tanggal', $date->year)
                ->where('status', 'diterima')
                ->count()
        );
        $dataOutcomeBulan = $bulanObjects->map(fn($date) =>
            Outcome::whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year)
                ->where('status', 'diterima')
                ->count()
        );

        // Leaderboard User (hanya user, exclude admin & ketua)
        $usersProgress = User::select('name')
            ->where('role', 'user')
            ->addSelect(DB::raw('
                (
                    (SELECT COUNT(*) FROM rapats WHERE rapats.nik = users.nik AND rapats.status = "diterima") +
                    (SELECT COUNT(*) FROM iurans WHERE iurans.nik = users.nik AND iurans.status = "diterima") +
                    (SELECT COUNT(*) FROM kaderisasi WHERE kaderisasi.nik = users.nik AND kaderisasi.status = "diterima") +
                    (SELECT COUNT(*) FROM outcomes WHERE outcomes.nik = users.nik AND outcomes.status = "diterima")
                ) as total_point
            '))
            ->orderByDesc('total_point')
            ->limit(10)
            ->get();

        // Leaderboard User Mingguan (hanya user, exclude admin & ketua)
        $startOfWeek = now()->startOfWeek();
        $endOfWeek = now()->endOfWeek();

        $usersProgressMingguan = User::select('name')
            ->where('role', 'user')
            ->addSelect(DB::raw('
                (
                    (SELECT COUNT(*) FROM rapats WHERE rapats.nik = users.nik AND rapats.status = "diterima" AND rapats.tanggal BETWEEN "' . $startOfWeek . '" AND "' . $endOfWeek . '") +
                    (SELECT COUNT(*) FROM iurans WHERE iurans.nik = users.nik AND iurans.status = "diterima" AND iurans.created_at BETWEEN "' . $startOfWeek . '" AND "' . $endOfWeek . '") +
                    (SELECT COUNT(*) FROM kaderisasi WHERE kaderisasi.nik = users.nik AND kaderisasi.status = "diterima" AND kaderisasi.tanggal BETWEEN "' . $startOfWeek . '" AND "' . $endOfWeek . '") +
                    (SELECT COUNT(*) FROM outcomes WHERE outcomes.nik = users.nik AND outcomes.status = "diterima" AND outcomes.created_at BETWEEN "' . $startOfWeek . '" AND "' . $endOfWeek . '")
                ) as total_point
            '))
            ->orderByDesc('total_point')
            ->limit(10)
            ->get();

        return view('admin.welcome', compact(
            'user_total', 'rapat_total', 'iuran_total', 'kaderisasi_total', 'outcome_total',
            'hariLabels', 'dataRapatHari', 'dataIuranHari', 'dataKaderisasiHari', 'dataOutcomeHari',
            'mingguLabels', 'dataRapat', 'dataIuran', 'dataKaderisasi', 'dataOutcome',
            'bulanLabels', 'dataRapatBulan', 'dataIuranBulan', 'dataKaderisasiBulan', 'dataOutcomeBulan',
            'usersProgress', 'usersProgressMingguan'
        ));
    }
}
