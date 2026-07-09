@extends('layouts.app')

@section('content')
<div class="container-fluid py-3 px-3 px-md-5">
    <div class="row justify-content-center">
        <div class="col-12 col-xl-11">
            <div class="card border-0 shadow-sm p-4 p-md-5 profile-card">
                <div class="row">
                    <div class="col-lg-4 text-center border-end-lg">
                        <div class="mb-4 text-start">
                            <h6 class="mb-0 text-dark fw-bold">Hi,</h6>
                            <h5 class="fw-bold text-dark">Welcome {{ Auth::user()->name }}.</h5>
                        </div>
                        <div class="avatar-section my-5">
                            <div class="avatar-wrapper position-relative d-inline-block">
                                @if(Auth::user()->profile_photo_path)
                                    <img src="{{ Storage::url(Auth::user()->profile_photo_path) }}" class="rounded-circle shadow-sm main-avatar">
                                @else
                                    <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto shadow-sm empty-avatar">
                                        <i class="fas fa-user fa-4x"></i>
                                    </div>
                                @endif
                                <label for="photo" class="btn-edit-photo shadow-sm">
                                    <i class="fas fa-pencil-alt"></i>
                                </label>
                            </div>
                            <h3 class="mt-4 fw-bold text-dark">{{ Auth::user()->name }}</h3>
                        </div>
                    </div>
                    <div class="col-lg-8 ps-lg-5">
                        <form action="{{ route('user-profile-information.update') }}" method="POST" enctype="multipart/form-data" id="profileForm">
                            @csrf
                            @method('PUT')
                            <input type="file" id="photo" name="photo" class="d-none">
                            <div class="row">
                                <div class="col-md-12 mb-4">
                                    <label class="small fw-bold text-muted mb-2 ms-2">Name :</label>
                                    <input type="text" name="name" class="form-control custom-input" value="{{ old('name', Auth::user()->name) }}" required>
                                </div>
                                <div class="col-md-12 mb-4">
                                    <label class="small fw-bold text-muted mb-2 ms-2">Email :</label>
                                    <input type="email" name="email" class="form-control custom-input" value="{{ old('email', Auth::user()->email) }}" required>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <label class="small fw-bold text-muted mb-2 ms-2">No.Tlp :</label>
                                    <input type="text" name="no_tlp" class="form-control custom-input"
                                        value="{{ old('no_tlp', Auth::user()->no_tlp) }}" placeholder="+62..." required>
                                </div>
                                <div class="col-md-12 mb-4">
                                    <label class="small fw-bold text-muted mb-2 ms-2">Alamat :</label>
                                    <div class="position-relative">
                                        <textarea name="address" class="form-control custom-input" rows="3"
                                                placeholder="Insert Your Address" required>{{ old('address', Auth::user()->address) }}</textarea>
                                        <i class="fas fa-map-marker-alt show-pass-icon text-muted" style="top: 25px;"></i>
                                    </div>
                                </div>
                            </div>

                            <div class="text-end mt-4">
                                <button type="submit" class="btn btn-warning shadow-sm text-white">
                                    Update Profile
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap');

    body {
        font-family: 'Poppins', sans-serif;
        background-color: #f0f2f9;
    }

    .profile-card {
        border-radius: 40px;
        background: #ffffff;
        min-height: 600px;
    }

    .text-purple { color: #7d5fff; }

    .main-avatar {
        width: 180px; height: 180px;
        object-fit: cover;
        border: 6px solid #f3f0ff;
    }

    .empty-avatar {
        width: 180px; height: 180px;
        background: #e0d4ff;
        border: 6px solid #f3f0ff;
        color: #7d5fff;
    }

    .btn-edit-photo {
        position: absolute; bottom: 10px; right: 10px;
        width: 45px; height: 45px;
        background: white; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        cursor: pointer; border: 1px solid #ddd;
    }

    .custom-input {
        background-color: #f4f6ff !important;
        border: none !important;
        border-radius: 20px !important;
        padding: 15px 20px !important;
        font-size: 1rem;
    }

    .custom-input:focus {
        box-shadow: 0 0 0 0.2rem rgba(125, 95, 255, 0.25) !important;
    }

    .show-pass-icon {
        position: absolute; top: 50%; right: 20px;
        transform: translateY(-50%);
        color: #aaa; cursor: pointer;
    }

    .btn-purple {
        background-color: #7d5fff;
        color: white;
        transition: 0.3s;
    }

    .btn-purple:hover {
        background-color: #6a49f2;
        color: white;
        transform: scale(1.02);
    }

    @media (min-width: 992px) {
        .border-end-lg { border-right: 1px solid #eee; }
    }
</style>

{{-- Integrasi SweetAlert2 --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    @if(session('error') || $errors->any())
        Swal.fire({
            icon: 'error',
            title: 'Gagal Memperbarui Profil',
            text: "{{ session('error') ?? 'Pastikan seluruh kolom diisi dengan benar dan tidak kosong.' }}",
            confirmButtonColor: '#dc3545'
        });
    @endif
    @if(session('success') || session('status') == 'profile-information-updated')
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: 'Data profil Anda telah diperbarui.',
            timer: 2000,
            showConfirmButton: false
        });
    @endif
    document.getElementById('profileForm').addEventListener('submit', function() {
        Swal.fire({
            title: 'Menyimpan Perubahan...',
            text: 'Mohon tunggu sebentar.',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
    });
</script>
@endsection
