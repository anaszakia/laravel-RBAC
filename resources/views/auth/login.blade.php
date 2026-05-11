@extends('layouts.auth')

@section('title', 'Sign In')

@section('content')
<main class="d-flex flex-column justify-content-center min-vh-100 py-6">
    <section>
        <div class="container">
            <div class="row mb-8">
                <div class="col-xl-4 offset-xl-4 col-md-12 col-12">
                    <div class="text-center">
                        <a href="{{ url('/dashboard') }}" class="fs-2 fw-bold d-flex align-items-center gap-2 justify-content-center mb-6">
                            <img src="{{ asset('images/brand/logo/logo-icon.svg') }}" alt="" />
                            <span>Dasher</span>
                        </a>
                        <h1 class="mb-1">Selamat Datang</h1>
                        <p class="mb-0">
                            Belum punya akun?
                            <a href="{{ route('register') }}" class="text-primary">Daftar di sini</a>
                        </p>
                    </div>
                </div>
            </div>
            <div class="row justify-content-center">
                <div class="col-xl-5 col-lg-6 col-md-8 col-12">
                    <div class="card card-lg mb-6">
                        <div class="card-body p-6">
                            <form method="POST" action="{{ route('login') }}" class="needs-validation mb-6" novalidate>
                                @csrf
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror"
                                        id="email" name="email" value="{{ old('email') }}" required />
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="password" class="form-label">Password</label>
                                    <div class="password-field position-relative">
                                        <input type="password" class="form-control fakePassword @error('password') is-invalid @enderror"
                                            id="password" name="password" required />
                                        <span><i class="ti ti-eye-off passwordToggler"></i></span>
                                        @error('password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="mb-4 d-flex align-items-center justify-content-between">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="remember" id="rememberMe" />
                                        <label class="form-check-label" for="rememberMe">Ingat Saya</label>
                                    </div>
                                    @if (Route::has('password.request'))
                                        <a href="{{ route('password.request') }}" class="text-primary">Lupa Password</a>
                                    @endif
                                </div>
                                <div class="d-grid">
                                    <button class="btn btn-primary" type="submit">Masuk</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
@endsection