@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4 header-detail">
        <div>
            <h3 class="fw-bold text-dark mb-1 h3-title">Detail Pesanan <span class="text-warning d-none d-sm-inline">#{{ $order->order_code }}</span></h3>
            <span class="text-warning fw-bold d-block d-sm-none fs-5 mb-2">#{{ $order->order_code }}</span>
            <p class="text-dark small mb-0">Dipesan pada {{ $order->created_at->format('d M Y, H:i') }}</p>
        </div>
        @php
            $statusClasses = [
                'waiting_payment' => 'bg-soft-warning text-warning',
                'paid'            => 'bg-soft-success text-success',
                'processing'      => 'bg-soft-info text-info',
                'shipped'         => 'bg-soft-primary text-primary',
                'completed'       => 'bg-soft-success text-success',
                'cancelled'       => 'bg-soft-danger text-danger',
            ];
            $class = $statusClasses[$order->status] ?? 'bg-soft-secondary text-secondary';
        @endphp
        <span class="badge {{ $class }} px-4 py-3 fw-bold shadow-sm status-badge">
            <i class="fas fa-circle me-1 small"></i> {{ strtoupper(str_replace('_', ' ', $order->status)) }}
        </span>
    </div>

    @if($order->status !== 'cancelled')
        <div class="card border-0 shadow-lg rounded-4 overflow-hidden mb-4">
            <div class="card-body p-4 bg-white">
                <div class="row g-4 info-row">
                    <div class="col-md-6 border-end info-column">
                        <h6 class="fw-bold text-uppercase text-dark mb-3" style="font-size: 0.75rem; letter-spacing: 1px;">Informasi Transaksi</h6>
                        <table class="table table-borderless table-sm mb-0 info-table">
                            <tr>
                                <td class="text-dark ps-0" width="40%">Metode Pembayaran</td>
                                <td class="fw-bold text-dark">: {{ ucfirst($order->payment_method ?? 'Midtrans') }}</td>
                            </tr>
                            <tr>
                                <td class="text-dark ps-0">Status Pembayaran</td>
                                <td class="fw-bold">
                                    <span class="{{ $order->payment_status === 'paid' ? 'text-success' : 'text-warning' }}">
                                        : {{ strtoupper($order->payment_status) }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-dark ps-0">Total Pembayaran</td>
                                <td class="fw-bold text-success">: Rp.{{ number_format($order->total, 0, ',', '.') }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6 info-column">
                        <h6 class="fw-bold text-uppercase text-dark mb-3" style="font-size: 0.75rem; letter-spacing: 1px;">Detail Pengiriman</h6>
                        <table class="table table-borderless table-sm mb-0 info-table">
                            <tr>
                                <td class="text-dark ps-0" width="40%">Opsi Pengiriman</td>
                                <td class="fw-bold text-dark">: {{ $order->shipping_option === 'internal' ? 'Kurir Internal' : 'Ekspedisi Luar' }}</td>
                            </tr>
                            <tr>
                                <td class="text-dark ps-0">Kurir / Vendor</td>
                                <td class="fw-bold text-dark">: {{ $order->courier_name ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="text-dark ps-0">Nomor Lacak/Resi</td>
                                <td class="fw-bold text-primary">: {{ $order->shipping_reference ?? 'Belum Tersedia' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="card border-0 shadow-lg rounded-4 overflow-hidden mb-4 bg-white">
        <div class="card-body p-0">
            <div class="p-4 border-bottom bg-light">
                <h6 class="fw-bold mb-0 text-dark">Item Pesanan</h6>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 detail-table">
                    <thead class="bg-light">
                        <tr class="text-dark small fw-bold">
                            <th class="ps-4 py-3 border-0">PRODUK</th>
                            <th class="py-3 border-0 text-center">QUANTITY</th>
                            <th class="py-3 border-0 text-end">HARGA SATUAN</th>
                            <th class="py-3 border-0 text-end pe-4">SUBTOTAL</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->items as $item)
                            <tr>
                                <td class="ps-4" data-label="Produk">
                                    <div class="d-flex align-items-center py-1 item-cell">
                                        <div class="bg-light rounded p-1 item-img-wrapper">
                                            @if ($item->product_image)
                                                <img src="{{ Storage::url($item->product_image) }}" alt="Product"
                                                    class="rounded item-img"
                                                    style="width: 45px; height: 45px; object-fit: cover;">
                                            @elseif ($item->product && $item->product->image)
                                                <img src="{{ Storage::url($item->product->image) }}" alt="Product"
                                                    class="rounded item-img"
                                                    style="width: 45px; height: 45px; object-fit: cover;">
                                            @else
                                                <div class="d-flex align-items-center justify-content-center bg-white rounded item-img"
                                                    style="width: 45px; height: 45px;">
                                                    <i class="fas fa-box text-dark"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <span class="fw-bold text-dark small text-uppercase item-name">
                                            {{ $item->product_name ?? ($item->product->name ?? 'Produk Tidak Tersedia') }}
                                        </span>
                                    </div>
                                </td>
                                <td class="text-center fw-medium" data-label="Quantity">{{ $item->quantity }}</td>
                                <td class="text-end text-dark" data-label="Harga Satuan">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                                <td class="text-end pe-4 fw-bold text-dark" data-label="Subtotal">Rp {{ number_format($item->quantity * $item->price, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-light border-top detail-tfoot">
                        <tr class="tfoot-row">
                            <td colspan="3" class="text-end fw-bold py-2 tfoot-label">Subtotal Produk</td>
                            <td class="text-end pe-4 py-2 tfoot-value">
                                Rp {{ number_format($order->items->sum(fn($i) => $i->quantity * $i->price), 0, ',', '.') }}
                            </td>
                        </tr>
                        <tr class="table-warning tfoot-row main-total-row">
                            <td colspan="3" class="text-end fw-bold py-3 text-dark tfoot-label">TOTAL PEMBAYARAN</td>
                            <td class="text-end pe-4 py-3 fw-bold text-success main-total-value" style="font-size: 1.1rem;">
                                Rp {{ number_format($order->total, 0, ',', '.') }}
                            </td>
                        </tr>
                    </footer>
                </table>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center flex-column flex-sm-row gap-3 mt-4 footer-actions">
        <a href="{{ route('orders.index') }}" class="btn btn-warning text-light px-4 border shadow-sm w-100-mobile order-1">
            <i class="fas fa-arrow-left me-1"></i> Kembali ke Riwayat
        </a>

        <div class="d-flex gap-2 w-100-mobile flex-column flex-sm-row order-0 order-sm-2">
            @if($order->status === 'waiting_payment' && $order->payment_status === 'pending' && !empty($snapToken))
                <button id="pay-button" class="btn btn-success rounded-pill px-5 py-2 fw-bold shadow w-100-mobile">
                    Bayar Sekarang <i class="fas fa-credit-card ms-1"></i>
                </button>
            @endif

            @if(in_array($order->status, ['shipped', 'delivered','processing']))
                <a href="{{ route('orders.tracking', $order->id) }}" class="btn btn-outline-primary px-4 py-2 fw-bold shadow-sm rounded-pill w-100-mobile text-center">
                    Lacak <i class="fas fa-map-marker-alt ms-1"></i>
                </a>
            @endif

            @if(in_array($order->status, ['shipped']))
                <form action="{{ route('orders.finish', $order->id) }}" method="POST" id="form-selesai-pesanan" class="d-inline w-100-mobile">
                    @csrf
                    <button type="button" class="btn btn-primary px-5 py-2 fw-bold shadow rounded-pill w-100-mobile" id="btn-klik-selesai">
                        Pesanan Selesai <i class="fas fa-check-circle ms-1"></i>
                    </button>
                </form>
            @endif
        </div>
    </div>
</div>

<style>
    .bg-soft-info { background-color: #e0f2fe; }
    .bg-soft-success { background-color: #ecfdf5; }
    .bg-soft-danger { background-color: #fef2f2; }
    .bg-soft-warning { background-color: #fffbeb; }
    .bg-soft-primary { background-color: #eef2ff; }
    .bg-soft-secondary { background-color: #f8fafc; }

    @media (max-width: 576px) {
        .header-detail {
            flex-direction: column !important;
            align-items: flex-start !important;
        }

        .status-badge {
            width: 100%;
            text-align: center;
        }

        .info-row {
            flex-direction: column;
        }

        .info-column {
            border-end: none !important;
            width: 100%;
        }

        .info-column:first-child {
            border-bottom: 1px dashed #e2e8f0;
            padding-bottom: 20px;
        }

        .info-table tr {
            display: flex;
            flex-direction: column;
            margin-bottom: 8px;
        }

        .info-table td {
            width: 100% !important;
            padding: 0 !important;
        }

        .detail-table thead {
            display: none;
        }

        .detail-table tbody tr {
            display: block;
            padding: 15px 10px;
            border-bottom: 3px solid #e2e8f0;
        }

        .detail-table tbody td {
            display: flex;
            justify-content: space-between;
            align-items: center;
            text-align: right;
            padding: 8px 10px !important;
            border: none !important;
            font-size: 13px;
        }

        .detail-table tbody td:first-child {
            display: block;
            text-align: left;
            border-bottom: 1px dashed #e2e8f0 !important;
            margin-bottom: 10px;
            padding-bottom: 12px !important;
        }

        .item-cell {
            flex-direction: column;
            align-items: flex-start !important;
            width: 100% !important;
        }

        .item-img-wrapper {
            width: 100% !important;
            margin-right: 0 !important;
            margin-bottom: 12px;
            padding: 0 !important;
        }

        .item-img {
            width: 100% !important;
            height: auto !important;
            aspect-ratio: 16 / 9;
            object-fit: cover;
        }

        .item-name {
            font-size: 13px;
            white-space: normal;
            display: block;
        }

        .detail-table tbody td::before {
            content: attr(data-label);
            font-weight: 700;
            text-transform: uppercase;
            font-size: 11px;
            color: #64748b;
            text-align: left;
            margin-right: 10px;
        }

        .detail-table tbody td:first-child::before {
            content: "";
        }

        .detail-tfoot {
            display: block;
            width: 100%;
        }

        .tfoot-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 20px;
            border-bottom: 1px solid #e2e8f0;
        }

        .main-total-row {
            background-color: #fffbeb !important;
        }

        .tfoot-label {
            text-align: left !important;
            padding: 0 !important;
            border: none !important;
        }

        .tfoot-value, .main-total-value {
            text-align: right !important;
            padding: 0 !important;
            border: none !important;
        }

        .footer-actions {
            flex-direction: column-reverse !important;
        }

        .w-100-mobile {
            width: 100% !important;
        }

        .order-1 {
            order: 1 !important;
        }

        .order-2 {
            order: 2 !important;
        }
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@if(!empty($snapToken) && $order->payment_status === 'pending')
<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ env('MIDTRANS_CLIENT_KEY') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const payButton = document.getElementById('pay-button');
            if (payButton) {
                payButton.addEventListener('click', function() {
                    snap.pay("{{ $snapToken }}", {
                        onSuccess: function(result) {
                            fetch("{{ route('orders.updateStatus') }}", {
                                method: "POST",
                                headers: {
                                    "Content-Type": "application/json",
                                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                                },
                                body: JSON.stringify({
                                    order_id: "{{ $order->id }}"
                                })
                            }).then(response => {
                                Swal.fire({
                                    title: 'Pembayaran Berhasil!',
                                    text: 'Pesanan Anda telah dibayar dan stok diperbarui.',
                                    icon: 'success',
                                    showConfirmButton: false,
                                    timer: 2500
                                }).then(() => {
                                    window.location.reload();
                                });
                            });
                        },
                        onPending: function(result) {
                            Swal.fire({
                                title: 'Menunggu Pembayaran',
                                text: 'Silahkan selesaikan pembayaran Anda.',
                                icon: 'info',
                                confirmButtonText: 'OKE'
                            }).then(() => {
                                window.location.reload();
                            });
                        },
                        onError: function(result) {
                            Swal.fire({
                                title: 'Pembayaran Gagal!',
                                text: 'Maaf, terjadi kesalahan pada sistem pembayaran.',
                                icon: 'error',
                                confirmButtonText: 'Coba Lagi'
                            }).then(() => {
                            window.location.reload();
                            });
                        },
                        onClose: function() {
                            window.location.reload();
                        }
                    });
                });
            }
        });
    </script>
@endif

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const btnSelesai = document.getElementById('btn-klik-selesai');
        const formSelesai = document.getElementById('form-selesai-pesanan');

        if (btnSelesai && formSelesai) {
            btnSelesai.addEventListener('click', function(e) {
                e.preventDefault();
                Swal.fire({
                    title: 'Pesanan Selesai?',
                    text: "Konfirmasi bahwa Anda telah menerima produk dengan baik.",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#0d6efd',
                    confirmButtonText: 'Ya, Selesai!',
                    cancelButtonText: 'Batal',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        formSelesai.submit();
                    }
                });
            });
        }
    });
</script>
@endsection
