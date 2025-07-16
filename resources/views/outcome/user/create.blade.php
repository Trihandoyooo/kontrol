@extends('layouts.app')

@section('content')
<style>
    body {
        background: rgb(239, 248, 243) !important;
    }
</style>

<div class="page-content mt-4">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card-container shadow-sm rounded-10">
                <div class="page-heading mb-3">
                    <h3 class="card-title">Tambah Outcome</h3>
                    <p class="text-muted my-1">
                        Silakan lengkapi informasi berikut untuk menambahkan outcome/output kegiatan.
                    </p>
                </div>
                <div class="card-body">

                    @if ($errors->any())
                        <div class="alert alert-danger rounded-3">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('outcome.user.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <input type="hidden" name="nik" value="{{ auth()->user()->nik }}">
                        <input type="hidden" name="status" value="terkirim">

                        <div class="mb-3">
                            <label class="form-label">Judul Output<span class="text-danger">*</span></label>
                            <input type="text" name="judul" class="form-control" required value="{{ old('judul') }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tanggal<span class="text-danger">*</span></label>
                            <input type="date" name="tanggal" class="form-control" required value="{{ old('tanggal') }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Nama Kegiatan<span class="text-danger">*</span></label>
                            <input type="text" name="nama_kegiatan" class="form-control" required value="{{ old('nama_kegiatan') }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Keterangan Kegiatan<span class="text-danger">*</span></label>
                            <textarea name="keterangan" class="form-control" rows="3" required>{{ old('keterangan') }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Manfaat Output<span class="text-danger">*</span></label>
                            <textarea name="manfaat" class="form-control" rows="3" required>{{ old('manfaat') }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Dapil<span class="text-danger">*</span></label>
                            <select name="dapil" class="form-select" required>
                                <option value="">-- Pilih Dapil --</option>
                                <option value="Dapil 1" {{ old('dapil') == 'Dapil 1' ? 'selected' : '' }}>Dapil 1</option>
                                <option value="Dapil 2" {{ old('dapil') == 'Dapil 2' ? 'selected' : '' }}>Dapil 2</option>
                                <option value="Dapil 3" {{ old('dapil') == 'Dapil 3' ? 'selected' : '' }}>Dapil 3</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Foto Dokumentasi <span class="text-danger">*</span></label>
                            <input type="file" name="dokumentasi[]" class="form-control" multiple id="dokumentasi-input" required>
                        </div>

                        <div id="dokumentasi-preview" class="mb-3"></div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-success">Simpan Outcome</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('dokumentasi-input').addEventListener('change', function(){
        const preview = document.getElementById('dokumentasi-preview');
        preview.innerHTML = '';
        const files = this.files;
        if(files.length === 0) return;

        let list = document.createElement('ul');
        list.classList.add('list-group');

        for(let i=0; i < files.length; i++){
            let item = document.createElement('li');
            item.classList.add('list-group-item');
            item.textContent = files[i].name;
            list.appendChild(item);
        }

        preview.appendChild(list);
    });
</script>
@endsection
