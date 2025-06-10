```blade
@extends('layouts.app')

@section('content')
<style>
    body {
        background:rgb(236, 244, 239) !important; /* Putih lembut untuk background */
    }

    .card-container {
        border-radius: 16px;
        box-shadow: 0 8px 20px rgba(0, 128, 0, 0.1); /* Hijau transparan pada shadow */
        padding: 1.5rem 2rem 2.5rem 2rem;
        background: linear-gradient(145deg, #ffffff, #e6f4ea); /* Kombinasi putih dan hijau muda */
        margin-bottom: 4rem;
    }

    .stats-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 60px;
        height: 60px;
        border-radius: 50%;
        color: #fff;
        font-size: 28px;
        box-shadow: 0 4px 10px rgba(0, 128, 0, 0.2); /* Hijau pada shadow */
        flex-shrink: 0;
    }

    .stats-icon.blue { background: linear-gradient(135deg, #0d6efd, #3f8efc); } /* Biru asli */
    .stats-icon.red { background: linear-gradient(135deg, #dc3545, #f15f6e); } /* Merah asli */
    .stats-icon.green { background: linear-gradient(135deg, #198754, #43c27e); } /* Hijau asli */
    .stats-icon.orange { background: linear-gradient(135deg, #fd7e14, #fcb16d); } /* Oranye asli */

    .stats-label { font-size: 0.85rem; color: #4a7043; } /* Hijau tua untuk teks */
    .stats-title { font-size: 1.4rem; font-weight: bold; color: #1b5e20; } /* Hijau gelap */
    .stats-desc { font-size: 0.75rem; color: #6b7280; } /* Abu-abu netral */

    .section-divider {
        border-top: 1px dashed #81c784; /* Hijau terang untuk divider */
        margin: 1.5rem 0;
    }

    .page-heading {
        margin-bottom: 0.25rem;
    }

    .page-heading h3 {
        color: #1b5e20; /* Hijau gelap untuk judul */
    }

    .page-description {
        font-size: 1rem;
        color: #4a7043; /* Hijau tua untuk deskripsi */
        max-width: 600px;
        margin-bottom: 1.5rem;
    }

    .chart-selector {
        margin-bottom: 1.5rem;
    }

    .chart-selector select {
        border: 1px solid #4caf50; /* Hijau untuk border */
        border-radius: 8px;
        padding: 0.5rem;
        font-size: 1rem;
        color: #1b5e20; /* Hijau gelap untuk teks */
        background-color: #e6f4ea; /* Hijau muda untuk background */
    }

    .chart-selector select:focus {
        outline: none;
        box-shadow: 0 0 0 2px rgba(76, 175, 80, 0.3); /* Hijau transparan untuk focus */
    }

    .chart-container {
        display: none;
    }

    .chart-container.active {
        display: block;
    }
</style>

<div class="page-content">
    <section class="row">
        <div class="col-12 col-lg-12">
            <div class="card-container">
                <div class="page-heading">
                    <h3>Selamat Datang di Dashboard Admin</h3>
                    <p>Berikut merupakan ringkasan statistik dan performa sistem secara cepat dan mudah.</p>
                </div>

                <h3 class="mb-4">Statistik Utama</h3>

                {{-- Statistik Total --}}
                <div class="row g-3">
                    @php
                        $stats = [
                            ['label' => 'User', 'value' => $user_total, 'desc' => 'User Anggota DPR Aktif', 'icon' => 'bi-people-fill', 'color' => 'blue'],
                            ['label' => 'Rapat', 'value' => $rapat_total, 'desc' => 'Rapat dan Koordinasi Tercatat', 'icon' => 'bi-calendar-event-fill', 'color' => 'red'],
                            ['label' => 'Iuran', 'value' => $iuran_total, 'desc' => 'Data Iuran Tercatat', 'icon' => 'bi-cash-coin', 'color' => 'green'],
                            ['label' => 'Kaderisasi', 'value' => $kaderisasi_total, 'desc' => 'Kegiatan Kaderisasi Tercatat', 'icon' => 'bi-person-check-fill', 'color' => 'orange'],
                        ];
                    @endphp

                    @foreach ($stats as $stat)
                        <div class="col-6 col-lg-3 col-md-6">
                            <div class="d-flex align-items-center bg-light rounded p-3 h-100">
                                <div class="stats-icon {{ $stat['color'] }} me-3 d-flex align-items-center justify-content-center">
                                    <i class="bi {{ $stat['icon'] }}"></i>
                                </div>
                                <div>
                                    <div class="stats-label">{{ $stat['label'] }}</div>
                                    <div class="stats-title">{{ $stat['value'] }}</div>
                                    <div class="stats-desc">{{ $stat['desc'] }}</div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="section-divider"></div>

                {{-- Pilihan Grafik --}}
                <h5 class="mb-3 mt-5">Statistik Data</h5>
                <div class="chart-selector">
                    <select id="chartSelect" onchange="switchChart()">
                        <option value="weekly">Per Minggu</option>
                        <option value="monthly">Per Bulan</option>
                        <option value="daily">Per Hari</option>
                    </select>
                </div>

                {{-- Kontainer Grafik --}}
                <div id="weeklyChartContainer" class="chart-container active">
                    <canvas id="weeklyChart" height="120"></canvas>
                </div>
                <div id="monthlyChartContainer" class="chart-container">
                    <canvas id="monthlyChart" height="120"></canvas>
                </div>
                <div id="dailyChartContainer" class="chart-container">
                    <canvas id="dailyChart" height="120"></canvas>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Data Grafik
    const mingguLabels = @json(array_slice($mingguLabels, 0, 4));
    const dataRapat = @json(array_slice($dataRapat, 0, 4));
    const dataIuran = @json(array_slice($dataIuran, 0, 4));
    const dataKaderisasi = @json(array_slice($dataKaderisasi, 0, 4));

    const bulanLabels = @json(array_slice($bulanLabels, 0, 4));
    const dataRapatBulan = @json(array_slice($dataRapatBulan, 0, 4));
    const dataIuranBulan = @json(array_slice($dataIuranBulan, 0, 4));
    const dataKaderisasiBulan = @json(array_slice($dataKaderisasiBulan, 0, 4));

    const hariLabels = @json(array_slice($hariLabels, 0, 7));
    const dataRapatHari = @json(array_slice($dataRapatHari, 0, 7));
    const dataIuranHari = @json(array_slice($dataIuranHari, 0, 7));
    const dataKaderisasiHari = @json(array_slice($dataKaderisasiHari, 0, 7));

    // Inisialisasi Grafik
    const ctxWeekly = document.getElementById('weeklyChart').getContext('2d');
    const weeklyChart = new Chart(ctxWeekly, {
        type: 'line',
        data: {
            labels: mingguLabels,
            datasets: [
                {
                    label: 'Rapat',
                    data: dataRapat,
                    borderColor: '#1b5e20', /* Hijau gelap */
                    backgroundColor: 'rgba(27, 94, 32, 0.1)',
                    fill: true
                },
                {
                    label: 'Iuran',
                    data: dataIuran,
                    borderColor: '#4caf50', /* Hijau terang */
                    backgroundColor: 'rgba(76, 175, 80, 0.1)',
                    fill: true
                },
                {
                    label: 'Kaderisasi',
                    data: dataKaderisasi,
                    borderColor: '#81c784', /* Hijau muda */
                    backgroundColor: 'rgba(129, 199, 132, 0.1)',
                    fill: true
                }
            ]
        },
        options: {
            responsive: true,
            interaction: {
                mode: 'index',
                intersect: false
            },
            stacked: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            },
            scales: {
                x: {
                    title: {
                        display: true,
                        text: 'Minggu ke-',
                        color: '#1b5e20' /* Hijau gelap */
                    }
                },
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Jumlah',
                        color: '#1b5e20' /* Hijau gelap */
                    }
                }
            }
        }
    });

    const ctxMonthly = document.getElementById('monthlyChart').getContext('2d');
    const monthlyChart = new Chart(ctxMonthly, {
        type: 'line', /* Ubah dari bar ke line */
        data: {
            labels: bulanLabels,
            datasets: [
                {
                    label: 'Rapat',
                    data: dataRapatBulan,
                    borderColor: '#1b5e20', /* Hijau gelap */
                    backgroundColor: 'rgba(27, 94, 32, 0.1)',
                    fill: true
                },
                {
                    label: 'Iuran',
                    data: dataIuranBulan,
                    borderColor: '#4caf50', /* Hijau terang */
                    backgroundColor: 'rgba(76, 175, 80, 0.1)',
                    fill: true
                },
                {
                    label: 'Kaderisasi',
                    data: dataKaderisasiBulan,
                    borderColor: '#81c784', /* Hijau muda */
                    backgroundColor: 'rgba(129, 199, 132, 0.1)',
                    fill: true
                }
            ]
        },
        options: {
            responsive: true,
            interaction: {
                mode: 'index',
                intersect: false
            },
            stacked: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            },
            scales: {
                x: {
                    title: {
                        display: true,
                        text: 'Bulan',
                        color: '#1b5e20' /* Hijau gelap */
                    }
                },
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Jumlah',
                        color: '#1b5e20' /* Hijau gelap */
                    }
                }
            }
        }
    });

    const ctxDaily = document.getElementById('dailyChart').getContext('2d');
    const dailyChart = new Chart(ctxDaily, {
        type: 'line',
        data: {
            labels: hariLabels,
            datasets: [
                {
                    label: 'Rapat',
                    data: dataRapatHari,
                    borderColor: '#1b5e20', /* Hijau gelap */
                    backgroundColor: 'rgba(27, 94, 32, 0.1)',
                    fill: true
                },
                {
                    label: 'Iuran',
                    data: dataIuranHari,
                    borderColor: '#4caf50', /* Hijau terang */
                    backgroundColor: 'rgba(76, 175, 80, 0.1)',
                    fill: true
                },
                {
                    label: 'Kaderisasi',
                    data: dataKaderisasiHari,
                    borderColor: '#81c784', /* Hijau muda */
                    backgroundColor: 'rgba(129, 199, 132, 0.1)',
                    fill: true
                }
            ]
        },
        options: {
            responsive: true,
            interaction: {
                mode: 'index',
                intersect: false
            },
            stacked: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            },
            scales: {
                x: {
                    title: {
                        display: true,
                        text: 'Hari',
                        color: '#1b5e20' /* Hijau gelap */
                    }
                },
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Jumlah',
                        color: '#1b5e20' /* Hijau gelap */
                    }
                }
            }
        }
    });

    // Fungsi untuk mengganti grafik
    function switchChart() {
        const select = document.getElementById('chartSelect');
        const containers = document.querySelectorAll('.chart-container');
        
        containers.forEach(container => {
            container.classList.remove('active');
        });

        const selectedChart = document.getElementById(`${select.value}ChartContainer`);
        if (selectedChart) {
            selectedChart.classList.add('active');
        }
    }

    // Debugging
    console.log('Weekly Labels:', mingguLabels);
    console.log('Weekly Rapat:', dataRapat);
    console.log('Weekly Iuran:', dataIuran);
    console.log('Weekly Kaderisasi:', dataKaderisasi);
    console.log('Monthly Labels:', bulanLabels);
    console.log('Monthly Rapat:', dataRapatBulan);
    console.log('Monthly Iuran:', dataIuranBulan);
    console.log('Monthly Kaderisasi:', dataKaderisasiBulan);
    console.log('Daily Labels:', hariLabels);
    console.log('Daily Rapat:', dataRapatHari);
    console.log('Daily Iuran:', dataIuranHari);
    console.log('Daily Kaderisasi:', dataKaderisasiHari);

    // Inisialisasi grafik pertama
    switchChart();
</script>
@endsection
```