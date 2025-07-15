@extends('layouts.app')

@section('content')

<style>
    body {
        background: rgb(239, 248, 243) !important; /* Putih kehijauan */
    }
</style>
<div class="mt-2">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <!-- Card -->
            <div class="card shadow-sm rounded-4">
                <div class="card-body">
                    <h2 class="card-title mb-4">Tambah Iuran</h2>
            <p class="text-subtitle text-muted">
                Berikut merupakan bagian yang harus diisi untuk melampirkan iuran yang telah bapak/ibu lakukan
            </p>

                    <form action="{{ route('iuran.user.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <!-- NIK Otomatis dari user login -->
                        <input type="hidden" name="nik" value="{{ auth()->user()->nik }}">

                        <!-- Status default 'terkirim' -->
                        <input type="hidden" name="status" value="terkirim">

                        <div class="mb-3">
                            <label for="jenis_iuran" class="form-label">Jenis Iuran</label>
                            <select name="jenis_iuran" class="form-select" required>
                                <option value="" disabled selected>Pilih Jenis Iuran</option>
                                @foreach([
                                    'Iuran Bulanan',
                                    'Sumbangan Fraksi',
                                    'Dana Infaq Shadaqoh dan Zakat (ZIS)',
                                    'Dana Khitmat',
                                    'Dana Kompensasi Kepada Caleg',
                                    'Dana Insidensial',
                                    'Dana Lainnya'
                                ] as $jenis)
                                    <option value="{{ $jenis }}">{{ $jenis }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="nominal" class="form-label">Nominal</label>
                            <input type="number" name="nominal" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="tanggal" class="form-label">Tanggal</label>
                            <input type="date" name="tanggal" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="catatan" class="form-label">Catatan (Opsional)</label>
                            <textarea name="catatan" class="form-control" rows="2"></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="dokumentasi" class="form-label">Upload Dokumentasi (Opsional)</label>
                            <input type="file" name="dokumentasi[]" class="form-control" accept=".jpg,.jpeg,.png,.pdf" multiple>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-success"> Kirim Iuran
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            <!-- End Card -->
        </div>
    </div>
</div>
@endsection
