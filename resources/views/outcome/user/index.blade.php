@extends('layouts.app')

@section('content')

<!-- Custom Styles -->
<style>
    body {
        background: rgb(236, 244, 239) !important;
    }
    .page-content {
        padding-left: 1.5rem !important;
        padding-right: 1.5rem;
    }
    .table tbody tr {
        vertical-align: middle;
    }
    .table tbody tr + tr {
        border-top: 12px solid #f2f7f5;
    }
    .table td, .table th {
        padding-top: 1rem;
        padding-bottom: 1rem;
    }
    .badge {
        padding: 6px 12px;
        border-radius: 6px;
        font-size: 0.85rem;
    }
    .badge-terkirim { background-color: #ffc107; color: black; }
    .badge-diterima { background-color: #198754; color: white; }
    .badge-ditolak { background-color: #dc3545; color: white; }
    .btn-sm {
        font-size: 0.85rem;
        padding: 0.3rem 0.6rem;
        border-radius: 6px;
    }
    .btn-outline-secondary i {
        margin-right: 4px;
    }
</style>

<div class="container mt-4">
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">

            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h2 class="mb-1">Outcome Anda</h2>
                    <p class="text-muted mb-0">Berikut merupakan data outcome yang telah Anda laporkan.</p>
                </div>
                <a href="{{ route('outcome.user.create') }}" class="btn btn-outline-success">
                    <i class="bi bi-plus-lg"></i> Tambah Outcome
                </a>
            </div>

            {{-- Search & Filter --}}
            <form method="GET" action="{{ route('outcome.user.index') }}" class="mb-4">
                <div class="row g-2 align-items-center">
                    <div class="col-md-4">
                        <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Cari judul, kegiatan, atau dapil...">
                    </div>
                    <div class="col-md-3">
                        <select name="status" class="form-select">
                            <option value="">Filter Status</option>
                            <option value="diterima" {{ request('status') == 'diterima' ? 'selected' : '' }}>Diterima</option>
                            <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                        </select>
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-search"></i> Cari
                        </button>
                        <a href="{{ route('outcome.user.index') }}" class="btn btn-secondary">
                            <i class="bi bi-x-circle"></i> Reset
                        </a>
                    </div>
                </div>
            </form>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            {{-- Menunggu Verifikasi --}}
            @if($outcomesMenunggu->isNotEmpty())
            <div class="mb-5">
                <h4 class="mb-3">Menunggu Verifikasi</h4>
                <div class="card shadow-sm border-0">
                    <div class="card-body table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Judul</th>
                                    <th>Tanggal</th>
                                    <th>Nama Kegiatan</th>
                                    <th>Dokumentasi</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($outcomesMenunggu as $outcome)
                                <tr>
                                    <td>{{ $outcome->judul }}</td>
                                    <td>{{ \Carbon\Carbon::parse($outcome->tanggal)->format('d-m-Y') }}</td>
                                    <td>{{ $outcome->nama_kegiatan }}</td>
                                    <td>
                                        @php
                                            $files = json_decode($outcome->dokumentasi ?? '[]', true);
                                        @endphp

                                        @if(is_array($files) && count($files))
                                            @foreach($files as $file)
                                                <a href="{{ asset('storage/' . $file) }}" target="_blank" class="btn btn-outline-secondary btn-sm mb-1">
                                                    <i class="bi bi-file-earmark-text"></i> Lihat
                                                </a>
                                            @endforeach
                                        @else
                                            <span class="text-muted">Tidak ada</span>
                                        @endif
                                    </td>
                                    <td><span class="badge badge-terkirim">Terkirim</span></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif

            {{-- Riwayat Outcome --}}
            <h4 class="mb-3">Outcome Disetujui / Ditolak</h4>
            <div class="card shadow-sm border-0">
                <div class="card-body table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Judul</th>
                                <th>Tanggal</th>
                                <th>Nama Kegiatan</th>
                                <th>Dapil</th>
                                <th>Manfaat</th>
                                <th>Status</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($outcomes as $outcome)
                            <tr>
                                <td>{{ $outcome->judul }}</td>
                                <td>{{ \Carbon\Carbon::parse($outcome->tanggal)->format('d-m-Y') }}</td>
                                <td>{{ $outcome->nama_kegiatan }}</td>
                                <td>{{ $outcome->dapil }}</td>
                                <td>{{ \Illuminate\Support\Str::limit($outcome->manfaat, 50) }}</td>
                                <td>
                                    <span class="badge 
                                        {{ $outcome->status == 'terkirim' ? 'badge-terkirim' : 
                                           ($outcome->status == 'diterima' ? 'badge-diterima' : 
                                           ($outcome->status == 'ditolak' ? 'badge-ditolak' : '')) }}">
                                        {{ ucfirst($outcome->status) }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('outcome.user.show', $outcome->id) }}" class="btn btn-sm btn-outline-info mb-1">
                                        <i class="bi bi-eye"></i> Detail
                                    </a>
                                    <a href="{{ route('outcome.user.edit', $outcome->id) }}" class="btn btn-sm btn-outline-warning mb-1">
                                        <i class="bi bi-pencil-square"></i> Edit
                                    </a>
                                    <form action="{{ route('outcome.user.destroy', $outcome->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger mb-1">
                                            <i class="bi bi-trash"></i> Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted">Tidak ada data outcome yang disetujui.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="mt-3">
                        {{ $outcomes->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
