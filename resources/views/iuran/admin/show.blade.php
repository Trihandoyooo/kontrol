@extends('layouts.app')

@section('content')
<div class="page-heading">
    <h3>Detail Iuran</h3>
</div>

<div class="page-content">
    <div class="card shadow-sm">
        <div class="card-body">
            <p><strong>NIK:</strong> {{ $iuran->user->nik }}</p>
            <p><strong>Nama:</strong> {{ $iuran->user->name }}</p>
            <p><strong>Jenis Iuran:</strong> {{ $iuran->jenis_iuran }}</p>
            <p><strong>Nominal:</strong> Rp {{ number_format($iuran->nominal,0,',','.') }}</p>
            <p><strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($iuran->tanggal)->format('d-m-Y') }}</p>
            <p><strong>Catatan:</strong> {{ $iuran->catatan ?? '-' }}</p>
            <p><strong>Status:</strong> {{ ucfirst($iuran->status) }}</p>
            <p><strong>Alasan Tolak:</strong> {{ $iuran->alasan_tolak ?? '-' }}</p>
            <p><strong>Dokumentasi:</strong>
                @if($iuran->dokumentasi)
                    <a href="{{ asset('storage/' . $iuran->dokumentasi) }}" target="_blank" class="btn btn-outline-secondary btn-sm">
                        Lihat Dokumentasi
                    </a>
                @else
                    Tidak ada
                @endif
            </p>
            <a href="{{ route('admin.iuran.index') }}" class="btn btn-secondary mt-3">Kembali</a>
            <a href="{{ route('admin.iuran.edit', $iuran->id) }}" class="btn btn-primary mt-3">Edit Status</a>
        </div>
    </div>
</div>
@endsection
