<?php

namespace App\Http\Controllers;

use App\Models\Rapat;
use App\Models\Iuran;
use App\Models\Kaderisasi;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $role = auth()->user()->role;

        if ($role === 'user') {
            $userNik = Auth::user()->nik;

            // Total kontribusi
            $totalRapat = Rapat::where('nik', $userNik)
                ->where('status', 'diterima')
                ->count();

            $totalKaderisasi = Kaderisasi::where('nik', $userNik)
                ->where('status', 'diterima')
                ->count();

            $iuranDihitung = Iuran::where('nik', $userNik)
                ->whereNotIn('jenis_iuran', ['insidensial', 'lainnya'])
                ->sum('nominal');

            // Target bulanan
            $targetRapat = 10;
            $targetKaderisasi = 10;
            $targetIuran = 100000000;

            // Statistik per minggu (4 minggu terakhir)
            $mingguLabels = [];
            $dataRapat = [];
            $dataIuran = [];
            $dataKaderisasi = [];

            for ($i = 3; $i >= 0; $i--) {
                $startOfWeek = Carbon::now()->startOfWeek()->subWeeks($i);
                $endOfWeek = Carbon::now()->endOfWeek()->subWeeks($i);
                $label = "Minggu {$startOfWeek->format('d M')} - {$endOfWeek->format('d M')}";

                $mingguLabels[] = $label;
                $dataRapat[] = Rapat::where('nik', $userNik)
                    ->where('status', 'diterima')
                    ->whereBetween('created_at', [$startOfWeek, $endOfWeek])
                    ->count();

                $dataIuran[] = Iuran::where('nik', $userNik)
                    ->whereNotIn('jenis_iuran', ['insidensial', 'lainnya'])
                    ->whereBetween('created_at', [$startOfWeek, $endOfWeek])
                    ->sum('nominal');

                $dataKaderisasi[] = Kaderisasi::where('nik', $userNik)
                    ->where('status', 'diterima')
                    ->whereBetween('created_at', [$startOfWeek, $endOfWeek])
                    ->count();
            }

            return view('user.welcome', compact(
                'totalRapat',
                'totalKaderisasi',
                'iuranDihitung',
                'targetRapat',
                'targetKaderisasi',
                'targetIuran',
                'mingguLabels',
                'dataRapat',
                'dataIuran',
                'dataKaderisasi'
            ));
        }

        return match ($role) {
            'admin' => redirect()->route('admin.home'),
            'ketua' => redirect()->route('ketua.home'),
            default => abort(403, 'Unauthorized'),
        };
    }
}
