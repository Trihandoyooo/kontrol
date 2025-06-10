<?php

namespace App\Http\Controllers;

use App\Models\Rapat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class RapatController extends Controller
{
    public function index(Request $request)
{
    $user = Auth::user();
    $userNik = $user->nik;

    $search = $request->input('search');
    $statusFilter = $request->input('status');

    $query = Rapat::where('nik', $userNik)
                  ->whereIn('status', ['diterima', 'ditolak']);

    if ($search) {
        $query->where(function($q) use ($search) {
            $q->where('judul', 'like', "%{$search}%")
              ->orWhere('jenis_rapat', 'like', "%{$search}%")
              ->orWhere('peserta', 'like', "%{$search}%");
        });
    }

    if ($statusFilter) {
        $query->where('status', $statusFilter);
    }

    if ($request->has('export') && $request->export == 'csv') {
        $rapats = $query->orderBy('tanggal', 'desc')->get();

        $response = new StreamedResponse(function() use ($rapats) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Jenis Rapat', 'Judul', 'Tanggal', 'Peserta', 'Status', 'Catatan']);

            foreach ($rapats as $rapat) {
                fputcsv($handle, [
                    ucfirst($rapat->jenis_rapat),
                    $rapat->judul,
                    $rapat->tanggal,
                    $rapat->peserta,
                    $rapat->status,
                    $rapat->catatan,
                ]);
            }
            fclose($handle);
        });

        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="rapat.csv"');

        return $response;
    }

    $rapats = $query->orderBy('tanggal', 'desc')->get();

    $rapatsMenunggu = Rapat::where('nik', $userNik)
                           ->where('status', 'terkirim')
                           ->orderBy('created_at', 'desc')
                           ->get();

    // Tambahan data grafik untuk admin
    $chartData = null;
    if ($user->role === 'admin') {
        $chartData = Rapat::selectRaw('status, COUNT(*) as total')
                          ->groupBy('status')
                          ->pluck('total', 'status')
                          ->toArray();
    }

    return view('rapat.user.index', compact('rapats', 'rapatsMenunggu', 'search', 'statusFilter', 'chartData'));
}

    public function create()
    {
        return view('rapat.user.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'jenis_rapat' => 'required|string',
            'judul' => 'required|string|max:255',
            'tanggal' => 'required|date',
            'peserta' => 'required|string|max:255',
            'dokumentasi.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'catatan' => 'nullable|string',
        ]);

        $validated['nik'] = Auth::user()->nik;

        $paths = [];
        if ($request->hasFile('dokumentasi')) {
            foreach ($request->file('dokumentasi') as $file) {
                $paths[] = $file->store('dokumentasi_rapat', 'public');
            }
        }
        $validated['dokumentasi'] = json_encode($paths);

        Rapat::create($validated);

        return redirect()->route('rapat.user.index')->with('success', 'Data rapat berhasil ditambahkan.');
    }

    public function show($id)
    {
        $userNik = auth()->user()->nik;
        $rapat = Rapat::where('id', $id)->where('nik', $userNik)->firstOrFail();

        return view('rapat.user.show', compact('rapat'));
    }

    public function edit($id)
    {
        $rapat = Rapat::where('nik', Auth::user()->nik)->findOrFail($id);
        return view('rapat.user.edit', compact('rapat'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'jenis_rapat' => 'required|string',
            'judul' => 'required|string|max:255',
            'tanggal' => 'required|date',
            'peserta' => 'required|string|max:255',
            'dokumentasi.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'catatan' => 'nullable|string',
        ]);

        $rapat = Rapat::where('nik', Auth::user()->nik)->findOrFail($id);

        $existing = json_decode($rapat->dokumentasi, true) ?? [];

        if ($request->hasFile('dokumentasi')) {
            foreach ($request->file('dokumentasi') as $file) {
                $existing[] = $file->store('dokumentasi_rapat', 'public');
            }
        }

        $validated['dokumentasi'] = count($existing) ? json_encode($existing) : null;

        $rapat->update($validated);

        return redirect()->route('rapat.user.index')->with('success', 'Data rapat berhasil diperbarui');
    }

    public function destroy($id)
    {
        $rapat = Rapat::where('nik', Auth::user()->nik)->findOrFail($id);

        $files = json_decode($rapat->dokumentasi, true);
        if ($files) {
            foreach ($files as $file) {
                Storage::disk('public')->delete($file);
            }
        }

        $rapat->delete();

        return redirect()->route('rapat.user.index')->with('success', 'Data rapat berhasil dihapus');
    }
}
