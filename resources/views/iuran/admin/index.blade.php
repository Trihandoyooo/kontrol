@extends('layouts.app')

@section('content')

<style>
    body {
        background: #f2f7f5 !important;
    }

    .tab-pane h2, .tab-pane h3, .tab-pane h5 {
        color: #1b5e20;
    }
    .badge-terkirim {
        background-color: #ffc107;
        color: black;
        padding: 4px 8px;
        border-radius: 6px;
        font-size: 0.85rem;
    }
    .badge-diterima {
        background-color: #198754;
        color: white;
        padding: 4px 8px;
        border-radius: 6px;
        font-size: 0.85rem;
    }
    .badge-ditolak {
        background-color: #dc3545;
        color: white;
        padding: 4px 8px;
        border-radius: 6px;
        font-size: 0.85rem;
    }
</style>

<div class="mt-3">
    <div class="card-container">

        <!-- Tabs -->
        <ul class="nav nav-tabs mb-4" id="iuranTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="statistik-tab" data-bs-toggle="tab" data-bs-target="#statistik" type="button" role="tab">Ringkasan Statistik</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="alokasi-tab" data-bs-toggle="tab" data-bs-target="#alokasi" type="button" role="tab">Alokasi Dana</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="iuran-tab" data-bs-toggle="tab" data-bs-target="#iuran" type="button" role="tab">Data Iuran</button>
            </li>
        </ul>


    <div class="tab-content" id="iuranTabContent">
        <!-- Tab Statistik -->
        <div class="tab-pane fade show active" id="statistik" role="tabpanel">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body">
                    <h2>Ringkasan Statistik Iuran</h2>
                    <p class="text-subtitle text-muted">Berikut merupakan ringkasan total iuran dan rekap pengguna.</p>

                    <!-- Ringkasan Semua Total -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="card shadow-sm border-0" style="background-color: #198754; color: white;">
                                <div class="card-body">
                                    <h6 class="card-title text-white">Total Iuran Diterima</h6>
                                    <h5 class="fw-bold text-white">Rp {{ number_format($totalDiterima, 0, ',', '.') }}</h5>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card shadow-sm border-0" style="background-color: #ffc107; color: black;">
                                <div class="card-body">
                                    <h6 class="card-title">Total Iuran Terkirim/Belum Di Verifikasi</h6>
                                    <h5 class="fw-bold text-black">Rp {{ number_format($totalTerkirim, 0, ',', '.') }}</h5>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card shadow-sm border-0" style="background-color: #dc3545; color: white;">
                                <div class="card-body">
                                    <h6 class="card-title">Total Iuran Ditolak</h6>
                                    <h5 class="fw-bold text-white">Rp {{ number_format($totalDitolak, 0, ',', '.') }}</h5>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Total Per User -->
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-body">
                            <h5 class="card-title">Total Iuran Diterima per Pengguna</h5>
                            <div class="table-responsive">
                                <table class="table table-sm table-striped align-middle">
                                    <thead>
                                        <tr>
                                            <th>Nama Pengguna</th>
                                            <th>Total Diterima</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($semuaTotalUser as $item)
                                            <tr>
                                                <td>{{ $item->user->name }}</td>
                                                <td><span class="badge bg-success text-white">Rp {{ number_format($item->total, 0, ',', '.') }}</span></td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="2" class="text-muted text-center">Belum ada data</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab Alokasi Dana -->
        <div class="tab-pane fade" id="alokasi" role="tabpanel">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="card-title mb-0">Alokasi Dana oleh Admin</h5>
                        <a href="{{ route('admin.alokasi.create') }}" class="btn btn-sm btn-outline-success">
                            <i class="bi bi-plus-circle"></i> Tambah Alokasi
                        </a>
                    </div>

                    <!-- Grafik Pie Alokasi Dana -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body">
        <div style="max-width: 300px; margin: auto;">
    <canvas id="alokasiPieChart"></canvas>
</div>
    </div>
</div>

                    <div class="table-responsive">
                       <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Nama Kegiatan</th>
                                    <th>Jumlah</th>
                                    <th>Tanggal</th>
                                    <th>Deskripsi</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($alokasis as $alokasi)
                                    <tr>
                                        <td>{{ $alokasi->nama_kegiatan }}</td>
                                        <td>Rp {{ number_format($alokasi->jumlah, 0, ',', '.') }}</td>
                                        <td>{{ \Carbon\Carbon::parse($alokasi->tanggal)->format('d-m-Y') }}</td>
                                        <td>{{ \Illuminate\Support\Str::limit($alokasi->deskripsi, 50) }}</td>
<td>
    <a href="{{ route('admin.alokasi.show', $alokasi->id) }}" class="btn btn-outline-info btn-sm">
        <i class="bi bi-eye me-1"></i>Show
    </a>
    <a href="{{ route('admin.alokasi.edit', $alokasi->id) }}" class="btn btn-outline-warning btn-sm">
        <i class="bi bi-pencil me-1"></i>Edit
    </a>
    <form action="{{ route('admin.alokasi.destroy', $alokasi->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus alokasi ini?')">
        @csrf
        @method('DELETE')
        <button class="btn btn-outline-danger btn-sm">
            <i class="bi bi-trash me-1"></i>Hapus
        </button>
    </form>
</td>

                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">Belum ada alokasi dana dicatat.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab Data Iuran -->
        <div class="tab-pane fade" id="iuran" role="tabpanel">
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title mb-3">Filter & Ekspor Data Iuran</h5>
                    <form method="GET" action="{{ route('admin.iuran.index') }}">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-3">
                                <label class="form-label">Jenis Iuran</label>
                                <select name="jenis_iuran" class="form-select">
                                    <option value="">Semua</option>
                                    @foreach(['Iuran Bulanan', 'Sumbangan Fraksi', 'Dana Infaq Shadaqoh dan Zakat (ZIS)', 'Dana Khitmat', 'Dana Kompensasi Kepada Caleg', 'Dana Insidensial', 'Dana Lainnya'] as $jenis)
                                        <option value="{{ $jenis }}" {{ request('jenis_iuran') == $jenis ? 'selected' : '' }}>{{ $jenis }}</option>
                                    @endforeach
                                </select>
                            </div>

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
                                <label class="form-label">Cari Nama</label>
                                <input type="text" name="nama" class="form-control" placeholder="Nama user..." value="{{ request('nama') }}">
                            </div>

                            <div class="col-auto d-grid">
                                <button type="submit" class="btn btn-outline-primary"><i class="bi bi-search"></i> Filter</button>
                            </div>
                            <div class="col-auto d-grid">
                                <a href="{{ route('admin.iuran.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-clockwise"></i> Reset</a>
                            </div>
                            <div class="col-auto d-grid">
                                <a href="{{ route('admin.iuran.pdf', request()->query()) }}" class="btn btn-outline-danger" title="Ekspor PDF"><i class="bi bi-file-earmark-pdf"></i> PDF</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Tabel Iuran -->
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Jenis Iuran</th>
                            <th>Nominal</th>
                            <th>Tanggal</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($iurans as $iuran)
                            <tr>
                                <td>{{ $iuran->user->name }}</td>
                                <td>{{ $iuran->jenis_iuran }}</td>
                                <td>Rp {{ number_format($iuran->nominal, 0, ',', '.') }}</td>
                                <td>{{ \Carbon\Carbon::parse($iuran->tanggal)->format('d-m-Y') }}</td>
                                <td>
                                    @php
                                        $badgeClass = 'badge-terkirim';
                                        if ($iuran->status === 'diterima') $badgeClass = 'badge-diterima';
                                        elseif ($iuran->status === 'ditolak') $badgeClass = 'badge-ditolak';
                                    @endphp
                                    <span class="{{ $badgeClass }}">{{ ucfirst($iuran->status) }}</span>
                                </td>
                                <td>
                                    <a href="{{ route('admin.iuran.show', $iuran->id) }}" class="btn btn-outline-info btn-sm me-1"><i class="bi bi-eye"></i> Detail</a>
                                    <form action="{{ route('admin.iuran.destroy', $iuran->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-outline-danger btn-sm me-1"><i class="bi bi-trash"></i> Hapus</button>
                                    </form>
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

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('alokasiPieChart').getContext('2d');

        const data = {
            labels: {!! json_encode(array_column($grafikAlokasi, 'label')) !!},
            datasets: [{
                label: 'Distribusi Dana',
                data: {!! json_encode(array_column($grafikAlokasi, 'value')) !!},
                backgroundColor: [
                    '#198754', '#FFC107', '#F44336', '#00BCD4', '#9C27B0', '#FF9800', '#3F51B5', '#795548'
                ],
                borderWidth: 1
            }]
        };

        const config = {
            type: 'pie',
            data: data,
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.label || '';
                                let value = context.raw || 0;
                                return label + ': Rp ' + value.toLocaleString('id-ID');
                            }
                        }
                    }
                }
            },
        };

        new Chart(ctx, config);
    });
</script>
@endpush
@endsection
