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

        .btn-add {
            background-color: #198754;
            color: white;
            padding: 0.4rem 0.8rem;
            border-radius: 6px;
            text-decoration: none;
            display: inline-block;
            border: 1px solid #198754;
            transition: background-color 0.3s, border-color 0.3s;
        }

        .btn-add:hover {
            background-color: #157347;
            border-color: #157347;
            color: white;
            text-decoration: none;
        }

        .btn-success-custom {
            background-color: #198754;
            color: white;
            padding: 0.35rem 0.75rem;
            border-radius: 6px;
            border: 1px solid #198754;
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            font-size: 0.875rem;
            cursor: pointer;
            transition: background-color 0.3s, border-color 0.3s;
        }

        .btn-success-custom:hover {
            background-color: #157347;
            border-color: #157347;
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
            display: inline-block;
        }

        .badge-diterima {
            background-color: #198754;
            color: white;
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 0.875rem;
            display: inline-block;
        }

        .badge-ditolak {
            background-color: #dc3545;
            color: white;
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 0.875rem;
            display: inline-block;
        }

        .table th,
        .table td {
            vertical-align: middle;
        }

        .btn-danger-custom {
            background-color: #dc3545;
            color: white;
            padding: 0.35rem 0.75rem;
            border-radius: 6px;
            border: 1px solid #dc3545;
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            font-size: 0.875rem;
            cursor: pointer;
            transition: background-color 0.3s, border-color 0.3s;
        }

        .btn-danger-custom:hover {
            background-color: #bb2d3b;
            border-color: #bb2d3b;
            color: white;
        }

        /* Tambahan style untuk tombol bootstrap yang kamu gunakan di form filter */
        .btn-primary {
            background-color: #0d6efd;
            color: white;
            border-radius: 6px;
            border: 1px solid #0d6efd;
            padding: 0.35rem 0.75rem;
            font-size: 0.875rem;
            cursor: pointer;
            transition: background-color 0.3s, border-color 0.3s;
        }

        .btn-primary:hover {
            background-color: #0b5ed7;
            border-color: #0b5ed7;
            color: white;
        }

        .btn-secondary {
            background-color: #6c757d;
            color: white;
            border-radius: 6px;
            border: 1px solid #6c757d;
            padding: 0.35rem 0.75rem;
            font-size: 0.875rem;
            cursor: pointer;
            transition: background-color 0.3s, border-color 0.3s;
        }

        .btn-secondary:hover {
            background-color: #5c636a;
            border-color: #5c636a;
            color: white;
        }

    </style>


    <div class="mt-4">
        <div class="card-container shadow-sm border-0 mb-4">
            <div class="card-body">
                <div class="page-heading mb-3">
                    <h3>Data Kaderisasi</h3>
                    <p>Berikut merupakan daftar kaderisasi yang telah Anda input. Data yang sudah diverifikasi admin akan
                        tampil di riwayat.</p>
                </div>
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title mb-3">Filter & Ekspor Data Kaderisasi</h5>
                        <form method="GET" action="{{ route('kaderisasi.admin.index') }}">
                            <div class="row g-3 align-items-end">

                                {{-- Filter Status --}}
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

                                {{-- Tanggal Dari --}}
                                <div class="col-md-2">
                                    <label class="form-label">Tanggal Dari</label>
                                    <input type="date" name="tanggal_dari" class="form-control"
                                        value="{{ request('tanggal_dari') }}">
                                </div>

                                {{-- Tanggal Sampai --}}
                                <div class="col-md-2">
                                    <label class="form-label">Tanggal Sampai</label>
                                    <input type="date" name="tanggal_sampai" class="form-control"
                                        value="{{ request('tanggal_sampai') }}">
                                </div>

                                {{-- Pencarian --}}
                                <div class="col-md-3">
                                    <label class="form-label">Pencarian</label>
                                    <input type="text" name="search" class="form-control"
                                        placeholder="Judul atau peserta..." value="{{ request('search') }}">
                                </div>

                                {{-- Tombol Filter --}}
                                <div class="col-auto d-grid">
                                    <button type="submit" class="btn btn-outline-primary">
                                        <i class="bi bi-search"></i> Filter
                                    </button>
                                </div>

                                {{-- Tombol Reset --}}
                                <div class="col-auto d-grid">
                                    <a href="{{ route('kaderisasi.admin.index') }}"
                                        class="btn btn-outline-secondary">
                                        <i class="bi bi-arrow-clockwise"></i> Reset
                                    </a>
                                </div>

                                {{-- Tombol Ekspor PDF --}}
                                <div class="col-auto d-grid">
                                    <a href="{{ route('kaderisasi.admin.pdf', request()->query()) }}"
                                        class="btn btn-outline-danger" title="Ekspor PDF">
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
                                <th>User</th>
                                <th>Judul</th>
                                <th>Tanggal</th>
                                <th>Peserta</th>
                                <th>Status</th>
                                <th style="width: 200px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($kaderisasi as $item)
                                <tr>
                                    <td>{{ $item->user->name ?? '-' }}</td>
                                    <td>{{ $item->judul }}</td>
                                    <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d-m-Y') }}</td>
                                    <td>{{ $item->peserta }}</td>
                                    <td>
                                        @php
                                            $badgeClass = 'badge-terkirim';
                                            if ($item->status === 'diterima') {
                                                $badgeClass = 'badge-diterima';
                                            } elseif ($item->status === 'ditolak') {
                                                $badgeClass = 'badge-ditolak';
                                            }
                                        @endphp
                                        <span class="{{ $badgeClass }}">{{ ucfirst($item->status) }}</span>
                                    </td>
                                    <td>
                                        <a href="{{ route('kaderisasi.admin.show', $item->id) }}"
                                            class="btn btn-outline-info btn-sm me-1"> <i class="bi bi-eye"></i> Detail</a>
                                        <form action="{{ route('kaderisasi.admin.destroy', $item->id) }}" method="POST"
                                            class="d-inline" onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-outline-danger btn-sm me-1">
                                                <i class="bi bi-trash"></i>Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">Tidak ada data kaderisasi.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{ $kaderisasi->links() }}

            </div>
        </div>
    </div>
@endsection
