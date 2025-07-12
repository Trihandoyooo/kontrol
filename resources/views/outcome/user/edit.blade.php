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
                    <h2 class="card-title">Edit Outcome</h2>
                    <p class="text-muted">Silakan ubah informasi outcome sesuai kebutuhan.</p>

                    {{-- Dokumentasi lama --}}
                    @if($outcome->dokumentasi)
                        @php $files = json_decode($outcome->dokumentasi, true); @endphp
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
                                        {{-- Form hapus file --}}
                                        <form action="{{ route('outcome.user.deleteFile', ['id' => $outcome->id, 'index' => $index]) }}" method="POST" onsubmit="return confirm('Hapus file ini?')">
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
                    <form action="{{ route('outcome.user.update', $outcome->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        {{-- Judul --}}
                        <div class="mb-3">
                            <label for="judul" class="form-label">Judul Output</label>
                            <input type="text" name="judul" id="judul" class="form-control" value="{{ old('judul', $outcome->judul) }}" required>
                        </div>

                        {{-- Nama Kegiatan --}}
                        <div class="mb-3">
                            <label for="nama_kegiatan" class="form-label">Nama Kegiatan</label>
                            <input type="text" name="nama_kegiatan" id="nama_kegiatan" class="form-control" value="{{ old('nama_kegiatan', $outcome->nama_kegiatan) }}" required>
                        </div>

                        {{-- Tanggal --}}
                        <div class="mb-3">
                            <label for="tanggal" class="form-label">Tanggal</label>
                            <input type="date" name="tanggal" id="tanggal" class="form-control" value="{{ old('tanggal', \Carbon\Carbon::parse($outcome->tanggal)->format('Y-m-d')) }}" required>
                        </div>

                        {{-- Dapil --}}
                        <div class="mb-3">
                            <label for="dapil" class="form-label">Dapil</label>
                            <input type="text" name="dapil" id="dapil" class="form-control" value="{{ old('dapil', $outcome->dapil) }}" required>
                        </div>

                        {{-- Keterangan --}}
                        <div class="mb-3">
                            <label for="keterangan" class="form-label">Keterangan</label>
                            <textarea name="keterangan" id="keterangan" class="form-control" rows="3">{{ old('keterangan', $outcome->keterangan) }}</textarea>
                        </div>

                        {{-- Manfaat --}}
                        <div class="mb-3">
                            <label for="manfaat" class="form-label">Manfaat</label>
                            <textarea name="manfaat" id="manfaat" class="form-control" rows="3">{{ old('manfaat', $outcome->manfaat) }}</textarea>
                        </div>

                        {{-- Upload dokumentasi baru --}}
                        <div class="mb-3">
                            <label for="dokumentasi" class="form-label">Upload Dokumentasi Baru (boleh lebih dari satu)</label>
                            <input type="file" name="dokumentasi[]" id="dokumentasi" class="form-control" multiple accept=".jpg,.jpeg,.png,.pdf">
                        </div>

                        {{-- Tombol --}}
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-success">Simpan Perubahan</button>
                            <a href="{{ route('outcome.user.index') }}" class="btn btn-secondary">Batal</a>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
