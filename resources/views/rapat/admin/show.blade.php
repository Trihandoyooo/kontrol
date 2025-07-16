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

<div class="card-container mt-4">
    <div class="page-heading mb-3">
        <h3>Detail Rapat</h3>
        <p class="text-muted mb-4">Menampilkan detail kegiatan rapat dan status verifikasi.</p>
    </div>

    {{-- Informasi --}}
    <div class="section-title">Informasi Rapat</div>
    <table class="data-table mb-4">
        <tr><td class="data-label">Judul</td><td class="data-value">{{ $rapat->judul }}</td></tr>
        <tr><td class="data-label">Jenis Rapat</td><td class="data-value">{{ $rapat->jenis_rapat }}</td></tr>
        <tr><td class="data-label">Tanggal</td><td class="data-value">{{ \Carbon\Carbon::parse($rapat->tanggal)->format('d-m-Y') }}</td></tr>
        <tr><td class="data-label">Peserta</td><td class="data-value">{{ $rapat->peserta }}</td></tr>
        <tr><td class="data-label">Catatan</td><td class="data-value">{{ $rapat->catatan ?? '-' }}</td></tr>
        <tr>
            <td class="data-label">Status</td>
            <td class="data-value">
                @php
                    $badge = 'bg-warning text-dark';
                    if ($rapat->status === 'diterima') $badge = 'bg-success';
                    elseif ($rapat->status === 'ditolak') $badge = 'bg-danger';
                @endphp
                <span class="badge {{ $badge }}">{{ ucfirst($rapat->status) }}</span>
            </td>
        </tr>
        <tr><td class="data-label">User</td><td class="data-value">{{ $rapat->user->name ?? '-' }}</td></tr>
    </table>

    {{-- Alasan Penolakan --}}
    @if($rapat->status === 'ditolak' && $rapat->alasan_tolak)
        <div class="alert alert-danger">
            <strong>Alasan Penolakan:</strong><br>
            {{ $rapat->alasan_tolak }}
        </div>
    @endif

    {{-- Dokumentasi --}}
    @if($rapat->dokumentasi)
        <div class="section-title">Dokumentasi</div>
        <div class="d-flex flex-wrap gap-3 mb-4">
            @foreach(json_decode($rapat->dokumentasi) as $file)
                <a href="{{ asset('storage/' . $file) }}" target="_blank">
                    <img src="{{ asset('storage/' . $file) }}" alt="Dokumentasi" class="thumbnail">
                </a>
            @endforeach
        </div>
    @endif

    {{-- Verifikasi --}}
    <div class="section-title">Ubah Status Verifikasi</div>
    <form method="POST" action="{{ route('admin.rapat.updateStatus', $rapat->id) }}">
        @csrf
        @method('PATCH')

        <div class="mb-3">
            <label for="status" class="form-label">Status</label>
            <select name="status" id="status" class="form-select" required onchange="toggleAlasanTolak(this.value)">
                <option value="terkirim" {{ $rapat->status == 'terkirim' ? 'selected' : '' }}>Terkirim</option>
                <option value="diterima" {{ $rapat->status == 'diterima' ? 'selected' : '' }}>Diterima</option>
                <option value="ditolak" {{ $rapat->status == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
            </select>
        </div>

        <div class="mb-3" id="alasan_tolak_container" style="display: {{ $rapat->status == 'ditolak' ? 'block' : 'none' }};">
            <label for="alasan_tolak" class="form-label">Alasan Tolak</label>
            <textarea name="alasan_tolak" id="alasan_tolak" class="form-control" rows="3">{{ old('alasan_tolak', $rapat->alasan_tolak) }}</textarea>
        </div>

        <button type="submit" class="btn btn-outline-primary">Simpan Status</button>
        <a href="{{ route('admin.rapat.index') }}" class="btn btn-outline-secondary">Kembali</a>
    </form>
</div>

<script>
    function toggleAlasanTolak(value) {
        const container = document.getElementById('alasan_tolak_container');
        container.style.display = (value === 'ditolak') ? 'block' : 'none';
    }
</script>
@endsection
