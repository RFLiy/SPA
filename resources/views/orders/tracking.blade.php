@extends('layouts.app')

@section('content')
    @php
        $progress = 0;
        if ($order->status === 'paid') $progress = 5;
        if ($order->status === 'processing') $progress = 33;
        if ($order->status === 'shipped') $progress = 66;
        if (in_array($order->status, ['delivered', 'finished'])) $progress = 100;
    @endphp

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-end gap-3 mb-4">
                <div>
                    <h3 class="fw-bold text-dark mb-1">Lacak Pesanan</h3>
                    <p class="text-muted small mb-0">Order ID: <span class="fw-bold text-primary">#{{ $order->order_code }}</span></p>
                </div>
                <a href="{{ route('orders.show', $order->id) }}" class="btn btn-warning border rounded-2 px-4 shadow-sm small w-100-mobile text-center text-light">
                    <i class="fas fa-arrow-left me-1"></i> Kembali ke Detail
                </a>
            </div>

            <div class="card shadow-sm border-0 rounded-4 bg-white overflow-hidden">
                <div class="card-body p-3 p-sm-4 bg-transparent">
                    <div class="horizontal-tracking mb-4">
                        <div class="steps d-flex justify-content-between" style="--progress: {{ $progress }}%;">
                            <div class="step-item {{ in_array($order->status, ['paid', 'processing', 'shipped', 'delivered', 'finished']) ? 'active' : '' }}">
                                <div class="step-icon"><i class="fas fa-wallet"></i></div>
                                <div class="step-content-wrapper">
                                    <div class="step-label">Lunas</div>
                                    @if($order->paid_at)
                                        <div class="step-date">{{ \Carbon\Carbon::parse($order->paid_at)->format('d M Y') }}</div>
                                    @endif
                                </div>
                            </div>
                            <div class="step-item {{ in_array($order->status, ['processing', 'shipped', 'delivered', 'finished']) ? 'active' : '' }}">
                                <div class="step-icon"><i class="fas fa-box"></i></div>
                                <div class="step-content-wrapper">
                                    <div class="step-label">Diproses</div>
                                </div>
                            </div>
                            <div class="step-item {{ in_array($order->status, ['shipped', 'delivered', 'finished']) ? 'active' : '' }}">
                                <div class="step-icon"><i class="fas fa-truck"></i></div>
                                <div class="step-content-wrapper">
                                    <div class="step-label">Dikirim</div>
                                </div>
                            </div>
                            <div class="step-item {{ in_array($order->status, ['delivered', 'finished']) ? 'active' : '' }}">
                                <div class="step-icon"><i class="fas fa-check-double"></i></div>
                                <div class="step-content-wrapper">
                                    <div class="step-label">Selesai</div>
                                    @if($order->finished_at)
                                        <div class="step-date">{{ \Carbon\Carbon::parse($order->finished_at)->format('d M Y') }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row g-4">
                        <div class="col-md-7">
                            <div class="p-3 p-sm-4 rounded-4 bg-light border-0 h-100">
                                <h6 class="fw-bold text-dark mb-3"><i class="fas fa-info-circle me-2 text-primary"></i>Ringkasan Pesanan</h6>
                                <div class="mb-4">
                                    @if($order->rfq)
                                        <span class="badge bg-primary px-3 rounded-pill mb-2">PESANAN CUSTOM (RFQ)</span>
                                        <p class="small text-dark mb-1 fw-bold">Kebutuhan Custom:</p>
                                        <p class="small text-muted italic mb-0">"{{ $order->rfq->description }}"</p>
                                    @else
                                        <span class="badge bg-secondary px-3 rounded-pill mb-2">PESANAN REGULER</span>
                                        <p class="small text-muted mb-0">Pembelian produk melalui katalog toko.</p>
                                    @endif
                                </div>

                                <hr class="my-3 opacity-25">

                                <h6 class="fw-bold text-dark mb-3"><i class="fas fa-shipping-fast me-2 text-primary"></i>Informasi Logistik</h6>
                                @if(in_array($order->status, ['shipped', 'delivered', 'finished']))
                                    @if($order->shipping_option)
                                        <div class="mb-3">
                                            <span class="badge bg-soft-success text-success px-3 rounded-pill mb-2">ARMADA INTERNAL</span>
                                            <p class="small text-muted mb-0">Pesanan dikirim langsung oleh tim logistik kami.</p>
                                        </div>
                                        <table class="table table-sm table-borderless mb-0 small logistic-table">
                                            <tr><td class="text-muted" width="40%">Nama Driver</td><td class="fw-bold">: {{ $order->courier_name ?? 'Staff Logistik' }}</td></tr>
                                            <tr><td class="text-muted">No. Surat Jalan</td><td class="fw-bold text-primary text-break">: {{ $order->shipping_reference }}</td></tr>
                                            <tr><td class="text-muted">Estimasi Tiba</td><td class="fw-bold text-dark">: {{ $order->estimated_arrival ? \Carbon\Carbon::parse($order->estimated_arrival)->format('d M Y') : '-' }}</td></tr>
                                        </table>
                                        <div class="mt-4">
                                            <a href="{{ route('orders.download-sj', $order->id) }}" target="_blank" class="btn btn-warning btn-sm w-100 rounded-pill shadow-sm">
                                                <i class="fas fa-file-pdf me-1"></i> Lihat Surat Jalan (PDF)
                                            </a>
                                        </div>
                                    @else
                                        <div class="mb-3">
                                            <span class="badge bg-soft-info text-info px-3 rounded-pill mb-2">EKSPEDISI PIHAK KETIGA</span>
                                        </div>
                                        <table class="table table-sm table-borderless mb-0 small logistic-table">
                                            <tr><td class="text-muted" width="40%">Nama Kurir</td><td class="fw-bold">: {{ $order->courier_name }}</td></tr>
                                            <tr><td class="text-muted">No. Resi (AWB)</td><td class="fw-bold text-primary text-break">: {{ $order->shipping_reference }}</td></tr>
                                            <tr><td class="text-muted">Status Kurir</td><td class="fw-bold text-success">: Sedang Transit</td></tr>
                                        </table>
                                    @endif
                                @else
                                    <div class="text-center py-4">
                                        <div class="bg-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3 shadow-sm" style="width:60px; height:60px;">
                                            <i class="fas fa-clock text-warning fs-4"></i>
                                        </div>
                                        <p class="text-muted small italic px-3 mb-0">Detail logistik akan diperbarui otomatis setelah admin memproses pengiriman barang Anda.</p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-5">
                            <div class="p-3 p-sm-4 rounded-4 border bg-white h-100 shadow-sm">
                                <h6 class="fw-bold text-dark mb-3"><i class="fas fa-map-marker-alt me-2 text-danger"></i>Alamat Pengiriman</h6>
                                <div class="bg-light p-3 rounded-3 mb-4">
                                    <p class="small text-muted mb-0 lh-base text-break">
                                        {{ $order->shipping_address ?? ($order->user->address ?? 'Alamat belum diatur') }}
                                    </p>
                                </div>

                                @if($order->status === 'shipped')
                                    <form action="{{ route('orders.finish', $order->id) }}" method="POST" id="finish-order-form">
                                        @csrf
                                        <button type="button" class="btn btn-success w-100 fw-bold rounded-pill py-2 shadow btn-finish-confirm">
                                            KONFIRMASI DITERIMA
                                        </button>
                                    </form>
                                    <div class="text-center mt-3">
                                        <small class="text-muted italic" style="font-size: 0.7rem;">
                                            <i class="fas fa-exclamation-circle me-1"></i> Klik jika barang sudah sampai tujuan.
                                        </small>
                                    </div>
                                @elseif($order->status === 'finished' || $order->status === 'delivered')
                                    <div class="alert alert-success border-0 rounded-pill py-2 text-center small mb-0">
                                        <i class="fas fa-check-circle me-1"></i> Pesanan telah selesai
                                    </div>
                                @endif
                                <div class="d-flex align-items-center justify-content-between p-2 mt-3 bg-light border shadow-sm rounded">
                                    <span class="small text-muted fst-italic d-none d-sm-block ms-2" style="font-size: 0.75rem;">Hubungi admin jika ada kendala</span>
                                    <span class="small text-muted fst-italic d-block d-sm-none ms-2" style="font-size: 0.7rem;">Hubungi Admin</span>
                                    <a href="https://wa.me/628123456789?text=Halo Admin, saya ingin bertanya tentang pesanan #{{ $order->order_code }}"
                                    target="_blank" class="btn btn-success btn-sm rounded-circle shadow-sm me-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;" title="Chat WhatsApp">
                                        <i class="fab fa-whatsapp"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .horizontal-tracking {
        position: relative;
        padding: 10px 0;
    }

    .steps {
        position: relative;
        display: flex;
        justify-content: space-between;
        z-index: 1;
        background-image: linear-gradient(to right, #0d6efd var(--progress), transparent var(--progress));
        background-size: 100% 6px;
        background-repeat: no-repeat;
        background-position: 0 25px;
    }

    .steps::before {
        content: "";
        position: absolute;
        top: 25px;
        left: 0;
        right: 0;
        height: 6px;
        background: #f1f5f9;
        z-index: -1;
        border-radius: 10px;
    }

    .step-item {
        text-align: center;
        flex: 1;
        position: relative;
    }

    .step-icon {
        width: 56px;
        height: 56px;
        background: #fff;
        border: 4px solid #f1f5f9;
        border-radius: 50%;
        line-height: 48px;
        margin: 0 auto 12px;
        color: #cbd5e1;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        font-size: 1.2rem;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .step-label {
        font-size: 0.75rem;
        font-weight: 800;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .step-date {
        font-size: 0.7rem;
        color: #64748b;
        margin-top: 4px;
    }

    .step-item.active .step-icon {
        background: #0d6efd;
        border-color: #fff;
        color: #fff;
        box-shadow: 0 10px 15px -3px rgba(13, 110, 253, 0.4);
        transform: scale(1.1);
    }

    .step-item.active .step-label {
        color: #0d6efd;
    }

    .bg-soft-success { background-color: #ecfdf5; }
    .bg-soft-info    { background-color: #f0f9ff; }
    .card            { background-color: #ffffff !important; }

    @media (max-width: 576px) {
        .w-100-mobile {
            width: 100% !important;
        }

        .steps {
            flex-direction: column;
            align-items: flex-start;
            gap: 24px;
            padding-left: 20px;
            background-image: none !important;
        }

        .steps::before {
            width: 6px;
            height: calc(100% - 40px);
            top: 20px;
            left: 45px;
            right: auto;
        }

        .steps::after {
            content: "";
            position: absolute;
            width: 6px;
            height: calc(var(--progress));
            max-height: calc(100% - 40px);
            top: 20px;
            left: 45px;
            background: #0d6efd;
            z-index: -1;
            border-radius: 10px;
        }

        .step-item {
            display: flex;
            align-items: center;
            gap: 15px;
            text-align: left;
            width: 100%;
        }

        .step-icon {
            margin: 0 !important;
            width: 48px !important;
            height: 48px !important;
            font-size: 1rem;
            flex-shrink: 0;
            border-width: 3px !important;
        }

        .step-content-wrapper {
            display: flex;
            flex-direction: column;
        }

        .step-label {
            font-size: 0.7rem;
        }

        .step-date {
            margin-top: 2px;
        }

        .logistic-table tr {
            display: flex;
            flex-direction: column;
            margin-bottom: 8px;
        }

        .logistic-table td {
            width: 100% !important;
            padding: 0 !important;
        }
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const finishBtn = document.querySelector('.btn-finish-confirm');
        if(finishBtn) {
            finishBtn.addEventListener('click', function() {
                Swal.fire({
                    title: 'Konfirmasi Penerimaan',
                    text: "Pastikan semua produk telah Anda terima dalam kondisi baik.",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#198754',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Sudah Diterima',
                    cancelButtonText: 'Batal',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('finish-order-form').submit();
                    }
                });
            });
        }
    });
</script>
@endsection
