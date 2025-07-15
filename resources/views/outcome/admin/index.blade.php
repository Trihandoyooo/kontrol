@extends('layouts.app')

@section('content')
    <style>
        body {
            background: rgb(236, 244, 239) !important;
        }

        .badge-terkirim {
            background-color: #ffc107;
            color: black;
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 0.875rem;
        }

        .badge-diterima {
            background-color: #198754;
            color: white;
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 0.875rem;
        }

        .badge-ditolak {
            background-color: #dc3545;
            color: white;
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 0.875rem;
        }

        .table th,
        .table td {
            vertical-align: middle;
        }
    </style>

    <div class="mt-4">
        <div class="card-container shadow-sm border-0 mb-4">
            <div class="card-body">
                <div class="page-heading mb-3">
                    <h3>Data Outcome</h3>
                    <p>Berikut merupakan daftar outcome yang telah dilaporkan pengguna.</p>
                </div>
                {{-- Filter & Export --}}
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title mb-3">Filter & Ekspor Data</h5>
                        <form method="GET" action="{{ route('admin.outcome.index') }}">
                            <div class="row g-3 align-items-end">

                                <div class="col-md-2">
                                    <label class="form-label">Status</label>
                                    <select name="status" class="form-select">
                                        <option value="">Semua</option>
                                        <option value="terkirim" {{ request('status') == 'terkirim' ? 'selected' : '' }}>
                                            Terkirim</option>
                                        <option value="diterima" {{ request('status') == 'diterima' ? 'selected' : '' }}>
                                            Diterima</option>
                                        <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>
                                            Ditolak</option>
                                    </select>
                                </div>

                                <div class="col-md-2">
                                    <label class="form-label">Tanggal Dari</label>
                                    <input type="date" name="tanggal_dari" class="form-control"
                                        value="{{ request('tanggal_dari') }}">
                                </div>

                                <div class="col-md-2">
                                    <label class="form-label">Tanggal Sampai</label>
                                    <input type="date" name="tanggal_sampai" class="form-control"
                                        value="{{ request('tanggal_sampai') }}">
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label">Pencarian</label>
                                    <input type="text" name="search" class="form-control"
                                        placeholder="Judul atau nama kegiatan..." value="{{ request('search') }}">
                                </div>

                                <div class="col-auto d-grid">
                                    <button type="submit" class="btn btn-outline-primary">
                                        <i class="bi bi-search"></i> Filter
                                    </button>
                                </div>
                                <div class="col-auto d-grid">
                                    <a href="{{ route('admin.outcome.index') }}" class="btn btn-outline-secondary">
                                        <i class="bi bi-arrow-clockwise"></i> Reset
                                    </a>
                                </div>
                                <div class="col-auto d-grid">
                                    <a href="{{ route('admin.outcome.exportPdf', request()->query()) }}"
                                        class="btn btn-outline-danger">
                                        <i class="bi bi-file-earmark-pdf"></i> PDF
                                    </a>
                                </div>

                            </div>
                        </form>
                    </div>
                </div>

                {{-- Table --}}
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Pelapor</th>
                                <th>Judul</th>
                                <th>Tanggal</th>
                                <th>Nama Kegiatan</th>
                                <th>Dapil</th>
                                <th>Status</th>
                                <th style="width: 200px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($outcomes as $outcome)
                                <tr>
                                    <td>{{ $outcome->user->name ?? '-' }}</td>
                                    <td>{{ $outcome->judul }}</td>
                                    <td>{{ \Carbon\Carbon::parse($outcome->tanggal)->format('d-m-Y') }}</td>
                                    <td>{{ $outcome->nama_kegiatan }}</td>
                                    <td>{{ $outcome->dapil }}</td>
                                    <td>
                                        <span
                                            class="badge
                                    {{ $outcome->status === 'diterima'
                                        ? 'badge-diterima'
                                        : ($outcome->status === 'ditolak'
                                            ? 'badge-ditolak'
                                            : 'badge-terkirim') }}">
                                            {{ ucfirst($outcome->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.outcome.show', $outcome->id) }}"
                                            class="btn btn-outline-info btn-sm mb-1">
                                            <i class="bi bi-eye"></i> Detail
                                        </a>
                                        <form action="{{ route('admin.outcome.destroy', $outcome->id) }}" method="POST"
                                            class="d-inline" onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-outline-danger btn-sm mb-1">
                                                <i class="bi bi-trash"></i> Hapus
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">Tidak ada data outcome.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{ $outcomes->links() }}

            </div>
        </div>
    </div>
@endsection
