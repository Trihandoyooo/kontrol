<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AlokasiDana;
use Illuminate\Support\Facades\Storage;

class AlokasiDanaAdminController extends Controller
{
    public function index()
    {
        $alokasis = AlokasiDana::latest()->paginate(10);
        return view('iuran.admin.index', compact('alokasis'));
    }

    public function create()
{
    return view('iuran.alokasi.create');
}
    public function store(Request $request)
{
    $request->validate([
        'nama_kegiatan' => 'required|string|max:255',
        'jumlah' => 'required|numeric|min:1',
        'tanggal' => 'required|date',
        'deskripsi' => 'nullable|string',
        'dokumentasi' => 'required|array',
        'dokumentasi.*' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
    ]);

    // Simpan dokumentasi
    $dokumentasiPaths = [];
    if ($request->hasFile('dokumentasi')) {
        foreach ($request->file('dokumentasi') as $file) {
            $dokumentasiPaths[] = $file->store('alokasi_dana', 'public');
        }
    }

    // Simpan ke database
    \App\Models\AlokasiDana::create([
        'nama_kegiatan' => $request->nama_kegiatan,
        'jumlah' => $request->jumlah,
        'tanggal' => $request->tanggal,
        'deskripsi' => $request->deskripsi,
        'dokumentasi' => json_encode($dokumentasiPaths),
    ]);

    // Redirect ke halaman iuran.index (bukan alokasi.index)
    return redirect()->route('admin.iuran.index')->with('success', 'Alokasi dana berhasil ditambahkan.');
}

    public function show($id)
    {
        $alokasi = AlokasiDana::findOrFail($id);
        return view('iuran.alokasi.show', compact('alokasi'));
    }

    public function edit($id)
    {
        $alokasi = AlokasiDana::findOrFail($id);
        return view('iuran.alokasi.edit', compact('alokasi'));
    }

    public function update(Request $request, $id)
{
    $request->validate([
        'nama_kegiatan' => 'required|string|max:255',
        'jumlah' => 'required|numeric|min:1',
        'tanggal' => 'required|date',
        'deskripsi' => 'nullable|string',
        'dokumentasi' => 'nullable',
        'dokumentasi.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
    ]);

    $alokasi = AlokasiDana::findOrFail($id);
    $data = $request->only(['nama_kegiatan', 'jumlah', 'tanggal', 'deskripsi']);

    // DECODE JSON STRING menjadi array
    $existingFiles = $alokasi->dokumentasi ? json_decode($alokasi->dokumentasi, true) : [];

    if ($request->hasFile('dokumentasi')) {
        foreach ($request->file('dokumentasi') as $file) {
            $existingFiles[] = $file->store('alokasi', 'public');
        }
    }

    $data['dokumentasi'] = json_encode($existingFiles);

    $alokasi->update($data);

    return redirect()->route('admin.iuran.index')->with('success', 'Data alokasi berhasil diperbarui.');
}
    public function destroy($id)
{
    $alokasi = AlokasiDana::findOrFail($id);

    if ($alokasi->dokumentasi) {
        $files = json_decode($alokasi->dokumentasi, true) ?? [];
        foreach ($files as $file) {
            Storage::disk('public')->delete($file);
        }
    }

    $alokasi->delete();

    return redirect()->back()->with('success', 'Data alokasi berhasil dihapus.');
}


    public function deleteFile($id, $index)
{
    $alokasi = \App\Models\AlokasiDana::findOrFail($id);
    $files = json_decode($alokasi->dokumentasi, true) ?? [];

    if (isset($files[$index])) {
        \Illuminate\Support\Facades\Storage::disk('public')->delete($files[$index]);
        unset($files[$index]);
        $files = array_values($files); // Reindex

        $alokasi->update([
            'dokumentasi' => json_encode($files),
        ]);
    }

    return back()->with('success', 'File dokumentasi berhasil dihapus.');
}

}
