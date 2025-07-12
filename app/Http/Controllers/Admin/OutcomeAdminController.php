<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Outcome;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Carbon;

class OutcomeAdminController extends Controller
{
   public function index(Request $request)
{
    $search = $request->input('search');
    $statusFilter = $request->input('status');
    $tanggalDari = $request->input('tanggal_dari');
    $tanggalSampai = $request->input('tanggal_sampai');

    $query = Outcome::with('user')
        ->whereIn('status', ['diterima', 'ditolak', 'terkirim']);

    if ($search) {
        $query->where(function ($q) use ($search) {
            $q->where('judul', 'like', "%{$search}%")
              ->orWhere('nama_kegiatan', 'like', "%{$search}%")
              ->orWhere('dapil', 'like', "%{$search}%")
              ->orWhereHas('user', function ($qu) use ($search) {
                  $qu->where('name', 'like', "%{$search}%");
              });
        });
    }

    if ($statusFilter) {
        $query->where('status', $statusFilter);
    }

    if ($tanggalDari && $tanggalSampai) {
        $query->whereBetween('tanggal', [$tanggalDari, $tanggalSampai]);
    }

    $outcomes = $query->orderBy('tanggal', 'desc')->paginate(10);

    return view('outcome.admin.index', compact('outcomes', 'search', 'statusFilter', 'tanggalDari', 'tanggalSampai'));
}


    public function show($id)
    {
        $outcome = Outcome::findOrFail($id);
        return view('outcome.admin.show', compact('outcome'));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:diterima,ditolak',
            'alasan_tolak' => 'nullable|required_if:status,ditolak|string|max:500'
        ]);

        $outcome = Outcome::findOrFail($id);
        $outcome->status = $request->status;
        $outcome->alasan_tolak = $request->status == 'ditolak' ? $request->alasan_tolak : null;
        $outcome->save();

        return redirect()->route('admin.outcome.index')->with('success', 'Status outcome berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $outcome = Outcome::findOrFail($id);

        if ($outcome->foto) {
            foreach (json_decode($outcome->foto, true) as $file) {
                Storage::disk('public')->delete($file);
            }
        }

        $outcome->delete();
        return back()->with('success', 'Outcome berhasil dihapus.');
    }

    public function exportPdf()
    {
        $outcomes = Outcome::latest()->get();
        $pdf = \PDF::loadView('outcome.admin.pdf', compact('outcomes'));
        return $pdf->download('laporan-outcome.pdf');
    }
}
