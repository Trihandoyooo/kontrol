<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Outcome</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.5;
        }
        .header {
            display: table;
            width: 100%;
            margin-bottom: 10px;
            border-bottom: 2px solid #000;
            padding-bottom: 5px;
        }
        .logo-left,
        .logo-right {
            display: table-cell;
            vertical-align: middle;
            width: 60px;
        }
        .logo-left {
            text-align: left;
        }
        .logo-right {
            text-align: right;
        }
        .logo-left img,
        .logo-right img {
            height: 60px;
            width: 60px;
            object-fit: contain;
        }
        .header-title {
            display: table-cell;
            text-align: center;
            color: #086d46;
            font-weight: bold;
        }
        .header-title h2 {
            margin: 0;
            font-size: 18px;
            font-weight: bold;
        }
        .header-title p {
            margin: 2px 0;
            font-size: 12px;
            font-weight: bold;
            color: #086d46;
        }
        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 1rem;
            font-weight: bold;
        }
        th, td {
            border: 1px solid #aaa;
            padding: 6px;
            text-align: left;
        }
        th {
            background-color: #086d46;
            color: white;
        }
    </style>
</head>
<body>

    <div class="header">
        <div class="logo-left">
            <img src="file://{{ public_path('storage/logo/simoleglogo.jpg') }}" alt="Logo Aplikasi">
        </div>

        <div class="header-title">
            <h2>Laporan Outcome</h2>
            <p>Tanggal dan Waktu: {{ now()->translatedFormat('d F Y H:i:s') }}</p>
            <p>Periode: {{ $periode ?? 'Semua Data' }}</p>
        </div>

        <div class="logo-right">
            <img src="file://{{ public_path('storage/logo/pkblogo.jpg') }}" alt="Logo Partai">
        </div>
    </div>

@php
    $filtered = $outcomes->filter(function($item) {
        return in_array(strtolower($item->status), ['diterima', 'ditolak']);
    });
@endphp

@if($filtered->isEmpty())
    <p class="text-center"><em>Belum ada data outcome pada periode ini.</em></p>
@else
<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Judul</th>
            <th>Tanggal</th>
            <th>Nama Kegiatan</th>
            <th>Dapil</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($filtered as $i => $item)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $item->judul }}</td>
                <td>{{ \Carbon\Carbon::parse($item->tanggal)->translatedFormat('d F Y') }}</td>
                <td>{{ $item->nama_kegiatan }}</td>
                <td>{{ $item->dapil }}</td>
                <td>{{ ucfirst($item->status) }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
@endif

</body>
</html>
