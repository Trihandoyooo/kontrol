<!DOCTYPE html>
<html>
<head>
    <title>Laporan Iuran Bulanan</title>
    <style>
        body { font-family: sans-serif; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        table, th, td { border: 1px solid black; padding: 8px; }
        th { background-color: #f2f2f2; }
        .text-center { text-align: center; }
    </style>
</head>
<body>
    <h2 class="text-center">Laporan Iuran Bulan {{ \Carbon\Carbon::parse($bulan)->translatedFormat('F Y') }}</h2>

    @php
        $filtered = $iurans->filter(function($item) use ($bulan) {
            return \Carbon\Carbon::parse($item->tanggal)->format('Y-m') === $bulan
                && strtolower($item->status) !== 'terkirim';
        });
    @endphp

    @if($filtered->isEmpty())
        <p class="text-center"><em>Belum ada transaksi bulan ini.</em></p>
    @else
    <table>
        <thead>
            <tr>
                <th>Nama</th>
                <th>Jenis Iuran</th>
                <th>Nominal</th>
                <th>Tanggal</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($filtered as $item)
                <tr>
                    <td>{{ $item->user->name }}</td>
                    <td>{{ $item->jenis_iuran }}</td>
                    <td>Rp {{ number_format($item->nominal, 0, ',', '.') }}</td>
                    <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d-m-Y') }}</td>
                    <td>{{ ucfirst($item->status) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    @endif
</body>
</html>
