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

<div class="container mt-3">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow-sm rounded-4">
                <div class="card-body">
                    <h2 class="card-title">Edit Rapat</h2>
                    <p class="text-muted">Silakan ubah informasi rapat sesuai kebutuhan.</p>

                    {{-- Dokumentasi lama (DI LUAR form utama) --}}
                    @if($rapat->dokumentasi)
                        @php $files = json_decode($rapat->dokumentasi, true); @endphp
                        <div class="mb-4">
                            <h5>Dokumentasi Sebelumnya</h5>
                            <div class="row">
                                @foreach($files as $index => $file)
                                    <div class="col-md-4 text-center mb-3">
                                        @if(Str::endsWith($file, ['jpg', 'jpeg', 'png']))
                                            <img src="{{ asset('storage/' . $file) }}" class="img-fluid rounded mb-2" style="max-height: 150px;">
                                        @else
                                            <a href="{{ asset('storage/' . $file) }}" target="_blank">{{ basename($file) }}</a>
                                        @endif

                                        {{-- Form hapus file (di luar form utama) --}}
                                        <form action="{{ route('rapat.user.deleteFile', ['id' => $rapat->id, 'index' => $index]) }}" method="POST" onsubmit="return confirm('Hapus file ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger mt-1">Hapus</button>
                                        </form>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Form Edit --}}
                    <form action="{{ route('rapat.user.update', $rapat->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        {{-- Jenis Rapat --}}
                        <div class="mb-3">
                            <label for="jenis_rapat" class="form-label">Jenis Rapat</label>
                            <select name="jenis_rapat" id="jenis_rapat" class="form-select" required>
                                @foreach([
                                    'rapat komisi', 'rapat paripurna', 'rapat fraksi',
                                    'rapat lintas komisi', 'rapat kelengkapan dewan', 'rapat lainnya'
                                ] as $option)
                                    <option value="{{ $option }}" {{ old('jenis_rapat', $rapat->jenis_rapat) === $option ? 'selected' : '' }}>
                                        {{ ucfirst($option) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Judul --}}
                        <div class="mb-3">
                            <label for="judul" class="form-label">Judul</label>
                            <input type="text" name="judul" id="judul" class="form-control" value="{{ old('judul', $rapat->judul) }}" required>
                        </div>

                        {{-- Tanggal --}}
                        <div class="mb-3">
                            <label for="tanggal" class="form-label">Tanggal</label>
                            <input type="date" name="tanggal" id="tanggal" class="form-control" value="{{ old('tanggal', \Carbon\Carbon::parse($rapat->tanggal)->format('Y-m-d')) }}" required>
                        </div>

                        {{-- Peserta --}}
                        <div class="mb-3">
                            <label for="peserta" class="form-label">Peserta</label>
                            <input type="text" name="peserta" id="peserta" class="form-control" value="{{ old('peserta', $rapat->peserta) }}">
                        </div>

                        {{-- Notulen --}}
                        <div class="mb-3">
                            <label for="notulen" class="form-label">Notulen / Catatan</label>
                            <textarea name="notulen" id="notulen" class="form-control" rows="3">{{ old('notulen', $rapat->notulen) }}</textarea>
                        </div>

                        {{-- Upload dokumentasi baru --}}
                        <div class="mb-3">
                            <label for="dokumentasi" class="form-label">Upload Dokumentasi Baru (boleh lebih dari satu)</label>
                            <input type="file" name="dokumentasi[]" id="dokumentasi" class="form-control" multiple accept=".jpg,.jpeg,.png,.pdf">
                        </div>

                        {{-- Tombol --}}
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-success">Simpan Perubahan</button>
                            <a href="{{ route('rapat.user.index') }}" class="btn btn-secondary">Batal</a>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
