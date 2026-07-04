@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
        <div>
            <h3 class="fw-bold text-dark mb-1">Keranjang Belanja</h3>
        </div>
        <div class="d-none d-sm-block">
            <a href="{{ route('products.index') }}" class="btn btn-warning text-white border px-4 shadow-sm btn-back">
                <i class="fas fa-arrow-left me-1"></i> Lihat Lainnya
            </a>
        </div>
    </div>

    @if($cartItems->isEmpty())
        <div class="card border-0 shadow-lg text-center py-5">
            <div class="card-body bg-transparent">
                <i class="fas fa-shopping-basket fa-4x text-warning mb-3"></i>
                <h5 class="text-muted">Keranjang Anda masih kosong</h5>
                <a href="{{ route('products.index') }}" class="btn btn-warning btn-sm mt-2 text-white rounded-pill px-4">Cari Produk</a>
            </div>
        </div>
    @else
    <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 responsive-table">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 py-3 text-uppercase small fw-bold text-dark">Produk</th>
                        <th class="py-3 text-uppercase small fw-bold text-dark text-center">Jumlah</th>
                        <th class="py-3 text-uppercase small fw-bold text-dark text-end">Harga Satuan</th>
                        <th class="py-3 text-uppercase small fw-bold text-dark text-end">Subtotal</th>
                        <th class="py-3 text-uppercase small fw-bold text-dark text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($cartItems as $item)
                    <tr>
                        <td class="ps-4" data-label="Produk">
                            <div class="d-flex align-items-center py-2 product-cell">
                                <div class="product-img-wrapper">
                                @if($item->product->image)
                                    <img src="{{ Storage::url($item->product->image) }}"
                                        class="rounded-3 shadow-sm border product-img"
                                        alt="{{ $item->product->name }}">
                                @else
                                    <div class="d-flex align-items-center justify-content-center bg-light rounded-3 border product-img">
                                        <i class="fas fa-image text-muted"></i>
                                    </div>
                                @endif
                                </div>
                                <div class="product-details">
                                    <div class="fw-bold text-dark text-uppercase small product-name">
                                        {{ $item->product->name }}
                                    </div>
                                    <small class="text-muted product-sku">SKU: {{ $item->product->id ?? 'N/A' }}</small>
                                </div>
                            </div>
                        </td>
                        <td class="text-center" data-label="Jumlah">
                            <div class="d-inline-flex align-items-center bg-light rounded-pill p-1">
                                <form method="POST" action="{{ route('cart.decrement', $item) }}">
                                    @csrf
                                    <button class="btn btn-white btn-sm rounded-circle shadow-sm border-0" style="width: 30px; height: 30px;">−</button>
                                </form>
                                <span class="mx-3 fw-bold small">{{ $item->quantity }}</span>
                                <form method="POST" action="{{ route('cart.increment', $item) }}">
                                    @csrf
                                    <button class="btn btn-white btn-sm rounded-circle shadow-sm border-0" style="width: 30px; height: 30px;">+</button>
                                </form>
                            </div>
                        </td>
                        <td class="text-end text-muted small fw-medium" data-label="Harga Satuan">
                            Rp {{ number_format($item->product->base_price, 0, ',', '.') }}
                        </td>
                        <td class="text-end" data-label="Subtotal">
                            <span class="fw-bold text-primary">
                                Rp {{ number_format($item->quantity * $item->product->base_price, 0, ',', '.') }}
                            </span>
                        </td>
                        <td class="text-center" data-label="Aksi">
                            <form method="POST" action="{{ route('cart.remove', $item) }}" class="delete-form">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn btn-soft-danger btn-sm rounded-circle btn-remove" title="Hapus">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="row justify-content-end mt-4">
        <div class="col-md-5 col-lg-5">
            <div class="card border-0 shadow-lg rounded-4">
                <div class="card-body p-4 bg-transparent">
                    <h6 class="fw-bold text-dark mb-3">Ringkasan Belanja</h6>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted small">Total Item</span>
                        <span class="fw-medium small">{{ $cartItems->sum('quantity') }} Unit</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted fw-bold">Total Harga</span>
                        <span class="fw-bold text-success fs-5 total-price">
                            Rp {{ number_format($cartItems->sum(fn($i) => $i->quantity * $i->product->base_price),0, ',', '.') }}
                        </span>
                    </div>
                    <hr class="my-3 opacity-50">
                    <a href="{{ route('checkout.index') }}"
                        class="btn btn-primary w-100 rounded-pill py-2 fw-bold shadow">
                        Lanjut ke Checkout <i class="fas fa-chevron-right ms-2"></i>
                    </a>
                    <div class="d-block d-sm-none mt-2">
                        <a href="{{ route('products.index') }}" class="btn btn-warning text-white w-100 rounded-pill py-2 fw-bold shadow-sm">
                            <i class="fas fa-arrow-left me-1"></i> Lihat Lainnya
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<style>
    .bg-light {
        background-color: #f8fafc !important;
    }

    .btn-soft-danger {
        background-color: #fef2f2;
        color: #dc3545;
        border: none;
        width: 35px;
        height: 35px;
        transition: all 0.2s;
    }

    .btn-soft-danger:hover {
        background-color: #dc3545;
        color: white;
    }

    .btn-white {
        background-color: white;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .table thead th {
        font-size: 11px;
        letter-spacing: 0.05em;
        border-bottom: none;
    }

    .table tbody tr {
        border-bottom: 1px solid #f1f5f9;
        transition: all 0.2s;
    }

    .table tbody tr:hover {
        background-color: #fcfcfc;
    }

    .product-cell {
        display: flex;
        align-items: center;
        width: 100%;
    }

    .product-img-wrapper {
        margin-right: 15px;
    }

    .product-img {
        width: 70px;
        height: 70px;
        object-fit: cover;
    }

    @media (max-width: 480px) {
        .responsive-table thead {
            display: none;
        }

        .responsive-table tbody tr {
            display: block;
            padding: 15px 10px;
            border-bottom: 3px solid #e2e8f0;
        }

        .responsive-table tbody td {
            display: flex;
            justify-content: space-between;
            align-items: center;
            text-align: right;
            padding: 8px 10px !important;
            border: none !important;
            font-size: 13px;
        }

        .responsive-table tbody td:first-child {
            display: block;
            text-align: left;
            border-bottom: 1px dashed #e2e8f0 !important;
            margin-bottom: 10px;
            padding-bottom: 12px !important;
        }

        .product-cell {
            flex-direction: column;
            align-items: flex-start !important;
            width: 100% !important;
        }

        .product-img-wrapper {
            width: 100% !important;
            margin-right: 0 !important;
            margin-bottom: 15px;
        }

        .product-img {
            width: 100% !important;
            height: auto !important;
            aspect-ratio: 16 / 9;
            object-fit: cover;
        }

        .product-details {
            width: 100%;
            text-align: left;
        }

        .product-name {
            font-size: 14px;
            font-weight: bold;
            white-space: normal;
            display: block;
            margin-bottom: 4px;
        }

        .product-sku {
            font-size: 12px;
            display: block;
        }

        .responsive-table tbody td::before {
            content: attr(data-label);
            font-weight: 700;
            text-transform: uppercase;
            font-size: 11px;
            color: #64748b;
            text-align: left;
            margin-right: 10px;
        }

        .responsive-table tbody td:first-child::before {
            content: "";
        }

        .total-price {
            font-size: 1rem !important;
        }
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.querySelectorAll('.btn-remove').forEach(button => {
        button.addEventListener('click', function() {
            const form = this.closest('.delete-form');
            Swal.fire({
                title: 'Hapus Item?',
                text: "Produk ini akan dihapus dari keranjang belanja Anda.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                customClass: {
                    confirmButton: 'rounded-pill px-4',
                    cancelButton: 'rounded-pill px-4'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
</script>
@endsection
