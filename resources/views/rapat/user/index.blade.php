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

    .controls-container {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
        align-items: center;
    }

    .btn-add {
        background-color: #198754;
        color: white;
        padding: 0.4rem 0.8rem;
        border-radius: 6px;
        text-decoration: none;
    }

    .btn-add:hover {
        background-color: #157347;
    }

    .btn-success-custom {
        background-color: #198754;
        color: white;
        padding: 0.35rem 0.75rem;
        border-radius: 6px;
        border: none;
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        font-size: 0.875rem;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    .btn-success-custom:hover {
        background-color: #157347;
        color: white;
    }

    .btn-light-custom {
        background-color: #f8f9fa;
        color: #212529;
        padding: 0.35rem 0.75rem;
        border-radius: 6px;
        border: 1px solid #ced4da;
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        font-size: 0.875rem;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    .btn-light-custom:hover {
        background-color: #e2e6ea;
        color: #212529;
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

    .table th, .table td {
        vertical-align: middle;
    }

    .btn-danger-custom {
        background-color: #dc3545;
        color: white;
        padding: 0.35rem 0.75rem;
        border-radius: 6px;
        border: none;
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        font-size: 0.875rem;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    .btn-danger-custom:hover {
        background-color: #bb2d3b;
        color: white;
    }
</style>

<!-- Main Content -->
<div class="container mt-4">
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">

            <!-- Flash Message -->
            @if(session('success'))
                <div class="alert alert-success mb-3">{{ session('success') }}</div>
            @endif

            <h4>Daftar Rapat Anda</h4>
            <p class="text-subtitle text-muted">
                Berikut merupakan menu dari iuran yang dapat digunakan untuk melaporkan segala iuran yang telah bapak/ibu lakukan
            </p>

            <!-- Controls -->
            <div class="controls-container mb-4">
                <form method="GET" action="{{ route('rapat.user.index') }}" class="d-flex flex-wrap gap-2 align-items-center">
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm w-auto" placeholder="Search...">
                    <button type="submit" class="btn btn-light-custom btn-sm">
                        <i class="bi bi-funnel"></i> Filter
                    </button>
                    <a href="{{ route('rapat.user.index', ['export' => 'csv', 'search' => request('search')]) }}" class="btn btn-light-custom btn-sm">
                        <i class="bi bi-file-earmark-arrow-down"></i> Export
                    </a>
                </form>

                <div class="ms-auto">
                    <a href="{{ route('rapat.user.create') }}" class="btn btn-success">
                        <i class="bi bi-plus-lg"></i> Tambah Rapat
                    </a>
                </div>
            </div>

            <!-- Rapat Menunggu Konfirmasi -->
            @if($rapatsMenunggu->isNotEmpty())
                <div class="mb-4">
                    <h5>Rapat Menunggu Konfirmasi</h5>
                    <small class="text-muted d-block mb-2">Rapat yang menunggu persetujuan admin.</small>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Judul</th>
                                    <th>Jenis</th>
                                    <th>Tanggal</th>
                                    <th>Dokumentasi</th>
                                    <th>Catatan</th>
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
                                                    <a href="{{ asset('storage/' . $file) }}" target="_blank" class="btn btn-sm btn-success">Lihat</a>
                                                @endforeach
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>{{ $rapat->catatan ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            <!-- Riwayat Rapat -->
            <h5>Riwayat Rapat</h5>
            <small class="text-muted d-block mb-2">Riwayat rapat dengan status terkini.</small>

            @if($rapats->isEmpty())
                <div class="alert alert-info text-center">Tidak ada data rapat.</div>
            @else
                <div class="table-responsive">
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
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($rapats as $rapat)
                                <tr>
                                    <td>{{ ucfirst($rapat->jenis_rapat) }}</td>
                                    <td>{{ $rapat->judul }}</td>
                                    <td>{{ \Carbon\Carbon::parse($rapat->tanggal)->format('d-m-Y') }}</td>
                                    <td>{{ $rapat->peserta ?? '-' }}</td>
                                    <td>
                                        @if($rapat->status == 'terkirim') 
                                            <span class="badge badge-terkirim">Terkirim</span>
                                        @elseif($rapat->status == 'diterima') 
                                            <span class="badge badge-diterima">Diterima</span>
                                        @elseif($rapat->status == 'ditolak') 
                                            <span class="badge badge-ditolak">Ditolak</span>
                                        @else 
                                            <span>-</span> 
                                        @endif
                                    </td>
                                    <td>
                                        @php $files = json_decode($rapat->dokumentasi); @endphp
                                        @if($files && count($files))
                                            @foreach($files as $file)
                                                <a href="{{ asset('storage/' . $file) }}" target="_blank" class="btn btn-sm btn-secondary">Lihat</a>
                                            @endforeach
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>{{ $rapat->catatan ?? '-' }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('rapat.user.show', $rapat->id) }}" class="btn btn-sm btn-success">
                                            <i class="bi bi-eye"></i> Lihat
                                        </a>
                                        <a href="{{ route('rapat.user.edit', $rapat->id) }}" class="btn btn-sm btn-warning">
                                            <i class="bi bi-pencil-square"></i> Edit
                                        </a>
                                        <form action="{{ route('rapat.user.destroy', $rapat->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Yakin ingin hapus?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="bi bi-trash"></i> Hapus
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

        </div>
    </div>
</div>

@endsection
