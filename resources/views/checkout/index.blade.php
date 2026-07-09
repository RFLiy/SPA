@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <div>
            <h3 class="fw-bold text-dark">Checkout</h3>
            <p class="text-muted small">Selesaikan pesanan Anda dengan mengisi informasi pengiriman di bawah ini.</p>
        </div>
        <div class="d-none d-sm-block">
            <a href="{{ route('cart.index') }}" class="btn btn-warning text-white border px-4 shadow-sm">
                <i class="fas fa-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>
    <form method="POST" action="{{ route('checkout.store') }}" id="checkoutForm">
        @csrf
        <div class="row g-4">
            <div class="col-md-6">
                <div class="card border-0 shadow-lg rounded-4 mb-4 bg-white">
                    <div class="card-body p-4 bg-transparent">
                        <div class="d-flex align-items-center mb-4">
                            <div class="bg-light rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 45px; height: 45px;">
                                <i class="fas fa-map-marker-alt text-primary"></i>
                            </div>
                            <h5 class="fw-bold mb-0 text-dark">Informasi Pengiriman</h5>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted small fw-bold text-uppercase">Alamat Lengkap Pengiriman</label>
                            <textarea name="shipping_address" id="shippingAddress"
                                class="form-control border shadow-none bg-white rounded-3 @error('shipping_address') is-invalid @enderror"
                                rows="4"
                                placeholder="Masukkan alamat lengkap (Jalan, No Rumah, Kec, Kota/Kab)" required
                                style="border-color: #e2e8f0 !important;">{{ old('shipping_address', auth()->user()->address) }}</textarea>

                            @error('shipping_address')
                                <div class="invalid-feedback fw-bold">
                                    {{ $message }}
                                </div>
                            @enderror
                            <div class="form-text mt-2 small text-muted">
                                <i class="fas fa-info-circle me-1"></i> Pastikan alamat sudah benar untuk menghindari kesalahan pengiriman.
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card border-0 shadow-lg rounded-4 bg-white">
                    <div class="card-body p-4 bg-transparent">
                        <div class="d-flex align-items-center mb-4">
                            <div class="bg-light rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 45px; height: 45px;">
                                <i class="fas fa-credit-card text-success"></i>
                            </div>
                            <h5 class="fw-bold mb-0 text-dark">Metode Pembayaran</h5>
                        </div>
                        <div class="p-3 border rounded-3 bg-white d-flex align-items-center justify-content-between" style="border-color: #e2e8f0 !important;">
                            <div class="form-check mb-0">
                                <input class="form-check-input shadow-none" type="radio" checked id="midtransRadio">
                                <label class="form-check-label fw-semibold text-dark" for="midtransRadio">
                                    Midtrans Payment Gateway
                                </label>
                                <small class="d-block text-muted">VA, QRIS, GoPay, dan Transfer Bank</small>
                            </div>
                            <i class="fas fa-credit-card text-success me-3"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card border-0 shadow-lg rounded-4 sticky-sm bg-white" style="top: 20px;">
                    <div class="card-body p-4 bg-transparent">
                        <h6 class="fw-bold text-dark mb-4 text-uppercase small border-bottom pb-2">Metode Pengiriman</h6>
                        <div class="row g-3 mb-4">
                            <div class="col-md-12">
                                <input type="hidden" name="shipping_type" value="internal">
                                <div class="card h-100 shadow-lg border p-3 bg-light" style="border-left: 4px solid #ffc107 !important;">
                                    <div class="d-flex align-items-center">
                                        <div class="shipping-icon bg-warning-subtle text-warning rounded-circle me-3">
                                            <i class="fas fa-shipping-fast"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-1 fw-bold">Official Delivery</h6>
                                        </div>
                                        <div class="ms-auto text-success">
                                            <small class="fw-bold">TERPILIH</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            @foreach($cartItems as $item)
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div style="max-width: 65%;">
                                    <span class="d-block fw-bold text-dark small text-uppercase text-break">{{ $item->product->name }}</span>
                                    <small class="text-muted">{{ $item->quantity }} x Rp.{{ number_format($item->product->base_price, 0, ',', '.') }}</small>
                                </div>
                                <span class="fw-bold text-dark small">
                                    Rp.{{ number_format($item->quantity * $item->product->base_price, 0, ',', '.') }}
                                </span>
                            </div>
                            @endforeach
                        </div>
                        <hr class="border-light">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <span class="fw-bold small uppercase">TOTAL TAGIHAN</span>
                            <span class="fs-5 fw-bold text-success">
                                Rp.{{ number_format($cartItems->sum(fn($i) => $i->quantity * $i->product->base_price), 0, ',', '.') }}
                            </span>
                        </div>
                        <button type="submit" class="btn btn-warning text-white w-100 rounded-pill py-3 fw-bold shadow-lg mb-3">
                            Place Order
                        </button>
                        <div class="d-block d-sm-none mb-3">
                            <a href="{{ route('cart.index') }}" class="btn btn-outline-warning w-100 rounded-pill py-2 fw-bold shadow-sm">
                                <i class="fas fa-arrow-left me-1"></i> Kembali ke Keranjang
                            </a>
                        </div>
                        <div class="text-center">
                            <small class="text-muted" style="font-size: 0.7rem;">
                                <i class="fas fa-lock me-1"></i> Pembayaran Terenkripsi & Aman
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<style>
    body {
        background-color: #f8fafc;
    }

    .card {
        background-color: #ffffff !important;
    }

    .form-control:focus, .form-select:focus {
        border-color: #3b82f6 !important;
        background-color: #fff !important;
    }

    .bg-light {
        background-color: #f1f5f9 !important;
    }

    .text-primary {
        color: #0d6efd !important;
    }

    .shipping-card {
        transition: all 0.2s ease-in-out;
        cursor: pointer;
        border-width: 2px !important;
    }

    .shipping-card:hover {
        border-color: #ffc107 !important;
        background-color: #fffdf5;
    }

    .shipping-icon {
        width: 45px;
        height: 45px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
    }

    .check-icon {
        display: none;
    }

    .btn-check:checked+.shipping-card {
        border-color: #ffc107 !important;
        background-color: #fffdf5;
    }

    .btn-check:checked+.shipping-card .check-icon {
        display: block;
    }

    @media (max-width: 576px) {
        .sticky-sm {
            position: relative !important;
            top: 0 !important;
        }
    }
</style>

{{-- Integrasi SweetAlert2 --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    @if(session('error') || $errors->any())
        Swal.fire({
            icon: 'error',
            title: 'Gagal Memproses Order',
            text: "{{ session('error') ?? 'Pastikan seluruh data dan alamat pengiriman diisi dengan benar.' }}",
            confirmButtonColor: '#dc3545'
        });
    @endif
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: "{{ session('success') }}",
            timer: 2000,
            showConfirmButton: false
        });
    @endif
    document.getElementById('checkoutForm').addEventListener('submit', function(e) {
        const addressField = document.getElementById('shippingAddress').value.trim();

        if(addressField === "") {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Alamat Kosong',
                text: 'Silakan isi alamat pengiriman terlebih dahulu!',
                confirmButtonColor: '#ffc107'
            });
            return false;
        }

        Swal.fire({
            title: 'Memproses Pesanan...',
            text: 'Mohon tunggu sebentar, sistem sedang membuat invoice.',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
    });
</script>
@endsection
