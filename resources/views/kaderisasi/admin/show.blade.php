@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h2>Detail Kaderisasi</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card mb-4">
        <div class="card-body">
            <h4>{{ $kaderisasi->judul }}</h4>
            <p><strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($kaderisasi->tanggal)->format('d-m-Y') }}</p>
            <p><strong>Peserta:</strong> {{ $kaderisasi->peserta }}</p>
            <p><strong>Catatan:</strong> {{ $kaderisasi->catatan ?? '-' }}</p>
            <p><strong>Status:</strong> 
                @php
                    $badge = 'bg-warning text-dark';
                    if ($kaderisasi->status === 'diterima') $badge = 'bg-success';
                    else if ($kaderisasi->status === 'ditolak') $badge = 'bg-danger';
                @endphp
                <span class="badge {{ $badge }}">{{ ucfirst($kaderisasi->status) }}</span>
            </p>
            <p><strong>User:</strong> {{ $kaderisasi->user->name ?? '-' }}</p>

            @if($kaderisasi->dokumentasi)
                <p><strong>Dokumentasi:</strong></p>
                <ul>
                    @foreach(json_decode($kaderisasi->dokumentasi) as $file)
                        <li><a href="{{ asset('storage/' . $file) }}" target="_blank">{{ basename($file) }}</a></li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>

    <h4>Ubah Status Verifikasi</h4>
    <form method="POST" action="{{ route('kaderisasi.admin.updateStatus', $kaderisasi->id) }}">
        @csrf
        <div class="mb-3">
            <label for="status" class="form-label">Status</label>
            <select name="status" id="status" class="form-select" required onchange="toggleAlasanTolak(this.value)">
                <option value="terkirim" {{ $kaderisasi->status == 'terkirim' ? 'selected' : '' }}>Terkirim</option>
                <option value="diterima" {{ $kaderisasi->status == 'diterima' ? 'selected' : '' }}>Diterima</option>
                <option value="ditolak" {{ $kaderisasi->status == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
            </select>
        </div>
        <div class="mb-3" id="alasan_tolak_container" style="display: {{ $kaderisasi->status == 'ditolak' ? 'block' : 'none' }}">
            <label for="alasan_tolak" class="form-label">Alasan Tolak</label>
            <textarea name="alasan_tolak" id="alasan_tolak" class="form-control" rows="3">{{ old('alasan_tolak', $kaderisasi->alasan_tolak) }}</textarea>
        </div>

        <button type="submit" class="btn btn-primary">Simpan Status</button>
        <a href="{{ route('kaderisasi.admin.index') }}" class="btn btn-secondary">Kembali</a>
    </form>
</div>

<script>
function toggleAlasanTolak(value) {
    const container = document.getElementById('alasan_tolak_container');
    if(value === 'ditolak') {
        container.style.display = 'block';
    } else {
        container.style.display = 'none';
    }
}
</script>
@endsection
