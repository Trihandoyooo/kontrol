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

<div class="page-heading mb-4">
    <h3>Edit Rapat</h3>
</div>

<div class="page-content">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm rounded-4">
                <div class="card-body">
                    <form action="{{ route('rapat.user.update', $rapat->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="jenis_rapat" class="form-label">Jenis Rapat</label>
                            <select name="jenis_rapat" id="jenis_rapat" class="form-select" required>
                                @php
                                    $options = [
                                        'rapat komisi', 'rapat paripurna', 'rapat fraksi',
                                        'rapat lintas komisi', 'rapat kelengkapan dewan', 'rapat lainnya'
                                    ];
                                @endphp
                                @foreach ($options as $option)
                                    <option value="{{ $option }}" {{ old('jenis_rapat', $rapat->jenis_rapat) === $option ? 'selected' : '' }}>
                                        {{ ucfirst($option) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('jenis_rapat')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="judul" class="form-label">Judul</label>
                            <input type="text" name="judul" id="judul" class="form-control" value="{{ old('judul', $rapat->judul) }}" required>
                            @error('judul')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="tanggal" class="form-label">Tanggal</label>
                            <input type="date" name="tanggal" id="tanggal" class="form-control" 
                                value="{{ old('tanggal', \Carbon\Carbon::parse($rapat->tanggal)->format('Y-m-d')) }}" required>
                            @error('tanggal')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="peserta" class="form-label">Peserta</label>
                            <input type="text" name="peserta" id="peserta" class="form-control" value="{{ old('peserta', $rapat->peserta) }}" required>
                            @error('peserta')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="catatan" class="form-label">Catatan</label>
                            <textarea name="catatan" id="catatan" rows="4" class="form-control">{{ old('catatan', $rapat->catatan) }}</textarea>
                            @error('catatan')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        @if ($rapat->dokumentasi)
                            @php $files = json_decode($rapat->dokumentasi, true); @endphp
                            <div class="mb-4">
                                <label class="form-label">Dokumentasi Saat Ini:</label>
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
                                            <form action="{{ route('rapat.user.destroy', $rapat->id) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Hapus file ini?')">
                                                    Hapus
                                                </button>
                                            </form>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('rapat.user.index') }}" class="btn btn-secondary">Batal</a>
                            <button type="submit" class="btn btn-success">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
