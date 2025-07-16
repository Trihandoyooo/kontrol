@extends('layouts.app')

@section('content')
<style>
    body {
        background: rgb(236, 244, 239) !important;
    }
</style>

<div class="mt-4">
    <div class="card-container">
        <div class="page-heading mb-3">
            <h3>Edit Data User</h3>
            <p class="text-subtitle text-muted">
                Berikut merupakan data user yang bisa anda edit di sini.
            </p>
        </div>
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.users.update', $user->nik) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                {{-- Informasi Dasar --}}
                <div class="mb-3">
                    <label for="nik" class="form-label">NIK</label>
                    <input type="text" name="nik" id="nik" class="form-control" value="{{ old('nik', $user->nik) }}" required>
                </div>

                <div class="mb-3">
                    <label for="name" class="form-label">Nama Lengkap</label>
                    <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="gelar_depan" class="form-label">Gelar Depan</label>
                        <input type="text" name="gelar_depan" class="form-control" value="{{ old('gelar_depan', $user->gelar_depan) }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="gelar_belakang" class="form-label">Gelar Belakang</label>
                        <input type="text" name="gelar_belakang" class="form-control" value="{{ old('gelar_belakang', $user->gelar_belakang) }}">
                    </div>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                </div>

                <div class="mb-3">
                    <label for="role" class="form-label">Role</label>
                    <select name="role" id="role" class="form-select" required onchange="toggleExtraFields()">
                        <option value="user" {{ $user->role === 'user' ? 'selected' : '' }}>User</option>
                        <option value="ketua" {{ $user->role === 'ketua' ? 'selected' : '' }}>Ketua</option>
                        <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin</option>
                    </select>
                </div>

                {{-- Tambahan jika role user --}}
                <div id="user-extra-fields" style="display: none;">
                    {{-- Dapil dan Suara --}}
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="dapil" class="form-label">Dapil</label>
                            <input type="text" name="dapil" class="form-control" value="{{ old('dapil', $user->dapil) }}">
                        </div>
                        <div class="col-md-6">
                            <label for="jumlah_suara_sebelumnya" class="form-label">Jumlah Suara Sebelumnya</label>
                            <input type="number" name="jumlah_suara_sebelumnya" class="form-control" min="0" value="{{ old('jumlah_suara_sebelumnya', $user->jumlah_suara_sebelumnya) }}">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="jumlah_suara" class="form-label">Jumlah Suara Sekarang</label>
                            <input type="number" name="jumlah_suara" class="form-control" min="0" value="{{ old('jumlah_suara', $user->jumlah_suara) }}">
                        </div>
                        <div class="col-md-6">
                            <label for="jumlah_tim" class="form-label">Jumlah Tim</label>
                            <input type="number" name="jumlah_tim" class="form-control" min="0" value="{{ old('jumlah_tim', $user->jumlah_tim) }}">
                        </div>
                    </div>

                    {{-- KTA & KTP --}}
                    <div class="mb-3">
                        <label for="nomor_kta" class="form-label">Nomor KTA</label>
                        <input type="text" name="nomor_kta" class="form-control" value="{{ old('nomor_kta', $user->nomor_kta) }}">
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="foto_ktp" class="form-label">Foto KTP</label>
                            <input type="file" name="foto_ktp" class="form-control" accept="image/*">
                            @if($user->foto_ktp)
                                <small class="text-muted">Saat ini: {{ $user->foto_ktp }}</small>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <label for="foto_kta" class="form-label">Foto KTA</label>
                            <input type="file" name="foto_kta" class="form-control" accept="image/*">
                            @if($user->foto_kta)
                                <small class="text-muted">Saat ini: {{ $user->foto_kta }}</small>
                            @endif
                        </div>
                    </div>

                    {{-- Lahir dan Data Pribadi --}}
                    <div class="mb-3">
                        <label for="tempat_lahir" class="form-label">Tempat Lahir</label>
                        <input type="text" name="tempat_lahir" class="form-control" value="{{ old('tempat_lahir', $user->tempat_lahir) }}">
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="tanggal_lahir" class="form-label">Tanggal Lahir</label>
                            <input type="date" name="tanggal_lahir" class="form-control" value="{{ old('tanggal_lahir', $user->tanggal_lahir) }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="usia" class="form-label">Usia</label>
                            <input type="number" name="usia" class="form-control" value="{{ old('usia', $user->usia) }}">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="jenis_kelamin" class="form-label">Jenis Kelamin</label>
                            <select name="jenis_kelamin" class="form-select">
                                <option value="L" {{ $user->jenis_kelamin == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="P" {{ $user->jenis_kelamin == 'P' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="agama" class="form-label">Agama</label>
                            <input type="text" name="agama" class="form-control" value="{{ old('agama', $user->agama) }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="status_perkawinan" class="form-label">Status Perkawinan</label>
                            <input type="text" name="status_perkawinan" class="form-control" value="{{ old('status_perkawinan', $user->status_perkawinan) }}">
                        </div>
                    </div>

                    {{-- Alamat --}}
                    <div class="mb-3">
                        <label for="alamat_ktp" class="form-label">Alamat KTP</label>
                        <input type="text" name="alamat_ktp" class="form-control" value="{{ old('alamat_ktp', $user->alamat_ktp) }}">
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="rt" class="form-label">RT</label>
                            <input type="text" name="rt" class="form-control" value="{{ old('rt', $user->rt) }}">
                        </div>
                        <div class="col-md-4">
                            <label for="rw" class="form-label">RW</label>
                            <input type="text" name="rw" class="form-control" value="{{ old('rw', $user->rw) }}">
                        </div>
                        <div class="col-md-4">
                            <label for="kecamatan" class="form-label">Kecamatan</label>
                            <input type="text" name="kecamatan" class="form-control" value="{{ old('kecamatan', $user->kecamatan) }}">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="kelurahan_desa" class="form-label">Kelurahan / Desa</label>
                        <input type="text" name="kelurahan_desa" class="form-control" value="{{ old('kelurahan_desa', $user->kelurahan_desa) }}">
                    </div>

                    <div class="mb-3">
                        <label for="kabupaten" class="form-label">Kabupaten</label>
                        <input type="text" name="kabupaten" class="form-control" value="{{ old('kabupaten', $user->kabupaten ?? 'Bengkalis') }}">
                    </div>
                </div>

                {{-- Foto Profil --}}
                <div class="mb-3">
                    <label for="foto_profil" class="form-label">Foto Profil</label>
                    <input type="file" name="foto_profil" class="form-control" accept="image/*">
                    @if($user->foto_profil)
                        <small class="text-muted">Foto saat ini: {{ $user->foto_profil }}</small><br>
                        <img src="{{ asset('profil/' . $user->foto_profil) }}" alt="Foto Profil" class="img-thumbnail" style="max-height: 120px;">
                    @endif
                </div>

                {{-- Password --}}
                <div class="mb-3">
                    <label for="password" class="form-label">Password Baru (opsional)</label>
                    <input type="password" name="password" class="form-control">
                </div>

                <div class="mb-3">
                    <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" class="form-control">
                </div>

                <button type="submit" class="btn btn-outline-success me-1">Update</button>
                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">Batal</a>
            </form>
        </div>
    </div>
</div>

<script>
    function toggleExtraFields() {
        const role = document.getElementById('role').value;
        document.getElementById('user-extra-fields').style.display = role === 'user' ? 'block' : 'none';
    }
    document.addEventListener('DOMContentLoaded', toggleExtraFields);
</script>
@endsection
