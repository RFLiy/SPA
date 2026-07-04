{{-- @extends('layouts.app')

@section('full_width_content')
<!-- Hero Section -->
<div id="heroCarousel" class="carousel slide hero-carousel" data-bs-ride="carousel">
    <div class="carousel-inner">
        <div class="carousel-item active">
            <div class="banner-media-container">
                <img src="{{ asset('images/hero1.jpg') }}" alt="Slide 1">
                <div class="carousel-caption" data-aos="fade-up" data-aos-delay="200">
                    <h1 class="display-3">Welcome Back, {{ Auth::user()->name }}!</h1>
                    <p class="lead">Check out our latest products and your personalized recommendations.</p>
                    <a href="{{ route('products.index') }}" class="btn btn-danger btn-lg mt-3" data-aos="zoom-in" data-aos-delay="400">Shop Now</a>
                </div>
            </div>
        </div>
        <div class="carousel-item">
            <div class="banner-media-container">
                <img src="{{ asset('images/hero2.jpg') }}" alt="Slide 2">
                <div class="carousel-caption" data-aos="fade-right">
                    <h1 class="display-3">Exclusive Offers</h1>
                    <p class="lead">Special deals only for our valued members.</p>
                    <a href="{{ route('products.index') }}" class="btn btn-danger btn-lg mt-3" data-aos="zoom-in" data-aos-delay="400">View Offers</a>
                </div>
            </div>
        </div>
    </div>
    <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon"></span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon"></span>
    </button>
</div>
@endsection

@section('content')
<!-- Featured Products -->
<div class="container my-5" id="feature">
    <h2 class="text-center mb-4" data-aos="fade-up">Recommended For You</h2>
    <div class="row">
        @foreach($products as $product)
        <div class="col-md-4 mb-4" data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
            <div class="card h-100 transition-300 hover-shadow-lg">
                <img src="{{ asset('images/' . $product->image) }}" class="card-img-top" alt="{{ $product->name }}">
                <div class="card-body text-center">
                    <h5 class="card-title">{{ $product->name }}</h5>
                    <p class="card-text">{{ Str::limit($product->description, 80) }}</p>
                    <a href="{{ route('products.show', $product->id) }}" class="btn btn-danger">View</a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

<!-- About Section -->
<div class="container my-5" id="about">
    <h2 class="text-center mb-5" data-aos="fade-up">Why Shop With Us?</h2>
    <div class="row text-center">
        <div class="col-md-4 mb-4" data-aos="fade-up" data-aos-delay="100">
            <i class="bi bi-truck fs-1 text-danger mb-3"></i>
            <h5>Fast Delivery</h5>
            <p>Quick shipping to your location safely.</p>
        </div>
        <div class="col-md-4 mb-4" data-aos="fade-up" data-aos-delay="200">
            <i class="bi bi-shield-lock fs-1 text-danger mb-3"></i>
            <h5>Secure Payment</h5>
            <p>Safe and encrypted transactions.</p>
        </div>
        <div class="col-md-4 mb-4" data-aos="fade-up" data-aos-delay="300">
            <i class="bi bi-star fs-1 text-danger mb-3"></i>
            <h5>Quality Products</h5>
            <p>High-quality products for your satisfaction.</p>
        </div>
    </div>
</div>
@endsection --}}
