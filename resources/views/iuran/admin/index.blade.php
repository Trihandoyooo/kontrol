@extends('layouts.app')

@section('content')

<style>
    body {
        background:rgb(236, 244, 239) !important;
    }

    .filter-label {
        font-weight: 600;
        font-size: 0.875rem;
    }

    .form-select-sm, .form-control-sm {
        font-size: 0.875rem;
    }
</style>

<div class="container mt-4">
    <div class="card shadow-sm border-0">
        <div class="card-body">
            <h2 class="mb-3">Rekap Semua Iuran Pengguna</h2>

            <!-- Filter -->
            <form action="{{ route('admin.iuran.index') }}" method="GET" class="row gx-3 gy-2 align-items-end mb-4">
                <div class="col-md-3">
                    <label class="filter-label">Filter Jenis Iuran</label>
                    <select name="jenis_iuran" class="form-select form-select-sm">
                        <option value="">Semua</option>
                        @foreach(['Iuran Bulanan', 'Sumbangan Fraksi', 'Dana Infaq Shadaqoh dan Zakat (ZIS)', 'Dana Khitmat', 'Dana Kompensasi Kepada Caleg', 'Dana Insidensial', 'Dana Lainnya'] as $jenis)
                            <option value="{{ $jenis }}" {{ request('jenis_iuran') == $jenis ? 'selected' : '' }}>{{ $jenis }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="filter-label">Filter Status</label>
                    <select name="status" class="form-select form-select-sm">
                        <option value="">Semua</option>
                        <option value="terkirim" {{ request('status') == 'terkirim' ? 'selected' : '' }}>Terkirim</option>
                        <option value="diterima" {{ request('status') == 'diterima' ? 'selected' : '' }}>Diterima</option>
                        <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="filter-label">Cari Nama</label>
                    <input type="text" name="nama" class="form-control form-control-sm" placeholder="Nama user" value="{{ request('nama') }}">
                </div>

                <div class="col-md-2">
                    <button class="btn btn-primary btn-sm w-100">
                        <i class="bi bi-filter"></i> Filter
                    </button>
                </div>
            </form>

            <!-- Table -->
            <div class="table-responsive">
                <table class="table table-bordered table-striped align-middle">
                    <thead class="table-success">
                        <tr>
                            <th>NIK</th>
                            <th>Nama</th>
                            <th>Jenis Iuran</th>
                            <th>Nominal</th>
                            <th>Tanggal</th>
                            <th>Catatan</th>
                            <th>Dokumentasi</th>
                            <th>Status</th>
                            <th>Alasan Tolak</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($iurans as $iuran)
                        <tr>
                            <td>{{ $iuran->user->nik }}</td>
                            <td>{{ $iuran->user->name }}</td>
                            <td>{{ $iuran->jenis_iuran }}</td>
                            <td>Rp {{ number_format($iuran->nominal, 0, ',', '.') }}</td>
                            <td>{{ \Carbon\Carbon::parse($iuran->tanggal)->format('d-m-Y') }}</td>
                            <td>{{ $iuran->catatan ?? '-' }}</td>
                            <td class="text-center">
                                @if($iuran->dokumentasi)
                                    <a href="{{ asset('storage/' . $iuran->dokumentasi) }}" target="_blank" class="btn btn-outline-secondary btn-sm">
                                        <i class="bi bi-file-earmark-text"></i> Lihat
                                    </a>
                                @else
                                    <span class="text-muted">Tidak ada</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <form action="{{ route('admin.iuran.updateStatus', $iuran->id) }}" method="POST" class="d-flex align-items-center" style="gap: 0.25rem;">
                                    @csrf
                                    @method('PATCH')
                                    <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                                        <option value="terkirim" {{ $iuran->status == 'terkirim' ? 'selected' : '' }}>Terkirim</option>
                                        <option value="diterima" {{ $iuran->status == 'diterima' ? 'selected' : '' }}>Diterima</option>
                                        <option value="ditolak" {{ $iuran->status == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                                    </select>
                                    @if($iuran->status == 'ditolak')
                                        <input type="text" name="alasan_tolak" value="{{ $iuran->alasan_tolak }}" placeholder="Alasan tolak" class="form-control form-control-sm" />
                                        <button type="submit" class="btn btn-primary btn-sm ms-1">Simpan</button>
                                    @endif
                                </form>
                            </td>
                            <td>{{ $iuran->status == 'ditolak' ? ($iuran->alasan_tolak ?? '-') : '-' }}</td>
                            <td class="text-center">
                                <a href="{{ route('admin.iuran.show', $iuran->id) }}" class="btn btn-outline-info btn-sm">
                                    <i class="bi bi-eye"></i> Detail
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center text-muted">Tidak ada data iuran ditemukan.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-end mt-3">
                {{ $iurans->withQueryString()->links() }}
            </div>
        </div>
    </div>
</div>

@endsection
