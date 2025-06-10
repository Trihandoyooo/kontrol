@extends('layouts.app')

@section('content')
<div class="page-heading">
    <h3>Edit Status Iuran</h3>
</div>

<div class="page-content">
    <form action="{{ route('admin.iuran.update', $iuran->id) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="mb-3">
            <label for="status" class="form-label">Status</label>
            <select name="status" id="status" class="form-select" required>
                @foreach($statuses as $status)
                    <option value="{{ $status }}" {{ $iuran->status == $status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>
                @endforeach
            </select>
            @error('status')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3" id="alasan-tolak-group" style="display: {{ $iuran->status == 'ditolak' ? 'block' : 'none' }}">
            <label for="alasan_tolak" class="form-label">Alasan Penolakan</label>
            <textarea name="alasan_tolak" id="alasan_tolak" class="form-control">{{ old('alasan_tolak', $iuran->alasan_tolak) }}</textarea>
            @error('alasan_tolak')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary">Simpan</button>
        <a href="{{ route('admin.iuran.index') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>

<script>
    document.getElementById('status').addEventListener('change', function() {
        if (this.value === 'ditolak') {
            document.getElementById('alasan-tolak-group').style.display = 'block';
        } else {
            document.getElementById('alasan-tolak-group').style.display = 'none';
        }
    });
</script>
@endsection
