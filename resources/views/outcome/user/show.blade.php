@extends('layouts.app')

@section('content')
<style>
    body {
        background: #f6f9f8 !important;
    }

    .card-wrapper {
        max-width: 1400px;
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
    <h4>Detail Outcome</h4>
    <p class="text-muted mb-4">Menampilkan detail kegiatan outcome yang Anda kirimkan.</p>

    {{-- Informasi --}}
    <div class="section-title">Informasi Outcome</div>
    <table class="data-table mb-1">
        <tr><td class="data-label">Judul Output</td><td class="data-value">{{ $outcome->judul }}</td></tr>
        <tr><td class="data-label">Nama Kegiatan</td><td class="data-value">{{ $outcome->nama_kegiatan }}</td></tr>
        <tr><td class="data-label">Tanggal</td><td class="data-value">{{ \Carbon\Carbon::parse($outcome->tanggal)->format('d-m-Y') }}</td></tr>
        <tr><td class="data-label">Keterangan</td><td class="data-value">{{ $outcome->keterangan ?? '-' }}</td></tr>
        <tr><td class="data-label">Manfaat</td><td class="data-value">{{ $outcome->manfaat ?? '-' }}</td></tr>
        <tr><td class="data-label">Dapil</td><td class="data-value">{{ $outcome->dapil }}</td></tr>
        <tr>
            <td class="data-label">Status</td>
            <td class="data-value">
                @php
                    $badge = 'bg-warning text-dark';
                    if ($outcome->status === 'diterima') $badge = 'bg-success';
                    elseif ($outcome->status === 'ditolak') $badge = 'bg-danger';
                @endphp
                <span class="badge {{ $badge }}">{{ ucfirst($outcome->status) }}</span>
            </td>
        </tr>
    </table>

    {{-- Alasan Penolakan --}}
    @if($outcome->status === 'ditolak' && $outcome->alasan_tolak)
        <div class="alert alert-danger mt-3">
            <strong>Alasan Penolakan:</strong><br>
            {{ $outcome->alasan_tolak }}
        </div>
    @endif

    {{-- Dokumentasi --}}
    @if($outcome->dokumentasi)
        <div class="section-title">Dokumentasi</div>
        <div class="d-flex flex-wrap gap-3 mb-4">
            @foreach(json_decode($outcome->dokumentasi) as $file)
                @php $ext = pathinfo($file, PATHINFO_EXTENSION); @endphp
                @if(in_array($ext, ['jpg','jpeg','png','webp']))
                    <a href="{{ asset('storage/' . $file) }}" target="_blank">
                        <img src="{{ asset('storage/' . $file) }}" alt="Dokumentasi" class="thumbnail">
                    </a>
                @elseif($ext === 'pdf')
                    <a href="{{ asset('storage/' . $file) }}" target="_blank" class="btn btn-outline-primary btn-sm">Lihat PDF</a>
                @else
                    <a href="{{ asset('storage/' . $file) }}" target="_blank" class="btn btn-outline-secondary btn-sm">Download File</a>
                @endif
            @endforeach
        </div>
    @endif

    {{-- Aksi --}}
    <div class="d-flex justify-content-between mt-4">
        <a href="{{ route('outcome.user.index') }}" class="btn btn-secondary">← Kembali</a>
        <div>
            <a href="{{ route('outcome.user.edit', $outcome->id) }}" class="btn btn-warning">Edit</a>
            <form action="{{ route('outcome.user.destroy', $outcome->id) }}" method="POST" style="display:inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger" onclick="return confirm('Yakin ingin menghapus outcome ini?')">Hapus</button>
            </form>
        </div>
    </div>
</div>
@endsection
