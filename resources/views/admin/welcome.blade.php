@extends('layouts.app')

@section('content')
    <style>
        body {
            background: #f2f7f5 !important;
        }

        .card-container {
            border-radius: 10px;
            box-shadow: 0 8px 20px rgba(0, 128, 0, 0.1);
            padding: 1.5rem 2rem 2.5rem 2rem;
            background: #ffffff;
            margin-bottom: 4rem;
        }

        .stats-icon {
            position: relative;
            top: -4px;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 56px;
            height: 56px;
            border-radius: 50%;
            color: #fff;
            font-size: 26px;
            box-shadow: 0 4px 10px rgba(0, 128, 0, 0.2);
            margin: 0 auto 6px auto;
            transition: transform 0.3s ease;
        }

        .stats-icon:hover {
            transform: scale(1.1);
        }

        .stats-icon i {
            font-size: 24px;
            position: relative;
            top: -5px;
        }

        .stats-icon.blue {
            background: linear-gradient(135deg, #0d6efd, #3f8efc);
        }

        .stats-icon.red {
            background: linear-gradient(135deg, #dc3545, #f15f6e);
        }

        .stats-icon.green {
            background: linear-gradient(135deg, #198754, #43c27e);
        }

        .stats-icon.orange {
            background: linear-gradient(135deg, #fd7e14, #fcb16d);
        }

        .stats-icon.purple {
            background: linear-gradient(135deg, #6f42c1, #b191f9);
        }

        .stats-label {
            font-size: 0.85rem;
            color: #4a7043;
            text-align: center;
        }

        .stats-title {
            font-size: 1.4rem;
            font-weight: bold;
            color: #1b5e20;
            text-align: center;
        }

        .stats-desc {
            font-size: 0.75rem;
            color: #6b7280;
            text-align: center;
        }


    </style>

    <div class="mt-4">
        <div class="card-container">
            <div class="page-heading mb-3">
                <h3>Selamat Datang di Dashboard Admin</h3>
                <p>Berikut merupakan ringkasan statistik dan performa sistem secara cepat dan mudah.</p>
            </div>

            <!-- Tabs -->
            <ul class="nav nav-tabs mb-4" id="dashboardTab" role="tablist">
                <li class="nav-item">
                    <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-utama" type="button">Statistik
                        Utama</button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-waktu" type="button">Statistik
                        Waktu</button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-user" type="button">Statistik
                        User</button>
                </li>
            </ul>

            <div class="tab-content" id="dashboardTabContent">
                <!-- Statistik Utama -->
                <div class="tab-pane fade show active" id="tab-utama">
                    <div class="row g-3">
                        @php
                            $stats = [
                                [
                                    'label' => 'User',
                                    'value' => $user_total,
                                    'desc' => 'User Aktif',
                                    'icon' => 'bi-people-fill',
                                    'color' => 'blue',
                                ],
                                [
                                    'label' => 'Rapat',
                                    'value' => $rapat_total,
                                    'desc' => 'Rapat Tercatat',
                                    'icon' => 'bi-calendar-event-fill',
                                    'color' => 'red',
                                ],
                                [
                                    'label' => 'Iuran',
                                    'value' => number_format($iuran_total),
                                    'desc' => 'Iuran Masuk',
                                    'icon' => 'bi-cash-coin',
                                    'color' => 'green',
                                ],
                                [
                                    'label' => 'Kaderisasi',
                                    'value' => $kaderisasi_total,
                                    'desc' => 'Kegiatan Kaderisasi',
                                    'icon' => 'bi-person-check-fill',
                                    'color' => 'orange',
                                ],
                                [
                                    'label' => 'Outcome',
                                    'value' => $outcome_total,
                                    'desc' => 'Outcome Terverifikasi',
                                    'icon' => 'bi-graph-up-arrow',
                                    'color' => 'purple',
                                ],
                            ];
                        @endphp
                        @foreach ($stats as $stat)
                            <div class="col-6 col-md-4 col-lg-3">
                                <div class="d-flex flex-column align-items-center bg-light rounded p-3 h-100 shadow-sm">
                                    <div class="stats-icon {{ $stat['color'] }}">
                                        <i class="bi {{ $stat['icon'] }} me-2 mb-3"></i>
                                    </div>
                                    <div class="stats-label">{{ $stat['label'] }}</div>
                                    <div class="stats-title">{{ $stat['value'] }}</div>
                                    <div class="stats-desc">{{ $stat['desc'] }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Statistik Waktu -->
                <div class="tab-pane fade" id="tab-waktu">
                    <h5 class="text-success mb-2">Data Harian</h5>
                    <canvas id="dailyChart" class="mb-4" height="200"></canvas>
                    <h5 class="text-success mb-2">Data Mingguan</h5>
                    <canvas id="weeklyChart" class="mb-4" height="200"></canvas>
                    <h5 class="text-success mb-2">Data Bulanan</h5>
                    <canvas id="monthlyChart" height="200"></canvas>
                </div>

                <!-- Statistik User -->
                <div class="tab-pane fade" id="tab-user">
                    <h5 class="text-success mb-3">Leaderboard Kontribusi User</h5>
                    <canvas id="userTotalBarChart" height="{{ count($usersProgress) * 40 }}"></canvas>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const mingguLabels = @json($mingguLabels);
            const bulanLabels = @json($bulanLabels);
            const hariLabels = @json($hariLabels);

            const dataRapat = @json($dataRapat);
            const dataIuran = @json($dataIuran);
            const dataKaderisasi = @json($dataKaderisasi);
            const dataOutcome = @json($dataOutcome);

            const dataRapatBulan = @json($dataRapatBulan);
            const dataIuranBulan = @json($dataIuranBulan);
            const dataKaderisasiBulan = @json($dataKaderisasiBulan);
            const dataOutcomeBulan = @json($dataOutcomeBulan);

            const dataRapatHari = @json($dataRapatHari);
            const dataIuranHari = @json($dataIuranHari);
            const dataKaderisasiHari = @json($dataKaderisasiHari);
            const dataOutcomeHari = @json($dataOutcomeHari);

            const userTotalLabels = @json($usersProgress->pluck('name'));
            const userTotalPoints = @json($usersProgress->pluck('total_point'));

            const createLineChart = (ctx, labels, datasets) => new Chart(ctx, {
                type: 'line',
                data: {
                    labels,
                    datasets
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

            document.querySelector('button[data-bs-target="#tab-waktu"]').addEventListener('shown.bs.tab', () => {
                if (!window.dailyChartInstance) {
                    window.dailyChartInstance = createLineChart(document.getElementById('dailyChart')
                        .getContext('2d'), hariLabels, [{
                                label: 'Rapat',
                                data: dataRapatHari,
                                borderColor: '#dc3545',
                                backgroundColor: 'rgba(220,53,69,0.1)',
                                fill: true
                            },
                            {
                                label: 'Iuran',
                                data: dataIuranHari,
                                borderColor: '#198754',
                                backgroundColor: 'rgba(25,135,84,0.1)',
                                fill: true
                            },
                            {
                                label: 'Kaderisasi',
                                data: dataKaderisasiHari,
                                borderColor: '#fd7e14',
                                backgroundColor: 'rgba(253,126,20,0.1)',
                                fill: true
                            },
                            {
                                label: 'Outcome',
                                data: dataOutcomeHari,
                                borderColor: '#6f42c1',
                                backgroundColor: 'rgba(111,66,193,0.1)',
                                fill: true
                            }
                        ]);
                    window.weeklyChartInstance = createLineChart(document.getElementById('weeklyChart')
                        .getContext('2d'), mingguLabels, [{
                                label: 'Rapat',
                                data: dataRapat,
                                borderColor: '#dc3545',
                                backgroundColor: 'rgba(220,53,69,0.1)',
                                fill: true
                            },
                            {
                                label: 'Iuran',
                                data: dataIuran,
                                borderColor: '#198754',
                                backgroundColor: 'rgba(25,135,84,0.1)',
                                fill: true
                            },
                            {
                                label: 'Kaderisasi',
                                data: dataKaderisasi,
                                borderColor: '#fd7e14',
                                backgroundColor: 'rgba(253,126,20,0.1)',
                                fill: true
                            },
                            {
                                label: 'Outcome',
                                data: dataOutcome,
                                borderColor: '#6f42c1',
                                backgroundColor: 'rgba(111,66,193,0.1)',
                                fill: true
                            }
                        ]);
                    window.monthlyChartInstance = createLineChart(document.getElementById('monthlyChart')
                        .getContext('2d'), bulanLabels, [{
                                label: 'Rapat',
                                data: dataRapatBulan,
                                borderColor: '#dc3545',
                                backgroundColor: 'rgba(220,53,69,0.1)',
                                fill: true
                            },
                            {
                                label: 'Iuran',
                                data: dataIuranBulan,
                                borderColor: '#198754',
                                backgroundColor: 'rgba(25,135,84,0.1)',
                                fill: true
                            },
                            {
                                label: 'Kaderisasi',
                                data: dataKaderisasiBulan,
                                borderColor: '#fd7e14',
                                backgroundColor: 'rgba(253,126,20,0.1)',
                                fill: true
                            },
                            {
                                label: 'Outcome',
                                data: dataOutcomeBulan,
                                borderColor: '#6f42c1',
                                backgroundColor: 'rgba(111,66,193,0.1)',
                                fill: true
                            }
                        ]);
                }
            });

            new Chart(document.getElementById('userTotalBarChart').getContext('2d'), {
                type: 'bar',
                data: {
                    labels: userTotalLabels,
                    datasets: [{
                        label: 'Total Poin',
                        data: userTotalPoints,
                        backgroundColor: 'rgba(25,135,84,0.8)'
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        x: {
                            beginAtZero: true
                        }
                    }
                }
            });
        });
    </script>
@endpush
