@extends('layouts.app')

@section('content')
<div class="page-heading mb-4">
    <h3>Detail Rapat</h3>
</div>

<div class="page-content">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm rounded-4">
                <div class="card-body">
                    <p><strong>Jenis Rapat:</strong> {{ $rapat->jenis_rapat }}</p>
                    <p><strong>Judul:</strong> {{ $rapat->judul }}</p>
                    <p><strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($rapat->tanggal)->format('d M Y') }}</p>
                    <p><strong>Peserta:</strong> {{ $rapat->peserta }}</p>
                    <p><strong>Catatan:</strong> {{ $rapat->catatan ?? '-' }}</p>

                    <p><strong>Dokumentasi:</strong><br>
                        @if ($rapat->dokumentasi)
    @php $files = json_decode($rapat->dokumentasi, true); @endphp
    <ul>
        @foreach ($files as $file)
            @php $ext = pathinfo($file, PATHINFO_EXTENSION); @endphp
            <li class="mb-2">
                @if (in_array($ext, ['jpg','jpeg','png']))
                    <img src="{{ asset('storage/' . $file) }}" alt="Dokumentasi" style="max-width: 200px;">
                @elseif ($ext === 'pdf')
                    <a href="{{ asset('storage/' . $file) }}" target="_blank" class="btn btn-outline-primary btn-sm">Lihat PDF</a>
                @else
                    <a href="{{ asset('storage/' . $file) }}" target="_blank" class="btn btn-outline-secondary btn-sm">Download File</a>
                @endif
            </li>
        @endforeach
    </ul>
@else
    <p>Tidak ada dokumentasi.</p>
@endif


                    <div class="d-flex justify-content-between">
                        <a href="{{ route('rapat.user.index') }}" class="btn btn-secondary">← Kembali</a>
                        <div>
                            <a href="{{ route('rapat.user.edit', $rapat->id) }}" class="btn btn-warning">Edit</a>
                            <form action="{{ route('rapat.user.destroy', $rapat->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger" onclick="return confirm('Yakin ingin menghapus rapat ini?')">Hapus</button>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
