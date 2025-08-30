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
                            <label class="form-label">Judul Output <span class="text-danger">*</span></label>
                            <input type="text" name="judul" class="form-control" required value="{{ old('judul') }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tanggal <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal" class="form-control" required value="{{ old('tanggal') }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Nama Kegiatan <span class="text-danger">*</span></label>
                            <input type="text" name="nama_kegiatan" class="form-control" required value="{{ old('nama_kegiatan') }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Keterangan Kegiatan <span class="text-danger">*</span></label>
                            <textarea name="keterangan" class="form-control" rows="3" required>{{ old('keterangan') }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Manfaat Output <span class="text-danger">*</span></label>
                            <textarea name="manfaat" class="form-control" rows="3" required>{{ old('manfaat') }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Dapil <span class="text-danger">*</span></label>
                            <select name="dapil" class="form-select" required>
                                <option value="">-- Pilih Dapil --</option>
                                <option value="Dapil 1" {{ old('dapil') == 'Dapil 1' ? 'selected' : '' }}>Dapil 1</option>
                                <option value="Dapil 2" {{ old('dapil') == 'Dapil 2' ? 'selected' : '' }}>Dapil 2</option>
                                <option value="Dapil 3" {{ old('dapil') == 'Dapil 3' ? 'selected' : '' }}>Dapil 3</option>
                            </select>
                        </div>

                        <div class="mb-3 position-relative">
                            <label class="form-label">Foto Dokumentasi <span class="text-danger">*</span></label>
                            <input type="file" name="dokumentasi[]" class="form-control" id="dokumentasi-input" multiple required accept="image/*,application/pdf">
                            <div id="overlayDokumentasi" style="position:absolute;top:0;left:0;width:100%;height:100%;cursor:pointer;"></div>
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

<!-- Modal Peringatan Dokumentasi -->
<div class="modal fade" id="modalPeringatanFoto" tabindex="-1" aria-labelledby="peringatanFotoLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-3">
            <div class="modal-header bg-danger text-dark">
                <h5 class="modal-title" id="peringatanFotoLabel">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i> Peringatan
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <p>Foto dokumentasi <strong>harus mencakup semua sudut penting kegiatan</strong>. Pastikan file sudah siap sebelum memilih.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="batalFoto">Batal</button>
                <button type="button" class="btn btn-success" id="btnKonfirmasiFoto">Saya Mengerti</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const overlay = document.getElementById('overlayDokumentasi');
    const modal = new bootstrap.Modal(document.getElementById('modalPeringatanFoto'));
    const dokumentasiInput = document.getElementById('dokumentasi-input');

    overlay.addEventListener('click', function () {
        modal.show();
    });

    document.getElementById('btnKonfirmasiFoto').addEventListener('click', function () {
        modal.hide();
        overlay.style.display = 'none';
        dokumentasiInput.click();
    });

    document.getElementById('batalFoto').addEventListener('click', function () {
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
            } else if (file.type === 'application/pdf') {
                const icon = document.createElement('div');
                icon.classList.add('border', 'rounded', 'p-2', 'text-center');
                icon.innerHTML = '<i class="bi bi-file-earmark-pdf" style="font-size:2rem;"></i><br>' + file.name;
                col.appendChild(icon);
            } else {
                col.textContent = file.name + ' (tidak dikenali)';
            }

            row.appendChild(col);
        });

        preview.appendChild(row);
    });
});
</script>
@endsection
