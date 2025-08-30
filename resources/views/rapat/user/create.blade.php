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
                    <h3>Input Rapat Baru</h3>
                    <p class="text-subtitle text-muted">Silakan isi form berikut untuk menambahkan rapat baru.</p>
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
                            <label for="jenis_rapat" class="form-label">Jenis Rapat <span class="text-danger">*</span></label>
                            <select name="jenis_rapat" class="form-select" required>
                                <option value="">-- Pilih Jenis Rapat --</option>
                                @foreach([
                                    'rapat komisi' => 'Rapat Komisi',
                                    'rapat paripurna' => 'Rapat Paripurna',
                                    'rapat fraksi' => 'Rapat Fraksi',
                                    'rapat lintas komisi' => 'Rapat Lintas Komisi',
                                    'rapat kelengkapan dewan' => 'Rapat Kelengkapan Dewan',
                                    'rapat acara dengan DPC PKB Bengkalis' => 'Rapat/Acara dengan DPC PKB Bengkalis',
                                    'rapat acara dengan DPW PKB' => 'Rapat/Acara dengan DPW PKB',
                                    'rapat acara dengan DPP PKB' => 'Rapat/Acara dengan DPP PKB',
                                    'rapat lainnya' => 'Rapat Lainnya',
                                ] as $value => $label)
                                    <option value="{{ $value }}" {{ old('jenis_rapat') == $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="judul" class="form-label">Judul Rapat <span class="text-danger">*</span></label>
                            <input type="text" name="judul" class="form-control" required value="{{ old('judul') }}">
                        </div>

                        <div class="mb-3">
                            <label for="lokasi" class="form-label">Lokasi Rapat <span class="text-danger">*</span></label>
                            <input type="text" name="lokasi" class="form-control" required value="{{ old('lokasi') }}">
                        </div>

                        <div class="mb-3">
                            <label for="tanggal" class="form-label">Tanggal <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal" class="form-control" required value="{{ old('tanggal') }}">
                        </div>

                        <div class="mb-3">
                            <label for="peserta" class="form-label">Peserta <span class="text-danger">*</span></label>
                            <input type="text" name="peserta" class="form-control" required value="{{ old('peserta') }}">
                        </div>

                        <div class="mb-3 position-relative">
                            <label for="dokumentasi" class="form-label">Dokumentasi (WAJIB) <span class="text-danger">*</span></label>
                            <input type="file" name="dokumentasi[]" id="dokumentasi-input" class="form-control" multiple required>
                            <div id="overlayDokumentasi" style="position:absolute;top:0;left:0;width:100%;height:100%;cursor:pointer;"></div>
                        </div>

                        <div id="dokumentasi-preview" class="mb-3"></div>

                        <div class="mb-3">
                            <label for="catatan" class="form-label">Catatan (Opsional)</label>
                            <textarea name="catatan" class="form-control" rows="3">{{ old('catatan') }}</textarea>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-success">Simpan Rapat</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Peringatan Dokumentasi -->
<div class="modal fade" id="modalPeringatanDokumentasi" tabindex="-1" aria-labelledby="peringatanLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-3">
            <div class="modal-header bg-danger text-dark">
                <h5 class="modal-title" id="peringatanLabel">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i> Peringatan
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <p>Foto dokumentasi <strong>harus menampakkan sisi depan dan belakang.</strong> Pastikan Anda sudah mempersiapkan file sebelum memilih.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="batalDokumentasi">Batal</button>
                <button type="button" class="btn btn-success" id="btnKonfirmasiDokumentasi">Saya Mengerti</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const overlay = document.getElementById('overlayDokumentasi');
    const modal = new bootstrap.Modal(document.getElementById('modalPeringatanDokumentasi'));
    const dokumentasiInput = document.getElementById('dokumentasi-input');

    overlay.addEventListener('click', function () {
        modal.show();
    });

    document.getElementById('btnKonfirmasiDokumentasi').addEventListener('click', function () {
        modal.hide();
        overlay.style.display = 'none';
        dokumentasiInput.click();
    });

    document.getElementById('batalDokumentasi').addEventListener('click', function () {
        modal.hide();
    });

    dokumentasiInput.addEventListener('change', function () {
        const preview = document.getElementById('dokumentasi-preview');
        preview.innerHTML = '';
        const files = this.files;
        if (files.length === 0) return;

        const row = document.createElement('div');
        row.classList.add('row', 'g-2');

        Array.from(files).forEach(file => {
            const col = document.createElement('div');
            col.classList.add('col-4', 'col-md-2');

            if (file.type.startsWith('image/')) {
                const img = document.createElement('img');
                img.src = URL.createObjectURL(file);
                img.classList.add('img-fluid', 'rounded', 'border');
                img.alt = file.name;
                col.appendChild(img);
            } else {
                col.textContent = file.name + ' (bukan gambar)';
            }
            row.appendChild(col);
        });
        preview.appendChild(row);
    });
});
</script>
@endsection
