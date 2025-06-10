@extends('layouts.app')

@section('content')
<div class="page-content">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow-sm rounded-4">
                <div class="card-body">
                    <h4>Detail Kaderisasi</h4>
                    <p class="text-subtitle text-muted">
                        Berikut merupakan daftar kaderisasi yang telah Anda input. Data yang sudah diverifikasi admin akan tampil di riwayat.
                    </p>

                    <p><strong>Judul:</strong><br> {{ $kaderisasi->judul }}</p>
                    <hr>

                    <p><strong>Tanggal:</strong><br> {{ \Carbon\Carbon::parse($kaderisasi->tanggal)->format('d M Y') }}</p>
                    <hr>

                    <p><strong>Peserta:</strong><br> {{ $kaderisasi->peserta ?? '-' }}</p>
                    <hr>

                    <p><strong>Status:</strong><br>
                        @switch($kaderisasi->status)
                            @case('terkirim')
                                <span class="badge bg-secondary">Terkirim</span>
                                @break
                            @case('diterima')
                                <span class="badge bg-success">Diterima</span>
                                @break
                            @case('ditolak')
                                <span class="badge bg-danger">Ditolak</span>
                                @break
                            @default
                                <span class="text-muted">-</span>
                        @endswitch
                    </p>
                    <hr>

@if($kaderisasi->status == 'ditolak' && !empty($kaderisasi->alasan_tolak))
    <p><strong>Alasan Ditolak:</strong><br> {{ $kaderisasi->alasan_tolak }}</p>
    <hr>
@endif


                    <p><strong>Catatan:</strong><br> {{ $kaderisasi->catatan ?? '-' }}</p>
                    <hr>

                    <p><strong>Dokumentasi:</strong></p>
                    @php
                        $files = json_decode($kaderisasi->dokumentasi, true);
                    @endphp

                    @if(is_array($files) && count($files))
                        <ul class="list-unstyled">
                            @foreach ($files as $file)
                                @php $ext = pathinfo($file, PATHINFO_EXTENSION); @endphp
                                <li class="mb-3">
                                    @if (in_array($ext, ['jpg', 'jpeg', 'png']))
                                        <img src="{{ asset('storage/' . $file) }}" alt="Dokumentasi" class="img-thumbnail" style="max-width: 200px;">
                                    @elseif ($ext === 'pdf')
                                        <a href="{{ asset('storage/' . $file) }}" target="_blank" class="btn btn-outline-primary btn-sm">Lihat PDF</a>
                                    @else
                                        <a href="{{ asset('storage/' . $file) }}" target="_blank" class="btn btn-outline-secondary btn-sm">Download File</a>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-muted">Tidak ada dokumentasi.</p>
                    @endif

                    <hr>

                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('kaderisasi.user.index') }}" class="btn btn-secondary">← Kembali</a>
                        <div>
                            <a href="{{ route('kaderisasi.user.edit', $kaderisasi->id) }}" class="btn btn-warning">Edit</a>
                            <form action="{{ route('kaderisasi.user.destroy', $kaderisasi->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger" onclick="return confirm('Yakin ingin menghapus data ini?')">
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
