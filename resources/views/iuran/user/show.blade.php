@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h3>Detail Iuran</h3>

    <div class="card">
        <div class="card-body">
            <p><strong>Jenis Iuran:</strong> {{ $iuran->jenis_iuran }}</p>
            <p><strong>Nominal:</strong> Rp {{ number_format($iuran->nominal, 0, ',', '.') }}</p>
            <p><strong>Tanggal:</strong> {{ $iuran->tanggal }}</p>
            <p><strong>Catatan:</strong> {{ $iuran->catatan }}</p>
            <p><strong>Dokumentasi:</strong>
                @if($iuran->dokumentasi)
                    <a href="{{ asset('storage/' . $iuran->dokumentasi) }}" target="_blank">Lihat File</a>
                @else
                    Tidak ada
                @endif
            </p>
        </div>
    </div>

    <a href="{{ route('iuran.index') }}" class="btn btn-secondary mt-3">Kembali</a>
</div>
@endsection
