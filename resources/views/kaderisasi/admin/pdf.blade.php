<!DOCTYPE html>
<html>
<head>
    <title>Laporan Kaderisasi</title>
    <style>
        body { font-family: sans-serif; }
        table { width: 100%; border-collapse: collapse; }
        table, th, td { border: 1px solid black; padding: 8px; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h2>Laporan Kaderisasi</h2>
    <table>
        <thead>
            <tr>
                <th>Judul</th>
                <th>Tanggal</th>
                <th>Peserta</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($kaderisasi as $item)
                <tr>
                    <td>{{ $item->judul }}</td>
                    <td>{{ $item->tanggal }}</td>
                    <td>{{ $item->peserta }}</td>
                    <td>{{ ucfirst($item->status) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
