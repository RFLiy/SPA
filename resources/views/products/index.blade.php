@extends('layouts.app')

@section('content')
<section class="container py-2">
    <div class="d-flex align-items-center justify-content-between mb-4" data-aos="fade-down">
        <h1 class="fw-bold mb-0">
            Katalog
            <span class="bg-warning text-white px-3 py-1 rounded-3" style="transform: skewX(-8deg); display: inline-block;">
                Produk.
            </span>
        </h1>
    </div>
    <div class="row g-4" data-aos="fade-up">
        @forelse($products as $product)
        <div class="col-md-3">
            <div class="card h-100 shadow-sm product-card border-0 rounded-3">
                <div style="height: 200px; overflow: hidden;" class="rounded-top">
                    <img src="{{ Storage::url($product->image) }}" class="card-img-top h-100 w-100 object-fit-cover" alt="{{ $product->name }}">
                </div>
                <div class="card-body d-flex flex-column bg-transparent shadow-sm">
                    <h5 class="fw-bold text-truncate" title="{{ $product->name }}">{{ $product->name }}</h5>
                    <small class="text-secondary mb-2">
                        {{ Str::limit(strip_tags(html_entity_decode($product->description)), 50) }}
                    </small>
                    <p class="mb-1 small {{ $product->stock <= 5 ? 'text-danger fw-bold' : 'text-muted' }}">
                        Stok: {{ $product->stock > 0 ? $product->stock . ' ' . $product->unit : 'Habis' }}
                    </p>
                    <p class="fw-bold mt-auto mb-3 fs-5">
                        Rp.{{ number_format($product->base_price, 0, ',', '.') }}
                    </p>
                    <div class="d-grid gap-2">
                        <form action="{{ route('cart.add', $product) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-outline-warning w-100 text-black" {{ $product->stock <= 0 ? 'disabled' : '' }}>
                                {{ $product->stock > 0 ? 'Beli Sekarang' : 'Stok Habis' }}
                            </button>
                        </form>
                            @if($product->stock > 0)
                                <a href="{{ route('products.show', $product) }}" class="btn btn-outline-warning btn-sm w-100 text-dark">
                                    Cek Detail
                                </a>
                            @else
                                <button class="btn btn-outline-warning btn-sm w-100 text-dark" disabled>
                                    Stok Habis
                                </button>
                            @endif
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12 text-center py-5">
            <p class="text-muted">Produk belum tersedia di etalase.</p>
        </div>
        @endforelse
    </div>
</section>
@endsection

