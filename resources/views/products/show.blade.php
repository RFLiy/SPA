@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-md-6 mb-4">
            <img src="{{ Storage::url($product->image) }}" class="img-fluid rounded shadow-sm w-100" alt="{{ $product->name }}" style="max-height: 500px; object-fit: fill;">
        </div>
        <div class="col-md-6">
            <h1 class="fw-bold">{{ $product->name }}</h1>
            <p class="h3 fw-bold mb-3">Rp{{ number_format($product->base_price, 0, ',', '.') }}</p>

            <div class="text-dark text-justify">
                <p>{!! $product->description !!}</p>
            </div>
            @if($product->category)
            <p class="mt-2">
                <strong>Category:</strong> {{ $product->category->name }}
            </p>
            @endif
            <form method="POST" action="{{ route('cart.add', $product) }}">
                @csrf
                <button class="btn btn-warning text-light">Tambah Ke Keranjang</button>
            </form>
        </div>
    </div>
</div>
@endsection
