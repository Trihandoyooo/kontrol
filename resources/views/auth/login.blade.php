<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Login - Mazer Admin Dashboard</title>
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />

    {{-- CSS --}}
    <link rel="stylesheet" href="{{ asset('templates/assets/compiled/css/app.css') }}" />
    <link rel="stylesheet" href="{{ asset('templates/assets/compiled/css/app-dark.css') }}" />
    <link rel="stylesheet" href="{{ asset('templates/assets/compiled/css/auth.css') }}" />
</head>

<body>
    <script src="{{ asset('templates/assets/static/js/initTheme.js') }}"></script>

    <div id="auth">
        <div class="row h-100">
            <div class="col-lg-5 col-10 mx-auto my-auto">
                <div id="auth-left">

                    {{-- Logo --}}
<div class="text-center mb-2">
    <img src="{{ asset('templates/assets/compiled/jpg/simoleglogo.png') }}" alt="Logo Besar" style="max-width: 100px; width: 100%; height: auto;" />
</div>


                    {{-- Title --}}
                    <h5 class="auth-title">Masuk</h5>
                    <p class="auth-subtitle mb-2">Selamat Datang di SIMOLEG</p>
                    <p class="auth-subtitle mb-3">DPC PKB Bengkalis</p>

                    {{-- Error Messages --}}
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            {{ $errors->first() }}
                        </div>
                    @endif

                    {{-- Login Form --}}
                    <form action="{{ route('login') }}" method="POST">
                        @csrf

                        {{-- NIK --}}
                        <div class="form-group position-relative has-icon-left mb-4">
                            <input type="text" name="nik" class="form-control" placeholder="NIK" required autofocus value="{{ old('nik') }}" />
                            <div class="form-control-icon">
                                <i class="bi bi-person-vcard"></i>
                            </div>
                            @error('nik')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        {{-- Password --}}
                        <div class="form-group position-relative has-icon-left mb-4">
                            <input type="password" name="password" class="form-control" placeholder="Password" required />
                            <div class="form-control-icon">
                                <i class="bi bi-shield-lock"></i>
                            </div>
                            @error('password')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        {{-- Remember Me --}}
                        <div class="form-check form-check-lg d-flex align-items-end">
                            <input class="form-check-input me-2" type="checkbox" name="remember" id="remember" />
                            <label class="form-check-label text-gray-600" for="remember">
                                Keep me logged in
                            </label>
                        </div>

                        {{-- Submit --}}
                        <button type="submit" class="btn btn-success btn-block shadow-lg mt-3">Log in</button>
                    </form>

                    {{-- Links --}}
                    <div class="text-center mt-3 text-lg">
                        <p><a class="text-success" href="{{ route('password.request') }}">Lupa password?</a>.</p>
                    </div>

                </div>
            </div>

            <div class="col-lg-7 d-none d-lg-block">
                <div id="auth-right">
                    <div id="auth-right">
    <img src="{{ asset('templates/assets/compiled/jpg/architecture1.jpg') }}" 
         alt="Ilustrasi Login" 
         style="width: 100%; height: 100%;" />
</div>

                </div>
            </div>
        </div>
    </div>
</body>

</html>
