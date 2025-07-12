@extends('layouts.app')

@section('content')
@php use Illuminate\Support\Str; @endphp

<style>
    body {
        background: #f2f7f5 !important;
    }
    .form-label {
        font-weight: 600;
    }
    .form-control, .form-select {
        border-radius: 0.5rem;
    }
</style>


<div class="page-content">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow-sm rounded-4">
                <div class="card-body">
            <h3>Edit Data Kaderisasi Anda</h3>
            <p class="text-subtitle text-muted">
                Berikut merupakan menu edit data kaderisasi dapat digunakan untuk memperbaiki data yang sebelumnya telah bapak/ibu inputkan.
            </p>
                    {{-- Alasan tolak (jika ada) --}}
                    @if ($kaderisasi->status === 'ditolak' && $kaderisasi->alasan_tolak)
                        <div class="alert alert-danger">
                            <strong>Alasan ditolak:</strong> {{ $kaderisasi->alasan_tolak }}
                        </div>
                    @endif

                    {{-- Form Update --}}
                    <form action="{{ route('kaderisasi.user.update', $kaderisasi->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        {{-- Judul --}}
                        <div class="mb-3">
                            <label for="judul" class="form-label">Judul Kegiatan</label>
                            <input type="text" name="judul" id="judul" class="form-control"
                                   value="{{ old('judul', $kaderisasi->judul) }}" required>
                            @error('judul')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Tanggal --}}
                        <div class="mb-3">
                            <label for="tanggal" class="form-label">Tanggal</label>
                            <input type="date" name="tanggal" id="tanggal" class="form-control"
                                   value="{{ old('tanggal', \Carbon\Carbon::parse($kaderisasi->tanggal)->format('Y-m-d')) }}" required>
                            @error('tanggal')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Peserta --}}
                        <div class="mb-3">
                            <label for="peserta" class="form-label">Peserta</label>
                            <input type="text" name="peserta" id="peserta" class="form-control"
                                   value="{{ old('peserta', $kaderisasi->peserta) }}">
                            @error('peserta')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Catatan --}}
                        <div class="mb-3">
                            <label for="catatan" class="form-label">Catatan</label>
                            <textarea name="catatan" id="catatan" rows="4" class="form-control">{{ old('catatan', $kaderisasi->catatan) }}</textarea>
                            @error('catatan')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Upload dokumentasi baru --}}
                        <div class="mb-3">
                            <label for="dokumentasi" class="form-label">Upload Dokumentasi Baru (boleh lebih dari satu)</label>
                            <input type="file" name="dokumentasi[]" id="dokumentasi" class="form-control" multiple accept=".jpg,.jpeg,.png,.pdf">
                        </div>

                        {{-- Tombol --}}
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('kaderisasi.user.index') }}" class="btn btn-secondary">Batal</a>
                            <button type="submit" class="btn btn-success">Kirim Ulang</button>
                        </div>
                    </form>

                    {{-- Dokumentasi lama --}}
                    @if ($kaderisasi->dokumentasi)
                        @php $files = json_decode($kaderisasi->dokumentasi, true); @endphp
                        <hr>
                        <label class="form-label mt-4">Dokumentasi Saat Ini:</label>
                        <div class="row">
                            @foreach ($files as $index => $file)
                                <div class="col-md-4 mb-3 text-center">
                                    @if (Str::endsWith($file, ['jpg', 'jpeg', 'png']))
                                        <img src="{{ asset('storage/' . $file) }}"
                                             alt="Dokumentasi"
                                             class="img-fluid rounded mb-2"
                                             style="max-height: 150px; object-fit: cover;">
                                    @else
                                        <a href="{{ asset('storage/' . $file) }}" target="_blank" class="d-block mb-2">
                                            {{ basename($file) }}
                                        </a>
                                    @endif

                                    {{-- Form Hapus Dokumentasi (TIDAK di dalam form utama) --}}
                                    <form action="{{ route('kaderisasi.user.deleteFile', ['id' => $kaderisasi->id, 'index' => $index]) }}" method="POST" onsubmit="return confirm('Hapus file ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                    </form>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
