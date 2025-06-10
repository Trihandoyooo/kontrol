@extends('layouts.app')

@section('content')
<div class="page-heading mb-4">
    <h3>Rapat Menunggu Konfirmasi</h3>
</div>

<div class="page-content">
    <div class="row justify-content-center">
        <div class="col-md-11">
            <div class="card shadow-sm rounded-4">
                <div class="card-body">

                    @if($rapats->isEmpty())
                        <div class="alert alert-info text-center rounded-3">
                            Tidak ada rapat menunggu verifikasi.
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover table-striped rounded-3">
                                <thead class="table-warning">
                                    <tr>
                                        <th>Jenis</th>
                                        <th>Judul</th>
                                        <th>Tanggal</th>
                                        <th>Status</th>
                                        <th>Alasan Ditolak</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($rapats as $rapat)
                                        <tr>
                                            <td>{{ ucfirst($rapat->jenis_rapat) }}</td>
                                            <td>{{ $rapat->judul }}</td>
                                            <td>{{ \Carbon\Carbon::parse($rapat->tanggal)->format('d-m-Y') }}</td>
                                            <td>
                                                @if($rapat->status == 'terkirim')
                                                    <span class="badge bg-warning text-dark">Terkirim</span>
                                                @elseif($rapat->status == 'ditolak')
                                                    <span class="badge bg-danger">Ditolak</span>
                                                @endif
                                            </td>
                                            <td>{{ $rapat->alasan_tolak ?? '-' }}</td>
                                            <td>
                                                <a href="{{ route('rapat.user.show', $rapat->id) }}" class="btn btn-sm btn-outline-info">
                                                    <i class="bi bi-eye"></i> Lihat
                                                </a>
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
    </div>
</div>
@endsection
