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

<div class="mt-4">
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">

            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h2 class="mb-1">Data Kaderisasi Anda</h2>
                    <p class="text-muted mb-0">Berikut merupakan data kaderisasi yang telah Anda laporkan.</p>
                </div>
                <a href="{{ route('kaderisasi.user.create') }}" class="btn btn-outline-success">
                    <i class="bi bi-plus-lg"></i> Tambah Kaderisasi
                </a>
            </div>

            {{-- Search & Filter --}}
            <form method="GET" action="{{ route('kaderisasi.user.index') }}" class="mb-4">
                <div class="row g-2 align-items-center">
                    <div class="col-md-4">
                        <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Cari judul atau peserta...">
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
                        <a href="{{ route('kaderisasi.user.index') }}" class="btn btn-secondary">
                            <i class="bi bi-x-circle"></i> Reset
                        </a>
                    </div>
                </div>
            </form>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            {{-- Menunggu Verifikasi --}}
            @if($kaderisasisMenunggu->isNotEmpty())
            <div class="mb-5">
                <h4 class="mb-3">Menunggu Verifikasi</h4>
                <div class="card shadow-sm border-0">
                    <div class="card-body table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Judul</th>
                                    <th>Tanggal</th>
                                    <th>Peserta</th>
                                    <th>Dokumentasi</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($kaderisasisMenunggu as $kaderisasi)
                                <tr>
                                    <td>{{ $kaderisasi->judul }}</td>
                                    <td>{{ \Carbon\Carbon::parse($kaderisasi->tanggal)->format('d-m-Y') }}</td>
                                    <td>{{ $kaderisasi->peserta ?? '-' }}</td>
                                    <td>
                                        @php $files = json_decode($kaderisasi->dokumentasi ?? '[]', true); @endphp
                                        @if($files && count($files))
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

            {{-- Riwayat Kaderisasi --}}
            <h4 class="mb-3">Kaderisasi Disetujui / Ditolak</h4>
            <div class="card shadow-sm border-0">
                <div class="card-body table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Judul</th>
                                <th>Tanggal</th>
                                <th>Peserta</th>
                                <th>Dokumentasi</th>
                                <th>Status</th>
                                <th>Catatan</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($kaderisasis as $kaderisasi)
                            <tr>
                                <td>{{ $kaderisasi->judul }}</td>
                                <td>{{ \Carbon\Carbon::parse($kaderisasi->tanggal)->format('d-m-Y') }}</td>
                                <td>{{ $kaderisasi->peserta ?? '-' }}</td>
                                <td>
                                    @php $files = json_decode($kaderisasi->dokumentasi ?? '[]', true); @endphp
                                    @if($files && count($files))
                                        @foreach($files as $file)
                                            <a href="{{ asset('storage/' . $file) }}" target="_blank" class="btn btn-outline-secondary btn-sm mb-1">
                                                <i class="bi bi-file-earmark-text"></i> Lihat
                                            </a>
                                        @endforeach
                                    @else
                                        <span class="text-muted">Tidak ada</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge
                                        {{ $kaderisasi->status == 'terkirim' ? 'badge-terkirim' :
                                           ($kaderisasi->status == 'diterima' ? 'badge-diterima' :
                                           ($kaderisasi->status == 'ditolak' ? 'badge-ditolak' : '') ) }}">
                                        {{ ucfirst($kaderisasi->status) }}
                                    </span>
                                </td>
                                <td>{{ $kaderisasi->catatan ?? '-' }}</td>
                                <td class="text-center">
                                    <a href="{{ route('kaderisasi.user.show', $kaderisasi->id) }}" class="btn btn-sm btn-outline-info mb-1">
                                        <i class="bi bi-eye"></i> Detail
                                    </a>
                                    <a href="{{ route('kaderisasi.user.edit', $kaderisasi->id) }}" class="btn btn-sm btn-outline-warning mb-1">
                                        <i class="bi bi-pencil-square"></i> Edit
                                    </a>
                                    <form action="{{ route('kaderisasi.user.destroy', $kaderisasi->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus data ini?')">
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
                                <td colspan="7" class="text-center text-muted">Tidak ada data kaderisasi yang disetujui.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="mt-3">
                        {{ $kaderisasis->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
