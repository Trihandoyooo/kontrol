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

<div class="mt-3">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow-sm rounded-4">
                <div class="card-body">
                    <h2 class="card-title">Edit Alokasi Dana</h2>
                    <p class="text-muted">Silakan ubah informasi alokasi dana sesuai kebutuhan.</p>

                    {{-- Dokumentasi Lama --}}
                    @if($alokasi->dokumentasi)
                        @php $files = json_decode($alokasi->dokumentasi, true); @endphp
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
                                        <form action="{{ route('admin.alokasi.deleteFile', ['id' => $alokasi->id, 'index' => $index]) }}" method="POST" onsubmit="return confirm('Hapus file ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger mt-1">Hapus</button>
                                        </form>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Form Edit --}}
                    <form action="{{ route('admin.alokasi.update', $alokasi->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="nama_kegiatan" class="form-label">Nama Kegiatan</label>
                            <input type="text" name="nama_kegiatan" id="nama_kegiatan" class="form-control"
                                   value="{{ old('nama_kegiatan', $alokasi->nama_kegiatan) }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="jumlah" class="form-label">Jumlah (Rp)</label>
                            <input type="number" name="jumlah" id="jumlah" class="form-control"
                                   value="{{ old('jumlah', $alokasi->jumlah) }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="tanggal" class="form-label">Tanggal</label>
                            <input type="date" name="tanggal" id="tanggal" class="form-control"
                                   value="{{ old('tanggal', \Carbon\Carbon::parse($alokasi->tanggal)->format('Y-m-d')) }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="deskripsi" class="form-label">Deskripsi</label>
                            <textarea name="deskripsi" id="deskripsi" rows="3" class="form-control">{{ old('deskripsi', $alokasi->deskripsi) }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label for="dokumentasi" class="form-label">Upload Dokumentasi Baru (boleh lebih dari satu)</label>
                            <input type="file" name="dokumentasi[]" id="dokumentasi" class="form-control" multiple accept=".jpg,.jpeg,.png,.pdf">
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-success">Simpan Perubahan</button>
                            <a href="{{ route('admin.iuran.index') }}" class="btn btn-secondary">Batal</a>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
