<!DOCTYPE html>
<html>
<head>
    <title>Laporan Outcome</title>
    <style>
        body { font-family: sans-serif; }
        table { width: 100%; border-collapse: collapse; }
        table, th, td { border: 1px solid black; padding: 8px; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h2>Laporan Outcome</h2>
    <table>
        <thead>
            <tr>
                <th>Judul</th>
                <th>Tanggal</th>
                <th>Nama Kegiatan</th>
                <th>Dapil</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($outcomes as $item)
                <tr>
                    <td>{{ $item->judul }}</td>
                    <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d-m-Y') }}</td>
                    <td>{{ $item->nama_kegiatan }}</td>
                    <td>{{ $item->dapil }}</td>
                    <td>{{ ucfirst($item->status) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
