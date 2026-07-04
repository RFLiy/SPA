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
        padding: 10px;
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

    .password-hint {
        font-size: 0.72rem;
        color: #6c757d;
        margin-top: 2px;
        display: block;
    }
</style>

<div class="container min-vh-100 d-flex justify-content-center align-items-center py-4">
    <div class="card auth-card" style="max-width: 1000px; width: 100%; height: auto;">
        <div class="row g-0 align-items-stretch" style="min-height: 100%;">

            <div class="col-md-6 p-4 p-lg-5 bg-white d-flex flex-column justify-content-center">
                <div class="mb-3">
                    <h2 class="fw-bold text-warning text-center m-0">Daftar Akun Baru</h2>
                </div>
                <form method="POST" action="{{ route('register') }}" id="registerForm">
                    @csrf
                    <div class="form-floating mb-2">
                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                            id="name" name="name" value="{{ old('name') }}" required placeholder="Nama Lengkap">
                        <label for="name">Nama Lengkap</label>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-floating mb-2">
                        <input type="email" class="form-control @error('email') is-invalid @enderror"
                            id="email" name="email" value="{{ old('email') }}" required placeholder="Email">
                        <label for="email">Email</label>
                        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-floating mb-2">
                        <input type="text" class="form-control @error('address') is-invalid @enderror"
                            id="address" name="address" value="{{ old('address') }}" required placeholder="Alamat">
                        <label for="address">Alamat</label>
                        @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-floating mb-2">
                        <input type="text" class="form-control @error('no_tlp') is-invalid @enderror"
                            id="no_tlp" name="no_tlp" value="{{ old('no_tlp') }}" required placeholder="Nomor Telpon">
                        <label for="no_tlp">Nomor Telpon</label>
                        @error('no_tlp') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-floating mb-2 position-relative">
                        <input type="password" class="form-control @error('password') is-invalid @enderror"
                            id="password" name="password" required placeholder="Password" style="padding-right: 45px;">
                        <label for="password">Password</label>

                        <button type="button" id="togglePassword"
                            class="btn position-absolute end-0 border-0 text-muted"
                            style="z-index: 10; margin-right: 10px; top: 10px;">
                            <i class="fas fa-eye" id="eyeIcon"></i>
                        </button>

                        @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-floating mb-3 position-relative">
                        <input type="password" class="form-control"
                            id="password_confirmation" name="password_confirmation"
                            required minlength="8"
                            placeholder="Konfirmasi Password" style="padding-right: 45px;">
                        <label for="password_confirmation">Konfirmasi Password</label>

                        <button type="button" id="togglePasswordConfirm"
                            class="btn position-absolute end-0 border-0 text-muted"
                            style="z-index: 10; margin-right: 10px; top: 10px;">
                            <i class="fas fa-eye" id="eyeIconConfirm"></i>
                        </button>

                        @error('password_confirmation')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @else
                        <small class="password-hint">
                            <i class="fas fa-info-circle me-1"></i> Minimal 8 karakter (kombinasi huruf & angka).
                        </small>
                        @enderror
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-dark btn-modern text-white">
                            Daftar
                        </button>
                        <a href="{{ route('login') }}" class="btn btn-link text-decoration-none text-muted text-center mt-1 p-0">
                            Sudah punya akun? <span class="text-warning fw-bold">Masuk</span>
                        </a>
                    </div>
                </form>
            </div>

            <div class="col-md-6 d-none d-md-flex flex-column justify-content-center p-5 text-white card-background">
                <div class="mb-2">
                    <span class="badge bg-warning text-dark mb-3 py-2 fs-6">PT. Sinar Perkasa Abadi</span>
                    <h2 class="fw-bold text-warning">Keamanan Data Anda Prioritas Kami.</h2>
                    <p class="opacity-75">Sistem kami menggunakan enkripsi end-to-end untuk memastikan informasi pribadi Anda tetap aman dan terlindungi.</p>
                </div>
                <div class="mt-4 border-top border-secondary pt-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-light rounded-circle p-2 me-3 text-dark d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                            <i class="fas fa-check fa-sm"></i>
                        </div>
                        <span>Enkripsi Password Bcrypt</span>
                    </div>
                    <div class="d-flex align-items-center">
                        <div class="bg-light rounded-circle p-2 me-3 text-dark d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                            <i class="fas fa-shield-alt fa-sm"></i>
                        </div>
                        <span>Proteksi Serangan CSRF</span>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    const togglePassword = document.querySelector('#togglePassword');
    const password = document.querySelector('#password');
    const eyeIcon = document.querySelector('#eyeIcon');

    togglePassword.addEventListener('click', function () {
        const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
        password.setAttribute('type', type);
        eyeIcon.classList.toggle('fa-eye');
        eyeIcon.classList.toggle('fa-eye-slash');
    });

    const togglePasswordConfirm = document.querySelector('#togglePasswordConfirm');
    const passwordConfirm = document.querySelector('#password_confirmation');
    const eyeIconConfirm = document.querySelector('#eyeIconConfirm');

    togglePasswordConfirm.addEventListener('click', function () {
        const type = passwordConfirm.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordConfirm.setAttribute('type', type);
        eyeIconConfirm.classList.toggle('fa-eye');
        eyeIconConfirm.classList.toggle('fa-eye-slash');
    });
</script>
@endsection
