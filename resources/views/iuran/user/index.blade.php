@extends('layouts.app')

@section('content')

<style>
    body {
        background: #e6f4ec !important;
    }
</style>

<div class="container mt-4">
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <h2 class="mb-3">Daftar Semua Iuran</h2>

            <div class="alert alert-success fw-bold" role="alert">
                Total Semua Iuran: Rp {{ number_format($totalKeseluruhan, 0, ',', '.') }}
            </div>

            <h4 class="mb-3">Progress Per Kategori</h4>
            <div class="row">
                @foreach($progressPerKategori as $kategori => $data)
                    <div class="col-md-4 mb-4">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h5 class="card-title mb-0">{{ $kategori }}</h5>
                                    <span class="badge bg-success text-white fw-bold">
                                        {{ $data['persentase'] }}%
                                    </span>
                                </div>

                                <p class="mb-2 fw-bold">
                                    Rp {{ number_format($data['terkumpul'], 0, ',', '.') }}
                                    <span class="text-bold">/ Rp {{ number_format($data['target'], 0, ',', '.') }}</span>
                                </p>

                                <div class="progress" style="height: 16px; background-color: #d4edda;">
                                    <div class="progress-bar" role="progressbar"
                                        style="width: {{ $data['persentase'] }}%; background-color:rgb(25, 106, 44);"
                                        aria-valuenow="{{ $data['persentase'] }}" aria-valuemin="0" aria-valuemax="100">
                                        {{ $data['persentase'] }}%
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <hr class="my-4">

            <h4 class="mb-3">Detail Transaksi Iuran</h4>

            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <table class="table table-bordered table-striped">
                        <thead class="table-success">
                            <tr>
                                <th>Jenis Iuran</th>
                                <th>Nominal</th>
                                <th>Tanggal</th>
                                <th>Catatan</th>
                                <th>Dokumentasi</th>
                                <th>Status</th>
                                <th>Alasan Ditolak</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($iurans as $iuran)
                            <tr>
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
                                    @if ($iuran->status === 'terkirim')
                                        <span class="badge bg-warning text-dark">Terkirim</span>
                                    @elseif ($iuran->status === 'diterima')
                                        <span class="badge bg-success">Diterima</span>
                                    @elseif ($iuran->status === 'ditolak')
                                        <span class="badge bg-danger">Ditolak</span>
                                    @endif
                                </td>
                                <td>
                                    {{ $iuran->status === 'ditolak' ? ($iuran->alasan_tolak ?? '-') : '-' }}
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('iuran.user.show', $iuran->id) }}" class="btn btn-outline-info btn-sm me-1">
                                        <i class="bi bi-eye"></i> Detail
                                    </a>
                                    <a href="{{ route('iuran.user.edit', $iuran->id) }}" class="btn btn-outline-warning btn-sm me-1">
                                        <i class="bi bi-pencil-square"></i> Edit
                                    </a>
                                    <form action="{{ route('iuran.user.destroy', $iuran->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Yakin ingin hapus?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-sm">
                                            <i class="bi bi-trash"></i> Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted">Belum ada data iuran.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="mt-3">
                        <a href="{{ route('iuran.user.create') }}" class="btn btn-success">
                            <i class="bi bi-plus-circle"></i> Tambah Iuran
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

@endsection
