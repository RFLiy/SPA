@extends('layouts.guest')

@section('content')
<style>
    .auth-card {
        border-radius: 24px;
        overflow: hidden;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.08);
        border: none;
    }

    .form-floating>.form-control {
        border-radius: 12px;
        border: 1px solid #e0e0e0;
        background-color: #fdfdfd;
    }

    .form-floating>.form-control:focus {
        border-color: #ffc107;
        box-shadow: 0 0 0 4px rgba(255, 193, 7, 0.1);
    }

    .btn-modern {
        border-radius: 12px;
        padding: 14px;
        font-weight: 700;
        letter-spacing: 0.5px;
        transition: all 0.3s ease;
    }

    .btn-modern:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(255, 193, 7, 0.3);
    }

    .card-background {
        background: linear-gradient(135deg, #212529 0%, #343a40 100%);
        position: relative;
    }
</style>

<div class="container min-vh-100 d-flex justify-content-center align-items-center py-5">
    <div class="card auth-card" style="max-width: 1000px; width: 100%;">
        <div class="row g-0">
            <div class="col-md-6 d-none d-md-flex flex-column justify-content-center p-5 text-white card-background text-center">
                <div class="mb-4">
                    <i class="fas fa-user-shield fa-4x mb-4 text-warning"></i>
                    <p>PT.Sinar Perkasa Abadi</p>
                    <h2 class="fw-bold">Selamat Datang Kembali!</h2>
                    <p class="opacity-75">Silakan masuk untuk melanjutkan pembelian produk.</p>
                </div>
                <div class="mt-4 border-top border-secondary pt-4 text-start">
                    <small class="d-block mb-2"><i class="fas fa-info-circle me-2 text-warning"></i> Pastikan koneksi internet Anda aman.</small>
                    <small class="d-block"><i class="fas fa-lock me-2 text-warning"></i> Sesi Anda akan dienkripsi secara otomatis.</small>
                </div>
            </div>
            <div class="col-md-6 p-4 p-lg-5 bg-white">
                <div class="text-center mb-5">
                    <h2 class="fw-bold text-warning">Login</h2>
                </div>
                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="form-floating mb-3">
                        <input type="email" class="form-control @error('email') is-invalid @enderror"
                            id="email" name="email" value="{{ old('email') }}" required autofocus placeholder="Email">
                        <label for="email">Email</label>
                        @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-floating mb-3 position-relative">
                        <input type="password" class="form-control @error('password') is-invalid @enderror"
                            id="password" name="password" required placeholder="Password" style="padding-right: 45px;">
                        <label for="password">Password</label>

                        <button type="button" id="togglePassword"
                            class="btn position-absolute top-50 end-0 translate-middle-y border-0 text-muted"
                            style="z-index: 10; margin-right: 10px;">
                            <i class="fas fa-eye" id="eyeIcon"></i>
                        </button>

                        @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-4 px-1">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                            <label class="form-check-label small text-muted" for="remember">
                                Ingat Saya
                            </label>
                        </div>
                        @if (Route::has('password.request'))
                        <a class="small text-warning fw-bold text-decoration-none" href="{{ route('password.request') }}">
                            Lupa Password?
                        </a>
                        @endif
                    </div>
                    <div class="d-grid gap-3">
                        <button type="submit" class="btn btn-dark btn-modern text-white shadow-sm">
                            Login
                        </button>
                        <p class="text-center small text-muted mt-2">
                            Belum punya akun?
                            <a href="{{ route('register') }}" class="text-warning fw-bold text-decoration-none">Daftar Akun</a>
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    const togglePassword = document.querySelector('#togglePassword');
    const password = document.querySelector('#password');
    const eyeIcon = document.querySelector('#eyeIcon');

    togglePassword.addEventListener('click', function () {
        // Toggle tipe input
        const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
        password.setAttribute('type', type);

        // Toggle icon (FontAwesome)
        eyeIcon.classList.toggle('fa-eye');
        eyeIcon.classList.toggle('fa-eye-slash');
    });
</script>
@endsection

