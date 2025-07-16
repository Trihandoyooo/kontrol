@extends('layouts.app')

@section('content')

<style>
    body {
        background: #e6f4ec !important;
    }

    .badge {
        padding: 6px 12px;
        font-size: 0.85rem;
        border-radius: 6px;
    }

    .table td, .table th {
        vertical-align: middle;
        padding-top: 1rem;
        padding-bottom: 1rem;
    }

    .table tbody tr + tr {
        border-top: 12px solid #f2f7f5;
    }

    .progress {
        height: 12px;
        background-color: #d4edda;
    }

    .progress-bar {
        background-color: rgb(11, 83, 43);
    }
</style>

<div class="mt-4">
    <div class="card-container shadow-sm border-0 mb-4">
        <div class="card-body">
            <div class="page-heading mb-3">
                <h3 class="mb-1">Iuran Anda</h3>
                <p class="text-muted mb-0">Berikut merupakan informasi untuk iuran yang bapak/ibu telah lakukan.</p>
            </div>

            {{-- Header dan Tombol Tambah --}}
            <div class="d-flex justify-content-between my-3 align-items-center flex-wrap gap-5">
                <a href="{{ route('iuran.user.create') }}" class="btn btn-outline-success flex-shrink-0 mb-2 mb-md-0">
                    <i class="bi bi-plus-circle"></i> Tambah Iuran
                </a>
                {{-- Search dan Filter --}}
                <form method="GET" class="flex-grow-1">
                    <div class="row g-2 align-items-center flex-nowrap">
                        <div class="col-12 col-md-5 flex-grow-1">
                            <input type="text" name="search" value="{{ request('search') }}" class="form-control"
                                placeholder="Cari jenis iuran, catatan, status...">
                        </div>
                        <div class="col-12 col-md-4 flex-grow-1">
                            <select name="status" class="form-select">
                                <option value="">Filter Status</option>
                                <option value="terkirim" {{ request('status') == 'terkirim' ? 'selected' : '' }}>Terkirim</option>
                                <option value="diterima" {{ request('status') == 'diterima' ? 'selected' : '' }}>Diterima</option>
                                <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                            </select>
                        </div>
                        <div class="col-auto flex-shrink-0">
                            <button class="btn btn-success w-100">
                                <i class="bi bi-search"></i> Cari
                            </button>
                        </div>
                        <div class="col-auto flex-shrink-0">
                            <a href="{{ route('iuran.user.index') }}" class="btn btn-secondary w-100">
                                <i class="bi bi-x-circle"></i> Reset
                            </a>
                        </div>
                    </div>
                </form>
            </div>


            {{-- Total Iuran Disetujui --}}
            <div class="alert bg-success text-white fw-bold d-flex justify-content-between align-items-center shadow-sm rounded-3 px-4 py-3 mb-4">
                <div>
                    <i class="bi bi-cash-stack me-2"></i> Total Iuran Disetujui
                </div>
                <h4 class="mb-0">Rp {{ number_format($totalDisetujui, 0, ',', '.') }}</h4>
            </div>

            {{-- Progress Per Kategori --}}
            <div class="page-heading">
                <h3>Progress Per Kategori</h3>
            </div>
            <div class="overflow-auto pb-2">
                <div class="d-flex flex-row gap-3" style="min-width: 600px;">
                    @foreach($progressPerKategori as $kategori => $data)
                        <div class="card border-0 shadow-sm flex-shrink-0" style="min-width: 250px;">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-2 gap-3">
                                    <h6 class="mb-0 text-truncate" title="{{ $kategori }}">{{ $kategori }}</h6>
                                    <span class="badge bg-success">{{ $data['persentase'] }}%</span>
                                </div>
                                <p class="mb-2 fw-bold small">
                                    Rp {{ number_format($data['terkumpul'], 0, ',', '.') }} /
                                    Rp {{ number_format($data['target'], 0, ',', '.') }}
                                </p>
                                <div class="progress">
                                    <div class="progress-bar" role="progressbar"
                                        style="width: {{ $data['persentase'] }}%;"
                                        aria-valuenow="{{ $data['persentase'] }}" aria-valuemin="0" aria-valuemax="100">
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <hr class="my-4">

            {{-- Tabel Menunggu Verifikasi --}}
            @if($iurans->where('status', 'terkirim')->isNotEmpty())
                <h4 class="mb-3">Menunggu Verifikasi</h4>
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Jenis Iuran</th>
                                    <th>Nominal</th>
                                    <th>Tanggal</th>
                                    <th>Dokumentasi</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($iurans->where('status', 'terkirim') as $iuran)
                                    <tr>
                                        <td>{{ $iuran->jenis_iuran }}</td>
                                        <td>Rp {{ number_format($iuran->nominal, 0, ',', '.') }}</td>
                                        <td>{{ \Carbon\Carbon::parse($iuran->tanggal)->format('d-m-Y') }}</td>
                                        <td>
                                            @php $files = json_decode($iuran->dokumentasi, true); @endphp
                                            @if($files)
                                                @foreach($files as $file)
                                                    <a href="{{ asset('storage/' . $file) }}" target="_blank" class="btn btn-outline-secondary btn-sm mb-1">
                                                        <i class="bi bi-file-earmark-text"></i> Lihat
                                                    </a>
                                                @endforeach
                                            @else
                                                <span class="text-muted">Tidak ada</span>
                                            @endif
                                        </td>
                                        <td><span class="badge bg-warning text-dark">Terkirim</span></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            {{-- Tabel Iuran Diterima --}}
            <div class="page-heading mb-3">
                <h3>Iuran Disetujui</h3>
            </div>
            <div class="card shadow-sm border-0">
                <div class="card-body table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Jenis Iuran</th>
                                <th>Nominal</th>
                                <th>Tanggal</th>
                                <th>Catatan</th>
                                <th>Dokumentasi</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($iurans->where('status', 'diterima') as $iuran)
                                <tr>
                                    <td>{{ $iuran->jenis_iuran }}</td>
                                    <td>Rp {{ number_format($iuran->nominal, 0, ',', '.') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($iuran->tanggal)->format('d-m-Y') }}</td>
                                    <td>{{ $iuran->catatan ?? '-' }}</td>
                                    <td>
                                        @php $files = json_decode($iuran->dokumentasi, true); @endphp
                                        @if($files)
                                            @foreach($files as $file)
                                                <a href="{{ asset('storage/' . $file) }}" target="_blank" class="btn btn-outline-secondary btn-sm mb-1">
                                                    <i class="bi bi-file-earmark-text"></i> Lihat
                                                </a>
                                            @endforeach
                                        @else
                                            <span class="text-muted">Tidak ada</span>
                                        @endif
                                    </td>
                                    <td><span class="badge bg-success">Diterima</span></td>
                                    <td>
                                        <a href="{{ route('iuran.user.show', $iuran->id) }}" class="btn btn-sm btn-outline-info">
                                            <i class="bi bi-eye"></i> Detail
                                        </a>
                                        <a href="{{ route('iuran.user.edit', $iuran->id) }}" class="btn btn-sm btn-outline-warning mb-1">
                                            <i class="bi bi-pencil-square"></i> Edit
                                        </a>
                                        <form action="{{ route('iuran.user.destroy', $iuran->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin hapus?')">
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
                                    <td colspan="7" class="text-center text-muted">Belum ada iuran yang disetujui.</td>
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
