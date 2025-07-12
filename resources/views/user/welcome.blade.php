@extends('layouts.app')
<x-header />

@section('content')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
@endpush

<style>
    body {
        background: #ecf4ef !important;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    #main {
        padding: 20px;
        max-width: 1200px;
        margin: auto;
    }

    .card {
        border-radius: 16px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        border: none;
        background: #ffffff;
    }

    .avatar-2xl img {
        width: 120px;
        height: 120px;
        object-fit: cover;
        border-radius: 50%;
        box-shadow: 0 0 10px rgba(39, 174, 96, 0.4);
    }

    h4 {
        font-size: 1.5rem;
    }

    .progress-circle {
        position: relative;
        width: 100px;
        height: 100px;
        margin: auto;
    }

    .progress-circle svg {
        transform: rotate(-90deg);
        width: 100px;
        height: 100px;
    }

    .progress-circle circle {
        fill: none;
        stroke-width: 10;
        stroke-linecap: round;
        transition: stroke-dashoffset 1s ease;
    }

    .progress-bg {
        stroke: #e0e0e0;
    }

    .progress-bar {
        stroke-dasharray: 283;
        stroke-dashoffset: 283;
    }

    .progress-text {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        font-weight: 600;
        color: #0e4882;
        user-select: none;
    }

    .section-divider-with-text {
        position: relative;
        text-align: center;
        margin: 32px 0;
    }

    .section-divider-with-text::before {
        content: "";
        position: absolute;
        top: 50%;
        left: 0;
        width: 100%;
        height: 2px;
        background: #f2f7f5;
        transform: translateY(-50%);
    }

    .section-divider-with-text span {
        position: relative;
        background: #ffffff;
        padding: 0 12px;
        font-size: 1rem;
        font-weight: 600;
        color: #27ae60;
        z-index: 1;
    }
</style>

<div id="main">
    <div class="card p-4">

        {{-- Profil --}}
        <div class="row align-items-center mb-3">
            <div class="col-md-3 text-center">
                <div class="avatar-2xl mb-2">
                    <img src="{{ Auth::user()->foto_profil ? asset('storage/' . Auth::user()->foto_profil) : asset('templates/assets/compiled/jpg/riza.jpg') }}" alt="Foto Profil">
                </div>
                <div class="text-muted">Nomor KTA</div>
                <div class="fw-semibold">{{ Auth::user()->no_kta ?? '-' }}</div>
            </div>
            <div class="col-md-9">
                <h4 class="mb-1">
                    {{ Auth::user()->gelar_depan ? Auth::user()->gelar_depan . ' ' : '' }}
                    {{ Auth::user()->name }}
                    {{ Auth::user()->gelar_belakang ? ', ' . Auth::user()->gelar_belakang : '' }}
                </h4>
                <p class="text-muted mb-2">Anggota DPR Bengkalis</p>
                <div class="row text-success fw-semibold">
                    <div class="col-md-3 mb-1"><i class="bi bi-geo-alt"></i> Dapil {{ Auth::user()->dapil ?? '-' }}</div>
                    <div class="col-md-3 mb-1"><i class="bi bi-people"></i> Tim: {{ Auth::user()->jumlah_tim ?? '-' }}</div>
                    <div class="col-md-3 mb-1"><i class="bi bi-megaphone"></i> Suara: {{ number_format(Auth::user()->jumlah_suara_sebelumnya,0,',','.') }}</div>
                    <div class="col-md-3 mb-1"><i class="bi bi-person"></i> Usia: {{ Auth::user()->usia }} Tahun</div>
                </div>
            </div>
        </div>

        <div class="section-divider-with-text">
            <span>Statistik Aktivitas Anda</span>
        </div>

        {{-- Statistik --}}
        <div class="row text-center mb-3">
            <div class="col-md-4 mb-2">
                <div class="p-3 rounded" style="background:#eaf4fb;">
                    <div><i class="bi bi-people" style="color:#3498db; font-size:1.5rem;"></i></div>
                    <div class="fw-bold text-primary">{{ $totalRapat }}</div>
                    <small>Total Rapat</small>
                </div>
            </div>
            <div class="col-md-4 mb-2">
                <div class="p-3 rounded" style="background:#eafaf1;">
                    <div><i class="bi bi-cash-stack" style="color:#27ae60; font-size:1.5rem;"></i></div>
                    <div class="fw-bold text-success">Rp {{ number_format($iuranDihitung,0,',','.') }}</div>
                    <small>Total Iuran</small>
                </div>
            </div>
            <div class="col-md-4 mb-2">
                <div class="p-3 rounded" style="background:#f3eafc;">
                    <div><i class="bi bi-mortarboard" style="color:#9b59b6; font-size:1.5rem;"></i></div>
                    <div class="fw-bold" style="color:#9b59b6;">{{ $totalKaderisasi }}</div>
                    <small>Kaderisasi</small>
                </div>
            </div>
        </div>

        <div class="section-divider-with-text">
            <span>Progress Bulanan dan Pencapaian Target</span>
        </div>

        {{-- Progress Bulanan Circle --}}
        @php
            $progressRapat = min(100, round(($totalRapat / $targetRapat) * 100));
            $progressKaderisasi = min(100, round(($totalKaderisasi / $targetKaderisasi) * 100));
            $progressIuran = min(100, round(($iuranDihitung / $targetIuran) * 100));
        @endphp

        <div class="row text-center">
            @foreach([
                ['label' => 'Rapat', 'value' => $progressRapat, 'color' => '#3498db'],
                ['label' => 'Iuran', 'value' => $progressIuran, 'color' => '#27ae60'],
                ['label' => 'Kaderisasi', 'value' => $progressKaderisasi, 'color' => '#9b59b6']
            ] as $item)
            <div class="col-md-4 mb-3">
                <div class="p-3 rounded card">
                    <div class="progress-circle" data-progress="{{ $item['value'] }}">
                        <svg>
                            <circle class="progress-bg" cx="50" cy="50" r="45"></circle>
                            <circle class="progress-bar" cx="50" cy="50" r="45" data-color="{{ $item['color'] }}"></circle>
                        </svg>
                        <div class="progress-text">{{ $item['value'] }}%</div>
                    </div>
                    <small class="fw-semibold mt-2 d-block">{{ $item['label'] }}</small>
                </div>
            </div>
            @endforeach
        </div>

        <div class="section-divider-with-text">
            <span>Pastikan Anda Tetap Aktif dan Konsisten</span>
        </div>

    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.progress-circle').forEach(circle => {
            const progress = parseFloat(circle.getAttribute('data-progress'));
            const radius = 45;
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
