@extends('layouts.app')

@section('content')
    <style>
        body {
            background: rgb(239, 248, 243) !important;
            /* Putih kehijauan */
        }
    </style>

    <div class="page-content mt-4">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card-container shadow-sm rounded-10">
                    <div class="page-heading mb-3">
                        <h3>Input Rapat Baru</h3>
                        <p class="text-subtitle text-muted">Berikut merupakan form yang dapat bapak/ibu isi
                            untuk
                            dapat menambahkan rapat.</p>
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

                        <form action="{{ route('rapat.user.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <div class="mb-3">

                                <label for="jenis_rapat" class="form-label">Jenis Rapat</label>
                                <select name="jenis_rapat" class="form-select" required>
                                    <option value="">-- Pilih Jenis Rapat --</option>
                                    <option value="rapat komisi"
                                        {{ old('jenis_rapat') == 'rapat komisi' ? 'selected' : '' }}>Rapat Komisi</option>
                                    <option value="rapat paripurna"
                                        {{ old('jenis_rapat') == 'rapat paripurna' ? 'selected' : '' }}>Rapat Paripurna
                                    </option>
                                    <option value="rapat fraksi"
                                        {{ old('jenis_rapat') == 'rapat fraksi' ? 'selected' : '' }}>Rapat Fraksi</option>
                                    <option value="rapat lintas komisi"
                                        {{ old('jenis_rapat') == 'rapat lintas komisi' ? 'selected' : '' }}>Rapat Lintas
                                        Komisi</option>
                                    <option value="rapat kelengkapan dewan"
                                        {{ old('jenis_rapat') == 'rapat kelengkapan dewan' ? 'selected' : '' }}>Rapat
                                        Kelengkapan Dewan</option>
                                    <option value="rapat fraksi"
                                        {{ old('jenis_rapat') == 'rapat fraksi' ? 'selected' : '' }}>Rapat Fraksi</option>
                                    <option value="rapat acara dengan DPC PKB Bengkalis"
                                        {{ old('jenis_rapat') == 'rapat acara dengan DPC PKB Bengkalis' ? 'selected' : '' }}>
                                        Rapat/Acara dengan DPC PKB Bengkalis</option>
                                    <option value="rapat acara dengan DPW PKB"
                                        {{ old('jenis_rapat') == 'rapat acara dengan DPW PKB' ? 'selected' : '' }}>
                                        Rapat/Acara dengan DPW PKB</option>
                                    <option value="rapat acara dengan DPP PKB"
                                        {{ old('jenis_rapat') == 'rapat acara dengan DPP PKB' ? 'selected' : '' }}>
                                        Rapat/Acara dengan DPP PKB</option>
                                    <option value="rapat lainnya"
                                        {{ old('jenis_rapat') == 'rapat lainnya' ? 'selected' : '' }}>Rapat Lainnya
                                    </option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="judul" class="form-label">Judul Rapat</label>
                                <input type="text" name="judul" class="form-control" required
                                    value="{{ old('judul') }}">
                            </div>

                            <div class="mb-3">
                                <label for="judul" class="form-label">Lokasi Rapat</label>
                                <input type="text" name="lokasi" class="form-control" required
                                    value="{{ old('lokasi') }}">
                            </div>

                            <div class="mb-3">
                                <label for="tanggal" class="form-label">Tanggal</label>
                                <input type="date" name="tanggal" class="form-control" required
                                    value="{{ old('tanggal') }}">
                            </div>

                            <div class="mb-3">
                                <label for="peserta" class="form-label">Peserta</label>
                                <input type="text" name="peserta" class="form-control" required
                                    value="{{ old('peserta') }}">
                            </div>

                            <div class="mb-3">
                                <label for="dokumentasi" class="form-label">Dokumentasi (Opsional)</label>
                                <input type="file" name="dokumentasi[]" class="form-control" multiple
                                    id="dokumentasi-input">
                            </div>

                            <!-- Container preview file terpilih -->
                            <div id="dokumentasi-preview" class="mb-3"></div>

                            <div class="mb-3">
                                <label for="catatan" class="form-label">Catatan (Opsional)</label>
                                <textarea name="catatan" class="form-control" rows="3">{{ old('notulen') }}</textarea>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-success ">Simpan Rapat</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('dokumentasi-input').addEventListener('change', function() {
            const preview = document.getElementById('dokumentasi-preview');
            preview.innerHTML = ''; // Clear previous preview
            const files = this.files;
            if (files.length === 0) return;

            let list = document.createElement('ul');
            list.classList.add('list-group');

            for (let i = 0; i < files.length; i++) {
                let item = document.createElement('li');
                item.classList.add('list-group-item');
                item.textContent = files[i].name;
                list.appendChild(item);
            }

            preview.appendChild(list);
        });
    </script>
@endsection
