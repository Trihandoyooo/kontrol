<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller; 
use App\Models\Rapat;
use App\Models\User;
use Illuminate\Http\Request;

class RapatAdminController extends Controller
{
    /**
     * Menampilkan semua data rapat dari semua user.
     */
    public function index()
    {
        $rapats = Rapat::orderBy('tanggal', 'desc')->with('user')->get();
        return view('rapat.admin.index', compact('rapats'));
    }

    /**
     * Menampilkan detail satu data rapat.
     */
    public function show($id)
    {
        $rapat = Rapat::with('user')->findOrFail($id);
        return view('rapat.admin.show', compact('rapat'));
    }

    /**
     * Verifikasi (ubah status) rapat menjadi diterima atau ditolak.
     */
    public function updateStatus(Request $request, $id)
{
    $request->validate([
        'status' => 'required|in:terkirim,diterima,ditolak',
        'alasan_tolak' => 'nullable|string|max:255',
    ]);

    $rapat = Rapat::findOrFail($id);
    $rapat->status = $request->status;

    // Simpan alasan_tolak hanya jika status ditolak
    if ($request->status === 'ditolak') {
        $rapat->alasan_tolak = $request->alasan_tolak;
    } else {
        $rapat->alasan_tolak = null; // Kosongkan kalau bukan ditolak
    }

    $rapat->save();

    return redirect()->back()->with('success', 'Status rapat berhasil diperbarui.');
}


    /**
     * Hapus data rapat.
     */
    public function destroy($id)
    {
        $rapat = Rapat::findOrFail($id);

        if ($rapat->dokumentasi) {
            foreach (json_decode($rapat->dokumentasi) as $file) {
                \Storage::disk('public')->delete($file);
            }
        }

        $rapat->delete();
        return back()->with('success', 'Data rapat berhasil dihapus.');
    }
}

