@extends('layouts.app')

@section('content')

<style>
    body {
        background: rgb(236, 244, 239) !important;
    }
    .page-content {
        padding-left: 1.5rem !important;
        padding-right: 1.5rem !important;
    }
    .controls-container {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
        align-items: center;
    }
    .btn-sm {
        border-radius: 6px;
        padding: 0.35rem 0.75rem;
        font-size: 0.875rem;
        transition: 0.3s;
    }
    .btn-outline-warning {
        color: #ffc107;
        border-color: #ffc107;
    }
    .btn-outline-warning:hover {
        background-color: #ffc107;
        color: #000;
    }
    .badge-terkirim { background-color: #ffc107; color: black; }
    .badge-diterima { background-color: #198754; color: white; }
    .badge-ditolak { background-color: #dc3545; color: white; }
    .table th, .table td { vertical-align: middle; }
</style>

<div class="container mt-0">
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <h2>Data Rapat</h2>
            <p class="text-subtitle text-muted">Berikut merupakan daftar rapat dari seluruh pengguna. Admin dapat memverifikasi dan menghapus data di sini.</p>

            <!-- Filter Form -->
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title mb-3">Filter & Ekspor Data Rapat</h5>
                    <form method="GET" action="{{ route('admin.rapat.index') }}">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-2">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select">
                                    <option value="">Semua</option>
                                    <option value="terkirim" {{ request('status') == 'terkirim' ? 'selected' : '' }}>Terkirim</option>
                                    <option value="diterima" {{ request('status') == 'diterima' ? 'selected' : '' }}>Diterima</option>
                                    <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Tanggal Dari</label>
                                <input type="date" name="tanggal_dari" class="form-control" value="{{ request('tanggal_dari') }}">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Tanggal Sampai</label>
                                <input type="date" name="tanggal_sampai" class="form-control" value="{{ request('tanggal_sampai') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Pencarian</label>
                                <input type="text" name="search" class="form-control" placeholder="Nama, jenis rapat atau judul..." value="{{ request('search') }}">
                            </div>
                            <div class="col-auto d-grid">
                                <button type="submit" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-search"></i> Filter
                                </button>
                            </div>
                            <div class="col-auto d-grid">
                                <a href="{{ route('admin.rapat.index') }}" class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-arrow-clockwise"></i> Reset
                                </a>
                            </div>
                            <div class="col-auto d-grid">
                                <a href="{{ route('admin.rapat.exportPdf', request()->query()) }}" class="btn btn-sm btn-outline-danger">
                                    <i class="bi bi-file-earmark-pdf"></i> PDF
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Tabel Rapat -->
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>User</th>
                            <th>Jenis Rapat</th>
                            <th>Judul</th>
                            <th>Tanggal</th>
                            <th>Peserta</th>
                            <th>Status</th>
                            <th style="width: 240px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rapats as $item)
                        <tr>
                            <td>{{ $item->user->name ?? '-' }}</td>
                            <td>{{ $item->jenis_rapat }}</td>
                            <td>{{ $item->judul }}</td>
                            <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d-m-Y') }}</td>
                            <td>{{ $item->peserta }}</td>
                            <td>
                                @php
                                    $badgeClass = match($item->status) {
                                        'diterima' => 'badge-diterima',
                                        'ditolak' => 'badge-ditolak',
                                        default => 'badge-terkirim'
                                    };
                                @endphp
                                <span class="badge {{ $badgeClass }}">{{ ucfirst($item->status) }}</span>
                            </td>
                            <td>
                                <a href="{{ route('admin.rapat.show', $item->id) }}" class="btn btn-outline-info btn-sm me-1">
                                    <i class="bi bi-eye"></i> Detail
                                </a>
                                <form action="{{ route('admin.rapat.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-outline-danger btn-sm me-1">
                                        <i class="bi bi-trash"></i> Hapus
                                    </button>
                                </form>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center">Tidak ada data rapat.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap Modal Support -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

@endsection
