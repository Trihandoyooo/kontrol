<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <!-- Bootstrap Icons CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <!-- CSRF Token untuk keamanan form -->
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net" />
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet" />

    <!-- Styles for Mazer & Bootstrap -->
    <link rel="stylesheet" href="{{ asset('templates/assets/compiled/css/app.css') }}" />
    <link rel="stylesheet" href="{{ asset('templates/assets/compiled/css/iconly.css') }}" />

    <!-- Laravel Vite -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

    <style>
        /* Sidebar dasar */
        #sidebar {
            width: 250px;
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            background-color: #eafaf1;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.05);
            transition: margin-left 0.3s ease-in-out;
            z-index: 100;
        }

        /* Sidebar sembunyi */
        #app.sidebar-hidden #sidebar {
            margin-left: -250px;
        }

        /* Konten utama bergeser kanan saat sidebar tampil */
        #main {
            margin-left: 250px;
            transition: margin-left 0.3s ease-in-out;
            min-height: 100vh;
        }

        /* Konten utama penuh saat sidebar sembunyi */
        #app.sidebar-hidden #main {
            margin-left: 0;
        }

        /* Header di dalam main */
        header.mb-3 {
            padding: 1rem 1.5rem;
            background: white;
            border-bottom: 1px solid #ddd;
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        /* Margin kiri header menyesuaikan sidebar */
        header.mb-3 {
            margin-left: 0;
            transition: margin-left 0.3s ease-in-out;
            width: 100%;
        }

        /* Tombol toggle hanya muncul di mobile */
        #sidebarToggle {
            display: none;
        }

        @media (max-width: 768px) {
            /* Sidebar default sembunyi di mobile */
            #sidebar {
                margin-left: -250px;
            }

            /* Saat toggle aktif, sidebar tampil */
            #app.sidebar-hidden #sidebar {
                margin-left: 0;
            }

            /* Main full width di mobile */
            #main {
                margin-left: 0;
            }

            /* Saat toggle aktif, main geser kanan */
            #app.sidebar-hidden #main {
                margin-left: 250px;
            }

            /* Tombol toggle muncul di mobile */
            #sidebarToggle {
                display: inline-block;
            }
        }
    </style>
</head>

<body>
    <div id="app">
        @auth
            {{-- Sidebar --}}
            @include('components.sidebar')

            <div id="main">
                {{-- Header --}}
                </header>

                {{-- Konten halaman --}}
                <div class="page">
                    @yield('content')
                </div>

                {{-- Footer --}}
                @include('components.footer')
            </div>
        @else
            {{-- Navbar standar Laravel saat belum login --}}
            <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
                <div class="container">
                    <a class="navbar-brand" href="{{ url('/') }}">
                        {{ config('app.name', 'Laravel') }}
                    </a>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
                        aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                        <span class="navbar-toggler-icon"></span>
                    </button>

                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <!-- Left Side Of Navbar -->
                        <ul class="navbar-nav me-auto"></ul>

                        <!-- Right Side Of Navbar -->
                        <ul class="navbar-nav ms-auto">
                            @guest
                                @if (Route::has('login'))
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                                    </li>
                                @endif

                                @if (Route::has('register'))
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                    </li>
                                @endif
                            @else
                                <li class="nav-item dropdown">
                                    <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"
                                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                        {{ Auth::user()->name }}
                                    </a>

                                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                        <a class="dropdown-item" href="{{ route('logout') }}"
                                            onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                            {{ __('Logout') }}
                                        </a>

                                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                            @csrf
                                        </form>
                                    </div>
                                </li>
                            @endguest
                        </ul>
                    </div>
                </div>
            </nav>

            <main class="py-4">
                @yield('content')
            </main>
        @endauth
    </div>

    {{-- Script pendukung --}}
    <script src="{{ asset('templates/assets/static/js/components/light.js') }}"></script>
    <script src="{{ asset('templates/assets/extensions/perfect-scrollbar/perfect-scrollbar.min.js') }}"></script>
    <script src="{{ asset('templates/assets/compiled/js/app.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    @stack('scripts')

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const toggleButton = document.getElementById('sidebarToggle');
            toggleButton?.addEventListener('click', () => {
                document.getElementById('app').classList.toggle('sidebar-hidden');
            });
        });
    </script>
</body>

</html>
