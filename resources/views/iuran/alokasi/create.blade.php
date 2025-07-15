@extends('layouts.app')

@section('content')
<div class="mt-2">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-sm rounded-4">
                <div class="card-body">
                    <h2 class="card-title mb-4">Tambah Alokasi Dana</h2>
                    <p class="text-subtitle text-muted">Lengkapi formulir berikut untuk mencatat alokasi dana kegiatan.</p>

                    <form action="{{ route('admin.alokasi.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <!-- Nama Kegiatan -->
                        <div class="mb-3">
                            <label for="nama_kegiatan" class="form-label">
                                Nama Kegiatan <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="nama_kegiatan" class="form-control" required value="{{ old('nama_kegiatan') }}">
                            @error('nama_kegiatan')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Jumlah Dana -->
                        <div class="mb-3">
                            <label for="jumlah" class="form-label">
                                Jumlah Dana <span class="text-danger">*</span>
                            </label>
                            <input type="number" name="jumlah" class="form-control" required value="{{ old('jumlah') }}">
                            @error('jumlah')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Tanggal -->
                        <div class="mb-3">
                            <label for="tanggal" class="form-label">
                                Tanggal Alokasi <span class="text-danger">*</span>
                            </label>
                            <input type="date" name="tanggal" class="form-control" required value="{{ old('tanggal') }}">
                            @error('tanggal')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Deskripsi -->
                        <div class="mb-3">
                            <label for="deskripsi" class="form-label">Deskripsi (Opsional)</label>
                            <textarea name="deskripsi" class="form-control" rows="3">{{ old('deskripsi') }}</textarea>
                            @error('deskripsi')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Dokumentasi -->
                        <div class="mb-3">
                            <label for="dokumentasi" class="form-label">
                                Upload Dokumentasi <span class="text-danger">*</span>
                            </label>
                            <input type="file" name="dokumentasi[]" class="form-control" accept=".jpg,.jpeg,.png,.pdf" multiple required>
                            @error('dokumentasi')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                            @error('dokumentasi.*')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-save me-1"></i> Simpan Alokasi
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
