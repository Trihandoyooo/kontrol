@extends('layouts.app')

@section('content')
<style>
    body {
        background: #f6f9f8 !important;
    }

    .card-wrapper {
        max-width: 1000px;
        margin: 2rem auto;
        background: #fff;
        border-radius: 12px;
        padding: 2rem;
        box-shadow: 0 4px 16px rgba(0,0,0,0.06);
    }

    .section-title {
        font-weight: 700;
        font-size: 1rem;
        margin: 1.5rem 0 0.75rem;
        color: #333;
        padding-bottom: 6px;
        border-bottom: 2px solid #dee2e6;
    }

    .data-table {
        width: 100%;
        border-collapse: collapse;
    }

    .data-table tr {
        border-bottom: 1px dashed #ccc;
    }

    .data-label {
        width: 200px;
        padding: 8px 0;
        font-weight: 600;
        color: #444;
        text-align: left;
        vertical-align: top;
        position: relative;
        padding-right: 20px;
    }

    .data-label::after {
        content: ":";
        position: absolute;
        right: 8px;
    }

    .data-value {
        padding: 8px 0;
        color: #222;
    }

    .thumbnail {
        width: 120px;
        height: 80px;
        object-fit: cover;
        border: 1px solid #ccc;
        border-radius: 6px;
        transition: 0.3s;
    }

    .thumbnail:hover {
        transform: scale(1.03);
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
</style>

<div class="card-wrapper">
    <h4>Detail Iuran</h4>
    <p class="text-muted mb-4">Menampilkan detail iuran serta status verifikasi dari admin.</p>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- INFORMASI IURAN --}}
    <div class="section-title">Informasi Iuran</div>
    <table class="data-table mb-6">
        <tr><td class="data-label">NIK</td><td class="data-value">{{ $iuran->user->nik }}</td></tr>
        <tr><td class="data-label">Nama</td><td class="data-value">{{ $iuran->user->name }}</td></tr>
        <tr><td class="data-label">Jenis Iuran</td><td class="data-value">{{ $iuran->jenis_iuran }}</td></tr>
        <tr><td class="data-label">Nominal</td><td class="data-value">Rp {{ number_format($iuran->nominal,0,',','.') }}</td></tr>
        <tr><td class="data-label">Tanggal</td><td class="data-value">{{ \Carbon\Carbon::parse($iuran->tanggal)->format('d-m-Y') }}</td></tr>
        <tr><td class="data-label">Catatan</td><td class="data-value">{{ $iuran->catatan ?? '-' }}</td></tr>
        <tr>
            <td class="data-label">Status</td>
            <td class="data-value">
                @php
                    $badge = 'bg-warning text-dark';
                    if ($iuran->status === 'diterima') $badge = 'bg-success';
                    elseif ($iuran->status === 'ditolak') $badge = 'bg-danger';
                @endphp
                <span class="badge {{ $badge }}">{{ ucfirst($iuran->status) }}</span>
            </td>
        </tr>
        <tr><td class="data-label">Alasan Tolak</td><td class="data-value">{{ $iuran->alasan_tolak ?? '-' }}</td></tr>
    </table>

@php
    $dokumentasi = json_decode($iuran->dokumentasi, true);
@endphp

@if(is_array($dokumentasi) && count($dokumentasi) > 0)
    <div class="section-title">Dokumentasi</div>
    <div class="d-flex flex-wrap gap-3 mb-4">
        @foreach($dokumentasi as $file)
            <a href="{{ asset('storage/' . $file) }}" target="_blank">
                <img src="{{ asset('storage/' . $file) }}" alt="Dokumentasi" class="thumbnail">

            </a>
        @endforeach
    </div>
@endif


    {{-- VERIFIKASI ADMIN --}}
    <div class="section-title">Ubah Status Verifikasi</div>
    <form method="POST" action="{{ route('admin.iuran.status.update', $iuran->id) }}">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="status" class="form-label">Status</label>
            <select name="status" id="status" class="form-select" required onchange="toggleAlasanTolak(this.value)">
                <option value="terkirim" {{ $iuran->status == 'terkirim' ? 'selected' : '' }}>Terkirim</option>
                <option value="diterima" {{ $iuran->status == 'diterima' ? 'selected' : '' }}>Diterima</option>
                <option value="ditolak" {{ $iuran->status == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
            </select>
        </div>

        <div class="mb-3" id="alasan_tolak_container" style="display: {{ $iuran->status == 'ditolak' ? 'block' : 'none' }};">
            <label for="alasan_tolak" class="form-label">Alasan Tolak</label>
            <textarea name="alasan_tolak" id="alasan_tolak" class="form-control" rows="3">{{ old('alasan_tolak', $iuran->alasan_tolak) }}</textarea>
        </div>

        <button type="submit" class="btn btn-outline-primary">Simpan Status</button>
        <a href="{{ route('admin.iuran.index') }}" class="btn btn-outline-secondary">Kembali</a>
    </form>
</div>

<script>
    function toggleAlasanTolak(value) {
        const container = document.getElementById('alasan_tolak_container');
        container.style.display = (value === 'ditolak') ? 'block' : 'none';
    }
</script>
@endsection
