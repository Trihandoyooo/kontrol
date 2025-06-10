@extends('layouts.app')

@section('content')

<style>
    body {
        background:rgb(236, 244, 239) !important;
    }
</style>

<div class="container mt-4">
    <h2>Daftar Rapat (Admin)</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered">
        <thead class="table-success">
            <tr>
                <th>Jenis Rapat</th>
                <th>Judul</th>
                <th>Lokasi</th>
                <th>Tanggal</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($rapats as $rapat)
            <tr>
                <td>{{ $rapat->jenis_rapat }}</td>
                <td>{{ $rapat->judul }}</td>
                <td>{{ $rapat->lokasi }}</td>
                <td>{{ \Carbon\Carbon::parse($rapat->tanggal)->format('d-m-Y') }}</td>

                <td>
                        <form action="{{ route('admin.rapat.updateStatus', $rapat->id) }}" method="POST" class="d-flex align-items-center gap-1">
                            @csrf
                            @method('PATCH')
                            <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                                <option value="terkirim" {{ $rapat->status == 'terkirim' ? 'selected' : '' }}>Terkirim</option>
                                <option value="diterima" {{ $rapat->status == 'diterima' ? 'selected' : '' }}>Diterima</option>
                                <option value="ditolak" {{ $rapat->status == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                            </select>

                        @if($rapat->status == 'ditolak')
                            <input type="text" name="alasan_tolak" value="{{ $rapat->alasan_tolak }}" placeholder="Alasan tolak" class="form-control form-control-sm">
                            <button type="submit" class="btn btn-primary btn-sm">Simpan</button>
                        @endif
                    </form>
                </td>

                <td>
                    <a href="{{ route('admin.rapat.show', $rapat->id) }}" class="btn btn-info btn-sm">Detail</a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center">Belum ada data rapat.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection
