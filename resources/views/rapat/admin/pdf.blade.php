<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Rapat</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.5;
        }
        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 1rem;
        }
        th, td {
            border: 1px solid #aaa;
            padding: 6px;
            text-align: left;
        }
        th {
            background-color: #d4edda;
        }
        h2 {
            text-align: center;
        }
    </style>
</head>
<body>

    <h2>Laporan Data Rapat</h2>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama User</th>
                <th>Jenis Rapat</th>
                <th>Judul</th>
                <th>Lokasi</th>
                <th>Tanggal</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rapats as $i => $rapat)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $rapat->user->name ?? '-' }}</td>
                <td>{{ $rapat->jenis_rapat }}</td>
                <td>{{ $rapat->judul }}</td>
                <td>{{ $rapat->lokasi }}</td>
                <td>{{ \Carbon\Carbon::parse($rapat->tanggal)->format('d-m-Y') }}</td>
                <td>{{ ucfirst($rapat->status) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>
