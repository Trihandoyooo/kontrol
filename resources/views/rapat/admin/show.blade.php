@extends('layouts.app')

@section('content')

<div class="container mt-4">
    <h2>Detail Rapat</h2>

    <a href="{{ route('admin.rapat.index') }}" class="btn btn-secondary mb-3">Kembali</a>

    <div class="card">
        <div class="card-body">
            <h5 class="card-title">{{ $rapat->judul }}</h5>
            <p><strong>Jenis Rapat:</strong> {{ $rapat->jenis_rapat }}</p>
            <p><strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($rapat->tanggal)->format('d-m-Y') }}</p>
            <p><strong>Peserta:</strong> {{ $rapat->peserta }}</p>
            <p><strong>Catatan:</strong> {{ $rapat->catatan ?? '-' }}</p>

            <p><strong>Status:</strong> 
                @if ($rapat->status === 'terkirim')
                    <span class="badge bg-warning text-dark">Terkirim</span>
                @elseif ($rapat->status === 'diterima')
                    <span class="badge bg-success">Diterima</span>
                @elseif ($rapat->status === 'ditolak')
                    <span class="badge bg-danger">Ditolak</span>
                @endif
            </p>

            @if($rapat->status === 'ditolak' && $rapat->alasan_tolak)
                <div class="alert alert-danger">
                    <strong>Alasan Penolakan:</strong> <br>
                    {{ $rapat->alasan_tolak }}
                </div>
            @endif

            <hr>

            <h6>Dokumentasi:</h6>
            @if($rapat->dokumentasi)
                <div class="d-flex flex-wrap gap-2">
                    @foreach(json_decode($rapat->dokumentasi) as $file)
                        @php
                            $ext = pathinfo($file, PATHINFO_EXTENSION);
                        @endphp

                        @if(in_array($ext, ['jpg', 'jpeg', 'png']))
                            <img src="{{ asset('storage/' . $file) }}" alt="Dokumentasi" style="max-width:150px; max-height:150px; object-fit:cover; border:1px solid #ddd; padding:3px;">
                        @elseif($ext === 'pdf')
                            <a href="{{ asset('storage/' . $file) }}" target="_blank" class="btn btn-outline-primary btn-sm mb-2">
                                <i class="bi bi-file-earmark-pdf"></i> Lihat PDF
                            </a>
                        @else
                            <a href="{{ asset('storage/' . $file) }}" target="_blank" class="btn btn-outline-secondary btn-sm mb-2">
                                Lihat File
                            </a>
                        @endif
                    @endforeach
                </div>
            @else
                <p>Tidak ada dokumentasi.</p>
            @endif
        </div>
    </div>
</div>

@endsection
