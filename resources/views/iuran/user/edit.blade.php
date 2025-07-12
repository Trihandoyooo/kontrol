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
                    {{-- Judul --}}
                    <h2 class="card-title mb-0">Edit Iuran</h2>
                    <p class="text-muted mb-4">Silakan ubah data iuran sesuai kebutuhan Anda.</p>

                    {{-- Dokumentasi Sebelumnya --}}
                    @if($iuran->dokumentasi)
                        @php $files = json_decode($iuran->dokumentasi, true); @endphp
                        <div class="mb-4 border rounded-3 p-3 bg-light shadow-sm">
                            <h5 class="mb-3 border-bottom pb-2">📂 Dokumentasi Sebelumnya</h5>
                            <div class="row g-3">
                                @foreach($files as $index => $file)
                                    <div class="col-md-4 text-center">
                                        @if(Str::endsWith($file, ['jpg', 'jpeg', 'png']))
                                            <img src="{{ asset('storage/' . $file) }}" class="img-fluid rounded shadow-sm mb-2" style="max-height: 150px;">
                                        @else
                                            <a href="{{ asset('storage/' . $file) }}" target="_blank">{{ basename($file) }}</a>
                                        @endif

                                        {{-- Tombol hapus file --}}
                                        <form action="{{ route('iuran.user.deleteFile', ['id' => $iuran->id, 'index' => $index]) }}" method="POST" onsubmit="return confirm('Hapus file ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger px-2 py-1 mt-1">Hapus</button>
                                        </form>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Form Edit Iuran --}}
                    <form action="{{ route('iuran.user.update', $iuran->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        {{-- Jenis Iuran --}}
                        <div class="mb-3">
                            <label for="jenis_iuran" class="form-label">Jenis Iuran</label>
                            <input type="text" name="jenis_iuran" id="jenis_iuran" class="form-control" value="{{ old('jenis_iuran', $iuran->jenis_iuran) }}" required>
                        </div>

                        {{-- Nominal --}}
                        <div class="mb-3">
                            <label for="nominal" class="form-label">Nominal</label>
                            <input type="number" name="nominal" id="nominal" class="form-control" value="{{ old('nominal', $iuran->nominal) }}" required>
                        </div>

                        {{-- Tanggal --}}
                        <div class="mb-3">
                            <label for="tanggal" class="form-label">Tanggal</label>
                            <input type="date" name="tanggal" id="tanggal" class="form-control" value="{{ old('tanggal', $iuran->tanggal) }}" required>
                        </div>

                        {{-- Catatan --}}
                        <div class="mb-3">
                            <label for="catatan" class="form-label">Catatan</label>
                            <textarea name="catatan" id="catatan" class="form-control" rows="3">{{ old('catatan', $iuran->catatan) }}</textarea>
                        </div>

                        {{-- Upload Dokumentasi Baru --}}
                        <div class="mb-4">
                            <label for="dokumentasi" class="form-label">Upload Dokumentasi Baru (boleh lebih dari satu)</label>
                            <input type="file" name="dokumentasi[]" id="dokumentasi" class="form-control" multiple accept=".jpg,.jpeg,.png,.pdf">
                        </div>

                        {{-- Tombol Aksi --}}
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-success"><i class="bi bi-save me-1"></i> Simpan Perubahan</button>
                            <a href="{{ route('iuran.user.index') }}" class="btn btn-secondary"><i class="bi bi-arrow-left me-1"></i> Batal</a>
                        </div>
                    </form>
                    {{-- End Form Edit --}}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
