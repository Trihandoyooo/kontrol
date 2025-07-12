@extends('layouts.app')

@section('content')
<style>
    body {
        background: rgb(239, 248, 243) !important;
    }
</style>

<div class="page-content">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow-sm rounded-10">
                <div class="card-body">
                    <h2 class="card-title mb-4">Tambah Kaderisasi</h2>
                    <p class="text-subtitle text-muted">
                        Silakan lengkapi informasi berikut untuk menambahkan data kaderisasi.
                    </p> 

                    @if ($errors->any())
                        <div class="alert alert-danger rounded-3">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('kaderisasi.user.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <input type="hidden" name="nik" value="{{ auth()->user()->nik }}">
                        <input type="hidden" name="status" value="terkirim">

                        <div class="mb-3">
                            <label for="judul" class="form-label">Judul Kaderisasi <span class="text-danger">*</span></label>
                            <input type="text" name="judul" class="form-control" required value="{{ old('judul') }}">
                        </div>

                        <div class="mb-3">
                            <label for="tanggal" class="form-label">Tanggal <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal" class="form-control" required value="{{ old('tanggal') }}">
                        </div>

                        <div class="mb-3">
                            <label for="peserta" class="form-label">Peserta Kaderisasi</label>
                            <textarea name="peserta" class="form-control" rows="3">{{ old('peserta') }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label for="catatan" class="form-label">Catatan</label>
                            <textarea name="catatan" class="form-control" rows="3">{{ old('catatan') }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label for="foto" class="form-label">Dokumentasi (Foto) <span class="text-danger">*</span></label>
                            <input type="file" name="foto[]" class="form-control" multiple id="foto-input" required>
                        </div>

                        <div id="foto-preview" class="mb-3"></div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-success btn-lg">Simpan Kaderisasi</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('foto-input').addEventListener('change', function(){
        const preview = document.getElementById('foto-preview');
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
