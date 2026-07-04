@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
            <div>
                <h3 class="fw-bold text-dark mb-0">Pesanan Saya</h3>
            </div>
                <a href="{{ url('/products') }}" class="btn btn-warning shadow-sm text-white btn-back">
                    <i class="fas fa-shopping-cart me-1 text-white"></i> Belanja
                </a>
            </div>
        </div>

        @if ($orders->isEmpty())
            <div class="card border-0 shadow-lg text-center py-5">
                <div class="card-body bg-transparent">
                    <i class="fas fa-box-open fa-4x text-warning mb-3"></i>
                    <h5 class="text-dark">Belum ada pesanan ditemukan</h5>
                    <a href="{{ url('/products') }}" class="btn btn-outline-warning btn-sm mt-2 px-4">Mulai Belanja</a>
                </div>
            </div>
        @else
            <div class="card border-0 shadow-lg rounded-4 bg-white" style="overflow: visible !important;">
                <div class="table-responsive" style="overflow: visible !important;">
                    <table class="table table-hover align-middle mb-0 text-center border-0 responsive-table">
                        <thead>
                            <tr class="bg-light">
                                <th class="py-3 text-dark small fs-6 fw-bold">No</th>
                                <th class="py-3 text-dark small fs-6 fw-bold">ORDER ID</th>
                                <th class="py-3 text-dark small fs-6 fw-bold">JENIS PRODUK</th>
                                <th class="py-3 text-dark small fs-6 fw-bold">TOTAL ITEM</th>
                                <th class="py-3 text-dark small fs-6 fw-bold">TOTAL</th>
                                <th class="py-3 text-dark small fs-6 fw-bold">STATUS</th>
                                <th class="py-3 text-dark small fs-6 fw-bold">AKSI</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($orders as $order)
                                <tr>
                                    <td class="text-dark fw-bold" data-label="No">
                                        {{ $loop->iteration }}
                                    </td>
                                    <td class="fw-bold text-dark" data-label="Order ID">
                                        #{{ $order->order_code }}
                                        <div class="text-dark fw-normal order-date" style="font-size: 0.75rem;">
                                            {{ $order->created_at->format('d/m/Y') }}
                                        </div>
                                    </td>
                                    <td data-label="Jenis Produk">
                                        <span class="text-dark fw-bold">{{ $order->items->count() }} Jenis</span>
                                    </td>
                                    <td data-label="Total Item">
                                        <span class="text-dark fw-bold">{{ $order->items->sum('quantity') }} Pcs</span>
                                    </td>
                                    <td data-label="Total">
                                        <span class="fw-bold text-dark">
                                            Rp {{ number_format($order->total, 0, ',', '.') }}
                                        </span>
                                    </td>
                                    <td data-label="Status">
                                        @php
                                            $statusClasses = [
                                                'waiting_payment' => 'bg-soft-warning text-warning',
                                                'processing' => 'bg-soft-info text-info',
                                                'shipped' => 'bg-soft-primary text-primary',
                                                'completed' => 'bg-soft-success text-success',
                                                'cancelled' => 'bg-soft-danger text-danger',
                                                'paid' => 'bg-soft-success text-success',
                                            ];
                                            $class = $statusClasses[$order->status] ?? 'bg-soft-secondary text-secondary';
                                        @endphp
                                        <span class="badge {{ $class }} px-3 py-2 fw-medium">
                                            {{ strtoupper(str_replace('_', ' ', $order->status)) }}
                                        </span>
                                    </td>
                                    <td data-label="Aksi">
                                        <div class="d-flex justify-content-center flex-wrap gap-2 action-container">
                                            @if (in_array($order->status, ['waiting_payment']))
                                                <a href="{{ route('orders.show', $order->id) }}"
                                                    class="btn btn-outline-success btn-sm shadow-sm btn-action">Pay</a>
                                            @endif
                                            <a href="{{ route('orders.show', $order->id) }}"
                                                class="btn btn-outline-primary btn-sm shadow-sm btn-action">
                                                Detail
                                            </a>

                                            @if (in_array($order->status, ['waiting_payment']))
                                                <form action="{{ route('orders.cancel', $order->id) }}" method="POST"
                                                    class="cancel-form d-inline">
                                                    @csrf
                                                    <button type="button"
                                                        class="btn btn-outline-danger btn-sm btn-cancel-order btn-action">
                                                        Cancel
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>

    <style>
        .bg-soft-info { background-color: #e0f2fe; }
        .bg-soft-success { background-color: #ecfdf5; }
        .bg-soft-danger { background-color: #fef2f2; }
        .bg-soft-warning { background-color: #fffbeb; }
        .bg-soft-primary { background-color: #eef2ff; }
        .bg-soft-secondary { background-color: #f8fafc; }

        .table thead th {
            font-size: 12px;
            letter-spacing: 0.05rem;
            border-bottom: 1px solid #f1f5f9 !important;
        }

        .table tbody tr {
            border-bottom: 1px solid #f1f5f9;
            transition: all 0.2s;
        }

        .table tbody tr:last-child td {
            border-bottom: none !important;
        }

        .table tbody tr:hover {
            background-color: #fcfcfc;
        }

        .table-responsive {
            overflow: visible !important;
            padding-bottom: 30px;
        }

        @media (max-width: 576px) {
            .btn-back {
                width: 100%;
                text-align: center;
            }

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
                padding: 10px !important;
                border: none !important;
                font-size: 13px;
                width: 100%;
            }

            .responsive-table tbody td:not(:last-child) {
                border-bottom: 1px dashed #f1f5f9 !important;
            }

            .responsive-table tbody td::before {
                content: attr(data-label);
                font-weight: 700;
                text-transform: uppercase;
                font-size: 11px;
                color: #64748b;
                text-align: left;
                margin-right: 15px;
            }

            .order-date {
                display: inline-block;
                margin-left: 5px;
            }

            .action-container {
                width: 100%;
                justify-content: flex-end !important;
            }

            .btn-action {
                flex: 1 1 auto;
                min-width: 65px;
                text-align: center;
            }

            .cancel-form {
                display: flex !important;
                flex: 1 1 auto;
            }
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.querySelectorAll('.btn-cancel-order').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const form = this.closest('.cancel-form');

                Swal.fire({
                    title: 'Batalkan Pesanan?',
                    text: "Pesanan yang dibatalkan tidak bisa dipulihkan.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Batalkan!',
                    cancelButtonText: 'Tutup',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    </script>
@endsection
