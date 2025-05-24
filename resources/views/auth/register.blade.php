<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Register - Mazer Admin Dashboard</title>
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />

    {{-- CSS --}}
    <link rel="stylesheet" href="{{ asset('templates/assets/compiled/css/app.css') }}" />
    <link rel="stylesheet" href="{{ asset('templates/assets/compiled/css/app-dark.css') }}" />
    <link rel="stylesheet" href="{{ asset('templates/assets/compiled/css/auth.css') }}" />
</head>

<body>
    <script src="{{ asset('assets/static/js/initTheme.js') }}"></script>

    <div id="auth">
        <div class="row h-100">
            <div class="col-lg-5 col-10 mx-auto my-auto">
                <div id="auth-left">

                    {{-- Logo --}}
                    <div class="auth-logo text-center mb-2">
                        <a href="{{ url('/') }}">
                            <img src="{{ asset('templates/assets/compiled/jpg/img2.png') }}" alt="Logo" style="width: 100px;" />
                        </a>
                    </div>

                    {{-- Title --}}
                    <h5 class="auth-title">Daftar Akun</h5>
                    <p class="auth-subtitle mb-3">Isi data Anda untuk mendaftar ke aplikasi monitoring dewan DPC PKB Bengkalis</p>

                    {{-- Error --}}
                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    {{-- Register Form --}}
                    <form action="{{ route('register') }}" method="POST">
                        @csrf

                        {{-- NIK --}}
                        <div class="form-group position-relative has-icon-left mb-4">
                            <input type="text" name="nik" class="form-control @error('nik') is-invalid @enderror" 
                                placeholder="NIK" value="{{ old('nik') }}" required autofocus />
                            <div class="form-control-icon">
                                <i class="bi bi-person-vcard"></i>
                            </div>
                            @error('nik')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        {{-- Name --}}
                        <div class="form-group position-relative has-icon-left mb-4">
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                placeholder="Username" value="{{ old('name') }}" required />
                            <div class="form-control-icon">
                                <i class="bi bi-person"></i>
                            </div>
                            @error('name')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        {{-- Email --}}
                        <div class="form-group position-relative has-icon-left mb-4">
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                                placeholder="Email" value="{{ old('email') }}" required />
                            <div class="form-control-icon">
                                <i class="bi bi-envelope"></i>
                            </div>
                            @error('email')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        {{-- Password --}}
                        <div class="form-group position-relative has-icon-left mb-4">
                            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" 
                                placeholder="Password" required autocomplete="new-password" />
                            <div class="form-control-icon">
                                <i class="bi bi-shield-lock"></i>
                            </div>
                            @error('password')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        {{-- Confirm Password --}}
                        <div class="form-group position-relative has-icon-left mb-4">
                            <input type="password" name="password_confirmation" class="form-control" 
                                placeholder="Konfirmasi Password" required autocomplete="new-password" />
                            <div class="form-control-icon">
                                <i class="bi bi-shield-lock"></i>
                            </div>
                        </div>

                        {{-- Submit --}}
                        <button type="submit" class="btn btn-success btn-block shadow-lg mt-5">Daftar</button>
                    </form>

                    {{-- Links --}}
                    <div class="text-center mt-5 text-lg">
                        <p class="text-gray-600">Sudah punya akun? <a href="{{ route('login') }}" class="text-success">Masuk</a>.</p>
                    </div>

                </div>
            </div>

            <div class="col-lg-7 d-none d-lg-block">
                <div id="auth-right">
                    {{-- Bisa tambahkan gambar atau elemen tambahan --}}
                </div>
            </div>
        </div>
    </div>
</body>

</html>
