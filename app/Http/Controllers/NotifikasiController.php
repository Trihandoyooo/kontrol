<?php

namespace App\Http\Controllers;

use App\Models\Notifikasi;
use Illuminate\Http\Request;

class NotifikasiController extends Controller
{
    // Admin kirim notifikasi manual
    public function store(Request $request)
    {
        $request->validate([
            'nik' => 'required|exists:users,nik',
            'judul' => 'required|string|max:255',
            'pesan' => 'nullable|string',
            'tipe' => 'required|string',
        ]);

        Notifikasi::create([
            'nik' => $request->nik,
            'judul' => $request->judul,
            'pesan' => $request->pesan,
            'tipe' => $request->tipe,
            'peringatan_ke' => 1,
            'dibaca' => false,
            'dibaca_pada' => null,
        ]);

        return back()->with('success', 'Notifikasi berhasil dikirim!');
    }

    // User melihat semua notifikasi miliknya
    public function index()
    {
        $nik = auth()->user()->nik;

        $notifs = Notifikasi::where('nik', $nik)
            ->orderByDesc('created_at')
            ->get();

        return view('notifikasi.index', compact('notifs'));
    }

    // Tandai semua sebagai dibaca
    public function markAllAsRead()
    {
        $user = auth()->user();

        Notifikasi::where('nik', $user->nik)
            ->where('dibaca', false)
            ->update([
                'dibaca' => true,
                'dibaca_pada' => now(),
            ]);

        return back()->with('success', 'Semua notifikasi ditandai telah dibaca.');
    }

    // Tandai satu notifikasi sebagai dibaca
    public function markAsRead($id)
    {
        $notif = Notifikasi::findOrFail($id);

        if ($notif->nik === auth()->user()->nik && !$notif->dibaca) {
            $notif->update([
                'dibaca' => true,
                'dibaca_pada' => now(),
            ]);
        }

        return back();
    }
}
