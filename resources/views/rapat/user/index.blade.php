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
        padding-top: 0.75rem;
        padding-bottom: 0.75rem;
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

    .badge-terkirim {
        background-color: #ffc107;
        color: black;
    }

    .badge-diterima {
        background-color: #198754;
        color: white;
    }

    .badge-ditolak {
        background-color: #dc3545;
        color: white;
    }

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

            {{-- Header --}}
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                <div>
                    <h2 class="mb-1">Rapat Anda</h2>
                    <p class="text-muted mb-0">Berikut merupakan informasi rapat yang telah bapak/ibu inputkan.</p>
                </div>
                <a href="{{ route('rapat.user.create') }}" class="btn btn-outline-success">
                    <i class="bi bi-plus-lg"></i> Tambah Rapat
                </a>
            </div>

            {{-- Flash Success --}}
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            {{-- Search + Filter --}}
            <form method="GET" class="mb-3">
                <div class="row g-2 align-items-center">
                    <div class="col-md-4">
                        <input type="text" name="search" value="{{ request('search') }}" class="form-control"
                            placeholder="Cari judul, jenis rapat, peserta...">
                    </div>
                    <div class="col-md-3">
                        <select name="status" class="form-select">
                            <option value="">Filter Status</option>
                            <option value="terkirim" {{ request('status') == 'terkirim' ? 'selected' : '' }}>Terkirim</option>
                            <option value="diterima" {{ request('status') == 'diterima' ? 'selected' : '' }}>Diterima</option>
                            <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-success w-100">
                            <i class="bi bi-search"></i> Cari
                        </button>
                    </div>
                    <div class="col-md-2">
                        <a href="{{ route('rapat.user.index') }}" class="btn btn-secondary w-100">
                            <i class="bi bi-x-circle"></i> Reset
                        </a>
                    </div>
                </div>
            </form>

            {{-- Rapat Menunggu Verifikasi --}}
            @if($rapatsMenunggu->isNotEmpty())
                <div class="mb-5">
                    <h4 class="mb-3">Menunggu Verifikasi</h4>
                    <div class="card shadow-sm border-0">
                        <div class="card-body table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Judul</th>
                                        <th>Jenis</th>
                                        <th>Tanggal</th>
                                        <th>Dokumentasi</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($rapatsMenunggu as $rapat)
                                        <tr>
                                            <td>{{ $rapat->judul }}</td>
                                            <td>{{ ucfirst($rapat->jenis_rapat) }}</td>
                                            <td>{{ \Carbon\Carbon::parse($rapat->tanggal)->format('d-m-Y') }}</td>
                                            <td>
                                                @php $files = json_decode($rapat->dokumentasi); @endphp
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

            {{-- Riwayat Rapat Disetujui / Ditolak --}}
            <h4 class="mb-3">Rapat Disetujui / Ditolak</h4>
            <div class="card shadow-sm border-0">
                <div class="card-body table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Jenis Rapat</th>
                                <th>Judul</th>
                                <th>Tanggal</th>
                                <th>Peserta</th>
                                <th>Status</th>
                                <th>Dokumentasi</th>
                                <th>Catatan</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($rapats as $rapat)
                                <tr>
                                    <td>{{ ucfirst($rapat->jenis_rapat) }}</td>
                                    <td>{{ $rapat->judul }}</td>
                                    <td>{{ \Carbon\Carbon::parse($rapat->tanggal)->format('d-m-Y') }}</td>
                                    <td>{{ $rapat->peserta ?? '-' }}</td>
                                    <td>
                                        <span class="badge 
                                            {{ $rapat->status == 'terkirim' ? 'badge-terkirim' :
                                               ($rapat->status == 'diterima' ? 'badge-diterima' :
                                               ($rapat->status == 'ditolak' ? 'badge-ditolak' : '')) }}">
                                            {{ ucfirst($rapat->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        @php $files = json_decode($rapat->dokumentasi); @endphp
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
                                    <td>{{ $rapat->catatan ?? '-' }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('rapat.user.show', $rapat->id) }}" class="btn btn-sm btn-outline-info mb-1">
                                            <i class="bi bi-eye"></i> Detail
                                        </a>
                                        <a href="{{ route('rapat.user.edit', $rapat->id) }}" class="btn btn-sm btn-outline-warning mb-1">
                                            <i class="bi bi-pencil-square"></i> Edit
                                        </a>
                                        <form action="{{ route('rapat.user.destroy', $rapat->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin hapus?')">
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
                                    <td colspan="8" class="text-center text-muted">Belum ada rapat disetujui atau ditolak.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
