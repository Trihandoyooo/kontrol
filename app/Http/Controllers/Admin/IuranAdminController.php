<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Iuran;

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

        if ($request->filled('nama')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->nama . '%');
            });
        }

        $iurans = $query->paginate(10);

        return view('iuran.admin.index', compact('iurans'));
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

    public function update(Request $request, $id)
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
