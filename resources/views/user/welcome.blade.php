@extends('layouts.app')

@section('content')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
@endpush

<style>
    body {
        background: #f5f7fa !important;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    #main {
        margin-left: 20px;
        transition: margin-left 0.3s ease;
        padding: 20px;
    }

    .sidebar-collapsed #main {
        margin-left: 60px;
    }

    .card {
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        border: none;
    }

    .avatar-2xl img {
        width: 80px;
        height: 80px;
        object-fit: cover;
        border-radius: 50%;
        border: 3px solid #27ae60;
        box-shadow: 0 0 8px rgba(39, 174, 96, 0.3);
    }

    .progress-circle {
        position: relative;
        width: 80px;
        height: 80px;
        margin: auto;
    }

    .progress-circle svg {
        transform: rotate(-90deg);
        width: 80px;
        height: 80px;
    }

    .progress-circle circle {
        fill: none;
        stroke-width: 8;
        stroke-linecap: round;
        transition: stroke-dashoffset 1s ease;
    }

    .progress-bg {
        stroke: #e9ecef;
    }

    .progress-bar {
        stroke-dasharray: 226;
        stroke-dashoffset: 226;
        fill: none;
    }

    .stat-card {
        background: #ffffff;
        padding: 15px;
        border-radius: 10px;
        text-align: center;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }

    .stat-card h5 {
        font-size: 2rem;
        font-weight: 700;
        margin: 0;
    }

    .stat-card small {
        color: #6c757d;
    }

    .progress-text {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        font-weight: 600;
        color: #2c3e50;
        user-select: none;
    }
</style>

<div id="main">
    <div class="container-fluid">

        <!-- Informasi User -->
        <div class="card p-4 mb-4">
            <div class="d-flex align-items-center mb-4">
                <div class="avatar-2xl me-4">
                    <img src="{{ asset('templates/assets/compiled/jpg/riza.jpg') }}" alt="Avatar">
                </div>
                <div>
                    <h4 class="mb-0">{{ Auth::user()->name }}</h4>
                    <small class="text-muted">Anggota DPR Bengkalis</small><br>
                    <small class="text-success">ID: {{ Auth::user()->id }}</small>
                </div>
            </div>

            <!-- Statistik -->
            <div class="row">
                @php
                    $progressRapat = min(100, round(($totalRapat / $targetRapat) * 100));
                    $progressKaderisasi = min(100, round(($totalKaderisasi / $targetKaderisasi) * 100));
                    $progressIuran = min(100, round(($iuranDihitung / $targetIuran) * 100));
                @endphp
                @foreach ([
                    ['label' => 'Total Rapat', 'value' => $totalRapat, 'color' => '#3498db', 'icon' => 'bi-people'],
                    ['label' => 'Total Iuran', 'value' => 'Rp ' . number_format($iuranDihitung, 0, ',', '.'), 'color' => '#27ae60', 'icon' => 'bi-cash-stack'],
                    ['label' => 'Kaderisasi', 'value' => $totalKaderisasi, 'color' => '#9b59b6', 'icon' => 'bi-mortarboard']
                ] as $stat)
                    <div class="col-md-4 mb-3">
                        <div class="stat-card">
                            <i class="bi {{ $stat['icon'] }} mb-2" style="font-size: 2rem; color: {{ $stat['color'] }}"></i>
                            <h5 style="color: {{ $stat['color'] }}">{{ $stat['value'] }}</h5>
                            <small>{{ $stat['label'] }}</small>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Progress + Iuran + Task -->
        @php
            $tasks = [];
            if ($totalRapat < $targetRapat) $tasks[] = "Mengikuti Rapat (sisa " . ($targetRapat - $totalRapat) . ")";
            if ($iuranDihitung < $targetIuran) $tasks[] = "Melunasi Iuran Bulanan";
            if ($totalKaderisasi < $targetKaderisasi) $tasks[] = "Kegiatan Kaderisasi (sisa " . ($targetKaderisasi - $totalKaderisasi) . ")";
        @endphp

        <div class="row">
            <!-- Progress -->
            <div class="col-md-4 mb-4">
                <div class="card p-4 h-100" style="background: #e8f4fa;">
                    <h5><i class="bi bi-graph-up me-1"></i> Progress Bulanan</h5>
                    <div class="d-flex justify-content-around mt-3">
                        @foreach ([
                            ['label' => 'Rapat', 'value' => $progressRapat, 'color' => '#3498db'],
                            ['label' => 'Iuran', 'value' => $progressIuran, 'color' => '#27ae60'],
                            ['label' => 'Kaderisasi', 'value' => $progressKaderisasi, 'color' => '#9b59b6']
                        ] as $item)
                            <div class="text-center" style="width: 90px;">
                                <div class="progress-circle" data-progress="{{ $item['value'] }}">
                                    <svg>
                                        <circle class="progress-bg" cx="40" cy="40" r="36"></circle>
                                        <circle class="progress-bar" cx="40" cy="40" r="36" data-color="{{ $item['color'] }}"></circle>
                                    </svg>
                                    <div class="progress-text">{{ $item['value'] }}%</div>
                                </div>
                                <small class="d-block mt-2 fw-semibold">{{ $item['label'] }}</small>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Iuran Terpakai -->
            <div class="col-md-4 mb-4">
                <div class="card p-4 h-100" style="background: #eafaf1;">
                    <h5><i class="bi bi-wallet2 me-1"></i> Total Iuran Terpakai</h5>
                    <h3 class="text-success">Rp {{ number_format($iuranDihitung, 0, ',', '.') }}</h3>
                    <small class="text-muted d-block mb-3">Dari target Rp {{ number_format($targetIuran, 0, ',', '.') }}</small>
                </div>
            </div>

            <!-- Task List -->
            <div class="col-md-4 mb-4">
                <div class="card p-4 h-100" style="background: #fef9e7;">
                    <h5><i class="bi bi-list-task me-1"></i> Task List</h5>
                    <ul class="list-unstyled mt-3">
                        @forelse ($tasks as $index => $task)
                            <li class="mb-2">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="task{{ $index }}">
                                    <label class="form-check-label" for="task{{ $index }}">{{ $task }}</label>
                                </div>
                            </li>
                        @empty
                            <li class="text-success">Semua target telah tercapai! 🎉</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.progress-circle').forEach(circle => {
            const progress = parseFloat(circle.getAttribute('data-progress'));
            const radius = 36;
            const circumference = 2 * Math.PI * radius;
            const offset = circumference - (progress / 100) * circumference;

            const progressBar = circle.querySelector('.progress-bar');
            const color = progressBar.getAttribute('data-color');

            progressBar.style.stroke = color;
            progressBar.style.strokeDasharray = `${circumference}`;
            progressBar.style.strokeDashoffset = `${circumference}`;

            setTimeout(() => {
                progressBar.style.strokeDashoffset = offset;
            }, 100);
        });
    });
</script>
@endpush

@endsection
