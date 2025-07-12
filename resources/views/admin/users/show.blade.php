@extends('layouts.app')

@section('content')
<style>
    body {
        background: #f6f9f8 !important;
    }

    .profile-wrapper {
        max-width: 1000px;
        margin: 0 auto 2rem;
        background: #fff;
        border-radius: 12px;
        padding: 2rem;
        box-shadow: 0 4px 16px rgba(0,0,0,0.06);
        display: flex;
        gap: 2rem;
        position: relative;
    }

    .btn-back {
        position: absolute;
        top: 20px;
        right: 20px;
    }

    .left-panel {
        width: 280px;
        flex-shrink: 0;
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .left-panel img {
        width: 100%;
        height: auto;
        border-radius: 10px;
        border: 1px solid #ccc;
    }

    .dprd-info-card {
        width: 100%;
        margin-top: 1rem;
        background: #f4f6fa;
        border: 1px solid #d9e2ec;
        border-radius: 10px;
        padding: 1rem;
        text-align: center;
    }

    .dprd-info-card h6 {
        font-weight: 700;
        margin-bottom: 0.5rem;
        color: #2b2b2b;
    }

    .dprd-info-card .info-item {
        font-size: 0.95rem;
        color: #444;
        margin-bottom: 0.25rem;
    }

    .right-panel {
        flex: 1;
    }

    .section-title {
        font-weight: 700;
        font-size: 1rem;
        margin: 1.5rem 0 0.75rem;
        color: #333;
        padding-bottom: 6px;
        border-bottom: 2px solid #dee2e6;
    }

    .data-table {
        width: 100%;
        border-collapse: collapse;
    }

    .data-table tr {
        border-bottom: 1px dashed #ccc;
    }

    .data-label {
        width: 200px;
        padding: 8px 0;
        font-weight: 600;
        color: #444;
        text-align: left;
        vertical-align: top;
        position: relative;
        padding-right: 20px;
    }

    .data-label::after {
        content: ":";
        position: absolute;
        right: 8px;
    }

    .data-value {
        padding: 8px 0;
        color: #222;
    }

    @media (max-width: 768px) {
        .profile-wrapper {
            flex-direction: column;
        }

        .left-panel,
        .right-panel {
            width: 100%;
        }

        .data-label {
            width: 150px;
        }
    }
</style>

<div class="container">
    <div class="profile-wrapper">
        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary btn-sm btn-back">
            <i class="bi bi-arrow-left-circle"></i> Kembali
        </a>

        <div class="left-panel">
            @if($user->foto_profil)
                <img src="{{ asset('storage/' . $user->foto_profil) }}" alt="Foto Profil">
            @else
                <img src="{{ asset('assets/default-avatar.png') }}" alt="Default">
            @endif

            <div class="dprd-info-card">
                <h6>Anggota DPRD Bengkalis</h6>
                <div class="info-item"><strong>Dapil:</strong> {{ $user->dapil }}</div>
                <div class="info-item"><strong>Suara Sebelumnya:</strong> {{ number_format($user->jumlah_suara_sebelumnya ?? 0) }}</div>
            </div>
        </div>

        <div class="right-panel">
            <div class="section-title">Data Pribadi</div>
            <table class="data-table">
                <tr><td class="data-label">Nama Lengkap</td><td class="data-value">{{ $user->gelar_depan }} {{ $user->name }}{{ $user->gelar_belakang ? ', '.$user->gelar_belakang : '' }}</td></tr>
                <tr><td class="data-label">NIK</td><td class="data-value">{{ $user->nik }}</td></tr>
                <tr><td class="data-label">Foto KTP</td>
                    <td class="data-value">
                        @if($user->foto_ktp)
                            <a href="{{ asset('storage/' . $user->foto_ktp) }}" target="_blank">Lihat Dokumen</a>
                        @else
                            Tidak tersedia
                        @endif
                    </td>
                </tr>
                <tr><td class="data-label">Foto KTA</td>
                    <td class="data-value">
                        @if($user->foto_kta)
                            <a href="{{ asset('storage/' . $user->foto_kta) }}" target="_blank">Lihat Dokumen</a>
                        @else
                            Tidak tersedia
                        @endif
                    </td>
                </tr>
                <tr><td class="data-label">Tempat Lahir</td><td class="data-value">{{ $user->tempat_lahir }}</td></tr>
                <tr><td class="data-label">Tanggal Lahir</td><td class="data-value">{{ \Carbon\Carbon::parse($user->tanggal_lahir)->format('d-m-Y') }}</td></tr>
                <tr><td class="data-label">Usia</td><td class="data-value">{{ $user->usia }} Tahun</td></tr>
                <tr><td class="data-label">Jenis Kelamin</td><td class="data-value">{{ $user->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</td></tr>
                <tr><td class="data-label">Agama</td><td class="data-value">{{ $user->agama }}</td></tr>
                <tr><td class="data-label">Status Perkawinan</td><td class="data-value">{{ $user->status_perkawinan }}</td></tr>
            </table>

            <div class="section-title">Alamat KTP</div>
            <table class="data-table">
                <tr><td class="data-label">Alamat Lengkap</td><td class="data-value">{{ $user->alamat_ktp }}</td></tr>
                <tr><td class="data-label">RT</td><td class="data-value">{{ $user->rt }}</td></tr>
                <tr><td class="data-label">RW</td><td class="data-value">{{ $user->rw }}</td></tr>
                <tr><td class="data-label">Kabupaten</td><td class="data-value">{{ $user->kabupaten }}</td></tr>
                <tr><td class="data-label">Kecamatan</td><td class="data-value">{{ $user->kecamatan }}</td></tr>
                </table>
        </div>
    </div>
</div>
@endsection
