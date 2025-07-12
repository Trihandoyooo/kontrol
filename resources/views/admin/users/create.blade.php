@extends('layouts.app')

@section('content')

<style>
    body {
        background: rgb(240, 247, 243) !important;
    }

    .container {
        padding: 2rem 0;
    }

    .card {
        border-radius: 12px;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.05);
    }

    .card-header h1 {
        font-size: 1.5rem;
        font-weight: 600;
        margin-bottom: 0.25rem;
    }

    .card-header p {
        font-size: 0.9rem;
        color: #6c757d;
        margin-bottom: 0;
    }
</style>

<div class="container">
    <div class="card">
        <div class="card-header p-4 border-bottom">
            <h1>Tambah User</h1>
            <p>Lengkapi formulir berikut untuk menambahkan data user baru ke dalam sistem. Pastikan semua informasi diisi dengan benar.</p>
        </div>

        <div class="card-body p-4">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                           <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.users.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="mb-3">
                    <label for="nik" class="form-label">NIK</label>
                    <input type="text" name="nik" class="form-control" required value="{{ old('nik') }}">
                </div>

                <div class="mb-3">
                    <label for="name" class="form-label">Nama Lengkap</label>
                    <input type="text" name="name" class="form-control" required value="{{ old('name') }}">
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="gelar_depan" class="form-label">Gelar Depan</label>
                        <input type="text" name="gelar_depan" class="form-control" value="{{ old('gelar_depan') }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="gelar_belakang" class="form-label">Gelar Belakang</label>
                        <input type="text" name="gelar_belakang" class="form-control" value="{{ old('gelar_belakang') }}">
                    </div>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" required value="{{ old('email') }}">
                </div>

                <div class="mb-3">
                    <label for="role" class="form-label">Role</label>
                    <select name="role" id="role" class="form-select" required onchange="toggleExtraFields()">
                        <option value="user" {{ old('role') == 'user' ? 'selected' : '' }}>User</option>
                        <option value="ketua" {{ old('role') == 'ketua' ? 'selected' : '' }}>Ketua</option>
                        <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                    </select>
                </div>

                {{-- Field tambahan untuk role user --}}
                <div id="user-extra-fields" style="display: none;">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="dapil" class="form-label">Dapil</label>
                            <input type="text" name="dapil" class="form-control" value="{{ old('dapil') }}">
                        </div>
                        <div class="col-md-6">
                            <label for="jumlah_suara_sebelumnya" class="form-label">Jumlah Suara Sebelumnya</label>
                            <input type="number" name="jumlah_suara_sebelumnya" class="form-control" min="0" value="{{ old('jumlah_suara_sebelumnya', 0) }}">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="jumlah_suara" class="form-label">Jumlah Suara Sekarang</label>
                        <input type="number" name="jumlah_suara" class="form-control" min="0" value="{{ old('jumlah_suara', 0) }}">
                    </div>

                    <div class="mb-3">
                        <label for="jumlah_tim" class="form-label">Jumlah Tim</label>
                        <input type="number" name="jumlah_tim" class="form-control" min="0" value="{{ old('jumlah_tim', 0) }}">
                    </div>

                    <div class="mb-3">
                        <label for="tempat_lahir" class="form-label">Tempat Lahir</label>
                        <input type="text" name="tempat_lahir" class="form-control" value="{{ old('tempat_lahir') }}">
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="tanggal_lahir" class="form-label">Tanggal Lahir</label>
                            <input type="date" name="tanggal_lahir" class="form-control" value="{{ old('tanggal_lahir') }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="usia" class="form-label">Usia</label>
                            <input type="number" name="usia" class="form-control" min="0" value="{{ old('usia') }}">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="jenis_kelamin" class="form-label">Jenis Kelamin</label>
                            <select name="jenis_kelamin" class="form-select">
                                <option value="L" {{ old('jenis_kelamin') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="P" {{ old('jenis_kelamin') == 'P' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="agama" class="form-label">Agama</label>
                            <input type="text" name="agama" class="form-control" value="{{ old('agama') }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="status_perkawinan" class="form-label">Status Perkawinan</label>
                            <input type="text" name="status_perkawinan" class="form-control" value="{{ old('status_perkawinan') }}">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="alamat_ktp" class="form-label">Alamat KTP</label>
                        <input type="text" name="alamat_ktp" class="form-control" value="{{ old('alamat_ktp') }}">
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="rt" class="form-label">RT</label>
                            <input type="text" name="rt" class="form-control" value="{{ old('rt') }}">
                        </div>
                        <div class="col-md-4">
                            <label for="rw" class="form-label">RW</label>
                            <input type="text" name="rw" class="form-control" value="{{ old('rw') }}">
                        </div>
                        <div class="col-md-4">
                            <label for="kecamatan" class="form-label">Kecamatan</label>
                            <input type="text" name="kecamatan" class="form-control" value="{{ old('kecamatan') }}">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="kelurahan_desa" class="form-label">Kelurahan / Desa</label>
                        <input type="text" name="kelurahan_desa" class="form-control" value="{{ old('kelurahan_desa') }}">
                    </div>

                    <div class="mb-3">
                        <label for="kabupaten" class="form-label">Kabupaten</label>
                        <input type="text" name="kabupaten" class="form-control" value="{{ old('kabupaten', 'Bengkalis') }}">
                    </div>

                    <div class="mb-3">
                        <label for="nomor_kta" class="form-label">Nomor KTA</label>
                        <input type="text" name="nomor_kta" class="form-control" value="{{ old('nomor_kta') }}">
                    </div>

                    <div class="mb-3">
                        <label for="foto_ktp" class="form-label">Foto KTP</label>
                        <input type="file" name="foto_ktp" class="form-control" accept="image/*">
                    </div>

                    <div class="mb-3">
                        <label for="foto_kta" class="form-label">Foto KTA</label>
                        <input type="file" name="foto_kta" class="form-control" accept="image/*">
                    </div>
                </div>

                <div class="mb-3">
                    <label for="foto_profil" class="form-label">Foto Profil (opsional)</label>
                    <input type="file" name="foto_profil" class="form-control" accept="image/*">
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" class="form-control" required>
                </div>

                <div class="d-flex gap-2">
                    <button class="btn btn-success" type="submit">Simpan</button>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function toggleExtraFields() {
        const role = document.getElementById('role').value;
        const extraFields = document.getElementById('user-extra-fields');
        extraFields.style.display = role === 'user' ? 'block' : 'none';
    }

    document.addEventListener('DOMContentLoaded', toggleExtraFields);
</script>

@endsection
