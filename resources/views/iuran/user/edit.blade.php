@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Edit Iuran</h2>

    <form action="{{ route('iuran.user.update', $iuran->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="jenis_iuran" class="form-label">Jenis Iuran</label>
            <input type="text" name="jenis_iuran" id="jenis_iuran" class="form-control" value="{{ $iuran->jenis_iuran }}" required>
        </div>

        <div class="mb-3">
            <label for="nominal" class="form-label">Nominal</label>
            <input type="number" name="nominal" id="nominal" class="form-control" value="{{ $iuran->nominal }}" required>
        </div>

        <div class="mb-3">
            <label for="tanggal" class="form-label">Tanggal</label>
            <input type="date" name="tanggal" id="tanggal" class="form-control" value="{{ $iuran->tanggal }}" required>
        </div>

        <div class="mb-3">
            <label for="catatan" class="form-label">Catatan</label>
            <textarea name="catatan" id="catatan" class="form-control">{{ $iuran->catatan }}</textarea>
        </div>

        <div class="mb-3">
            <label for="dokumentasi" class="form-label">Dokumentasi (Opsional)</label>
            <input type="file" name="dokumentasi" class="form-control">
            @if($iuran->dokumentasi)
                <p class="mt-2">File Saat Ini: <a href="{{ asset('storage/' . $iuran->dokumentasi) }}" target="_blank">Lihat</a></p>
            @endif
        </div>

        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
    </form>
</div>
@endsection
