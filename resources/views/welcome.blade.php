@extends('layouts.app')

@section('preload_lcp_image')
@endsection

@section('full_width_content')
<main id="main-content">
    <section class="container px-3 py-lg-5" id="About">
        <div class="row align-items-center gx-4 gx-lg-5">
            <div class="col-lg-6 mb-4 mb-lg-0 text-start">
                <h1 class="fw-bold text-start text-dark m-0 p-0 responsive-hero-title">
                    Kualitas Premium &
                    <span class="d-inline-block bg-warning text-white px-2 py-1 rounded shadow mt-1 responsive-hero-badge" style="transform: skewX(-6deg); width: fit-content; white-space: nowrap;">
                        Tahan Lama.
                    </span>
                </h1>
                <p class="lead text-secondary mt-3 mb-4 text-start mx-0 responsive-hero-text" style="max-width: 550px;" id="hero-text-2">
                    Solusi suspensi kendaraan terbaik dengan material High Alloy Steel. Dirancang khusus untuk memberikan stabilitas maksimal dan daya tahan ekstra di segala medan.
                </p>
                <div class="d-block w-100 m-0 p-0" id="hero-actions">
                    <a href="{{ route('products.index') }}" class="btn btn-warning btn-lg text-white custom-hero-btn w-lg-auto px-4">Lihat Produk</a>
                </div>
            </div>
            <div class="col-lg-6 mt-3 mt-lg-0" id="hero-image-container">
                <div class="rounded-4 overflow-hidden shadow-lg d-flex justify-content-center position-relative mx-auto mx-lg-0 ms-lg-auto" style="max-width: 100%; width: 450px;">
                    <img src="{{ asset('images/logo.webp') }}" alt="Auto Spring Logo" class="img-fluid rounded-3 w-100 h-auto">
                </div>
            </div>
        </div>
    </section>

    <section class="container px-3 py-3 py-lg-5">
        <div class="row align-items-center gx-4 gx-lg-5">
            <div class="col-lg-6 mb-4 mb-lg-0 order-1 order-lg-2 text-start">
                <h2 class="fw-bold text-start text-dark m-0 p-0 responsive-hero-title" id="hero-title-2">
                    Berpengalaman
                    <span class="d-inline-block bg-warning text-white px-2 py-1 rounded shadow mt-1 responsive-hero-badge" style="transform: skewX(-6deg); width: fit-content; white-space: nowrap;">
                        Lebih dari 5 Tahun
                    </span>
                </h2>
                <p class="lead text-secondary mt-3 mb-4 text-start mx-0 responsive-hero-text" style="max-width: 550px;" id="hero-text-description">
                    Kami telah melayani ribuan kebutuhan otomotif dan industri, menyediakan pegas baja berkualitas tinggi yang telah teruji melalui kontrol kualitas yang sangat ketat.
                </p>
                <div class="d-flex w-100 w-lg-auto justify-content-start m-0 p-0" id="hero-actions-2">
                    <a href="{{ route('products.index') }}" class="btn btn-warning btn-lg text-white custom-hero-btn w-lg-auto px-4">Penawaran Terbaru</a>
                </div>
            </div>
            <div class="col-lg-6 order-2 order-lg-1 mt-3 mt-lg-0" id="hero-image-container-2">
                <div class="rounded-4 overflow-hidden shadow-lg position-relative">
                    <img src="{{ asset('images/hero1.webp') }}" alt="Suasana Pabrik Auto Spring Manufacturing" class="img-fluid rounded-3 w-100" style="min-height: 200px; max-height: 350px; object-fit: cover;">
                </div>
            </div>
        </div>
    </section>

    <span id="Product"></span>
    <section class="container py-5 bg-light">
        <div class="d-flex align-items-center justify-content-between mb-4" data-aos="fade-right">
            <h2 class="fw-bold mb-0 fs-3 display-6 text-start">
                Katalog
                <span class="bg-warning text-white px-3 py-1 rounded-3 d-inline-block" style="transform: skewX(-8deg);">
                    Produk.
                </span>
            </h2>
            <a href="{{ route('products.index') }}" class="btn btn-warning btn-sm text-white px-3 py-2" style="font-size: 0.85rem; white-space: nowrap;">
                Lihat Lainnya
            </a>
        </div>
        <div class="row g-3 g-md-4 mt-2" data-aos="fade-right">
            @forelse($products as $product)
            <div class="col-6 col-md-4 col-lg-3">
                <div class="card h-100 shadow-sm product-card border-0 rounded-3">
                    <div class="product-img-container rounded-top" style="overflow: hidden;">
                        @if($product->image)
                            <img src="{{ Storage::url($product->image) }}" loading="lazy" class="card-img-top w-100 h-100 object-fit-cover" alt="Produk {{ $product->name }}">
                        @else
                            <div class="card-img-top w-100 h-100 bg-secondary d-flex align-items-center justify-content-center text-white">
                                No Image
                            </div>
                        @endif
                    </div>
                    <div class="card-body d-flex flex-column bg-transparent p-2 p-md-3 text-start">
                        <h3 class="h6 fw-bold text-truncate mb-1" title="{{ $product->name }}">{{ $product->name }}</h3>
                        <p class="text-secondary small mb-2 text-truncate-3">
                            {{ Str::limit(strip_tags(html_entity_decode($product->description)), 50) }}
                        </p>
                        <p class="mb-1 small {{ $product->stock <= 5 ? 'text-danger fw-bold' : 'text-muted' }}">
                            Stok: {{ $product->stock > 0 ? $product->stock . ' ' . $product->unit : 'Habis' }}
                        </p>
                        <p class="fw-bold mt-auto mb-3 fs-5 text-warning">
                            Rp {{ number_format($product->base_price, 0, ',', '.') }}
                        </p>
                        <div class="d-grid gap-2 mb-2">
                            <form action="{{ route('cart.add', $product) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-outline-warning btn-sm w-100 text-dark" {{ $product->stock <= 0 ? 'disabled' : '' }}>
                                    {{ $product->stock > 0 ? 'Beli Sekarang' : 'Stok Habis' }}
                                </button>
                            </form>
                        </div>
                        <div class="d-grid gap-2">
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

    <span id="Services"></span>
    <section class="py-5" data-aos="fade-up">
        <div class="container py-4">
            <div class="text-center mb-5">
                <h2 class="fw-bold mb-0">
                    Layanan
                    <span class="bg-warning text-white px-3 py-1 rounded-3 d-inline-block" style="transform: skewX(-8deg);">
                        Terbaik.
                    </span>
                </h2>
                <div class="mx-auto mt-3" style="width: 60px; height: 3px; background: #FFD41D; border-radius: 2px;"></div>
                <p class="text-muted mt-3 px-3">Solusi manufaktur dan distribusi pegas baja dengan standar kualitas industri internasional.</p>
            </div>
            <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-4">
                <div class="col">
                    <div class="card border-0 rounded-4 shadow-sm overflow-hidden h-100 text-center p-3 bg-white">
                        <div class="mb-2">
                            <img src="{{ asset('images/client1.webp') }}" loading="lazy" class="rounded-circle shadow-sm border border-2 border-warning mx-auto" style="width: 60px; height: 60px; object-fit: cover;" alt="Ilustrasi Layanan Distribusi">
                        </div>
                        <div class="card-body bg-transparent p-2">
                            <h3 class="h6 fw-bold text-dark mb-1">Distribusi Luas</h3>
                            <p class="text-muted small mb-0 text-truncate-3">Jaringan logistik mencakup seluruh wilayah Indonesia untuk pengiriman tepat waktu.</p>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card border-0 rounded-4 shadow-sm overflow-hidden h-100 text-center p-3 bg-white">
                        <div class="mb-2">
                            <img src="{{ asset('images/client2.webp') }}" loading="lazy" class="rounded-circle shadow-sm border border-2 border-warning mx-auto" style="width: 60px; height: 60px; object-fit: cover;" alt="Ilustrasi Tim Ahli Presisi">
                        </div>
                        <div class="card-body bg-transparent p-2">
                            <h3 class="h6 fw-bold text-dark mb-1">Tim Ahli Presisi</h3>
                            <p class="text-muted small mb-0 text-truncate-3">Dukungan teknisi berpengalaman untuk konsultasi specifications material custom.</p>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card border-0 rounded-4 shadow-sm overflow-hidden h-100 text-center p-3 bg-white">
                        <div class="mb-2">
                            <img src="{{ asset('images/client3.webp') }}" loading="lazy" class="rounded-circle shadow-sm border border-2 border-warning mx-auto" style="width: 60px; height: 60px; object-fit: cover;" alt="Ilustrasi Proses Produksi Instan">
                        </div>
                        <div class="card-body bg-transparent p-2">
                            <h3 class="h6 fw-bold text-dark mb-1">Produksi Instan</h3>
                            <p class="text-muted small mb-0 text-truncate-3">Proses manufaktur High Alloy Steel yang efisien tanpa menunggu waktu lama.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <span id="Contact"></span>
    <section class="py-5 position-relative overflow-hidden" data-aos="fade-up">
        <div class="position-absolute top-0 end-0 p-5 opacity-10 d-none d-lg-block" style="z-index: -1;">
            <i class="fas fa-cog fa-10x text-warning" aria-hidden="true"></i>
        </div>
        <div class="container text-center py-4">
            <div class="mb-5 px-2">
                <p class="text-uppercase fw-bold ls-3 mb-2" style="color: #FFD41D; font-size: 0.8rem; letter-spacing: 2px;">Keunggulan Kami</p>
                <h2 class="fw-bold display-6 fs-3 fs-md-2">
                    Kenapa Memilih
                    <span style="color: #FFD41D; font-style: italic;">Sinar Perkasa Abadi?</span>
                </h2>
                <div class="mx-auto mt-3" style="width: 60px; height: 3px; background: #FFD41D; border-radius: 5px;"></div>
                <p class="text-muted mt-3 mx-auto" style="max-width: 700px;">
                    Kami memahami bahwa suspensi adalah kunci kenyamanan dan keselamatan. Material baja paduan tinggi kami menawarkan daya tahan ekstra di segala medan.
                </p>
            </div>
            <div class="row g-4 justify-content-center">
                <div class="col-12 col-sm-6 col-md-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="card p-4 h-100 feature-card shadow-sm rounded-4 border-0 bg-white text-center">
                        <div class="icon-box mx-auto mb-3 d-flex align-items-center justify-content-center shadow-sm">
                            <i class="fas fa-microchip fa-xl"></i>
                        </div>
                        <h3 class="h5 fw-bold mb-2">Material High Alloy</h3>
                        <p class="text-muted small mb-0">Menggunakan campuran Kromium & Vanadium untuk ketahanan lelah 3x lebih tinggi dibanding baja karbon standar.</p>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-md-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="card p-4 h-100 feature-card shadow-sm rounded-4 border-0 bg-white text-center">
                        <div class="icon-box mx-auto mb-3 d-flex align-items-center justify-content-center shadow-sm">
                            <i class="fas fa-tools fa-xl"></i>
                        </div>
                        <h3 class="h5 fw-bold mb-2">Teknologi Presisi</h3>
                        <p class="text-muted small mb-0">Diproses dengan mesin otomatis terkini untuk menjamin akurasi dimensi dan performa suspensi yang konsisten.</p>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-md-4" data-aos="fade-up" data-aos-delay="300">
                    <div class="card p-4 h-100 feature-card shadow-sm rounded-4 border-0 bg-white text-center">
                        <div class="icon-box mx-auto mb-3 d-flex align-items-center justify-content-center shadow-sm">
                            <i class="fas fa-check-double fa-xl"></i>
                        </div>
                        <h3 class="h5 fw-bold mb-2">Teruji & Terpercaya</h3>
                        <p class="text-muted small mb-0">Telah melayani ribuan kebutuhan industri otomotif dengan standar kontrol kualitas yang sangat ketat.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-5" style="background-color: #fcfaf5;" data-aos="fade-up">
        <div class="container py-3">
            <div class="text-center mb-5 px-2">
                <h2 class="fw-bold mb-0">
                    Kata Mereka Tentang
                    <span class="bg-warning text-white px-3 py-1 rounded-3 d-inline-block" style="transform: skewX(-8deg);">
                        Produk Kami.
                    </span>
                </h2>
                <p class="text-muted mt-3">Ribuan pelanggan telah membuktikan kualitas suspensi dan layanan kami.</p>
            </div>
            <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-4">
                <div class="col" data-aos="fade-up" data-aos-delay="100">
                    <div class="card h-100 border-0 shadow-sm rounded-4 p-4 review-card text-center bg-white">
                            <div class="rounded-circle bg-warning text-white d-flex align-items-center justify-content-center fw-bold mx-auto shadow-sm" style="width: 55px; height: 55px; font-size: 20px;">
                                A
                            </div>
                        <div class="mb-2">
                            <h3 class="fw-bold mb-0 text-dark" style="font-size: 0.9rem;">Adam Magelang</h3>
                            <small class="text-muted d-block" style="font-size: 0.75rem;">Logistik Ekspres</small>
                        </div>
                        <div class="text-warning mb-2" style="font-size: 0.75rem;" aria-label="Rating 5 bintang">
                            <i class="fas fa-star" aria-hidden="true"></i><i class="fas fa-star" aria-hidden="true"></i><i class="fas fa-star" aria-hidden="true"></i><i class="fas fa-star" aria-hidden="true"></i><i class="fas fa-star" aria-hidden="true"></i>
                        </div>
                        <p class="card-text text-dark small fst-italic mb-0 text-truncate-3" style="font-size: 0.8rem; line-height: 1.4;">
                            "Setelah ganti ke High Alloy Steel dari sini, mobil muatan saya jauh lebih stabil dan pernya tidak gampang amblas meskipun bawa beban berat setiap hari."
                        </p>
                    </div>
                </div>
                <div class="col" data-aos="fade-up" data-aos-delay="200">
                    <div class="card h-100 border-0 shadow-sm rounded-4 p-4 review-card text-center bg-white">
                            <div class="rounded-circle bg-warning text-white d-flex align-items-center justify-content-center fw-bold mx-auto shadow-sm" style="width: 55px; height: 55px; font-size: 20px;">
                                J
                            </div>
                        <div class="mb-2">
                            <h3 class="fw-bold mb-0 text-dark" style="font-size: 0.9rem;">Jefry Sanca</h3>
                            <small class="text-muted d-block" style="font-size: 0.75rem;">Bengkel Spesialis</small>
                        </div>
                        <div class="text-warning mb-3" aria-label="Rating 5 bintang">
                            <i class="fas fa-star" aria-hidden="true"></i><i class="fas fa-star" aria-hidden="true"></i><i class="fas fa-star" aria-hidden="true"></i><i class="fas fa-star" aria-hidden="true"></i><i class="fas fa-star" aria-hidden="true"></i>
                        </div>
                        <p class="card-text text-dark small fst-italic mb-0 text-truncate-3" style="font-size: 0.8rem; line-height: 1.4;">
                            "Pelayanan sangat cepat and teknisinya sangat membantu menjelaskan specifications material Kromium & Vanadium yang saya butuhkan untuk proyek custom."
                        </p>
                    </div>
                </div>
                <div class="col" data-aos="fade-up" data-aos-delay="300">
                    <div class="card h-100 border-0 shadow-sm rounded-4 p-4 review-card text-center bg-white">
                        <div class="mb-2">
                            <div class="rounded-circle bg-warning text-white d-flex align-items-center justify-content-center fw-bold mx-auto shadow-sm" style="width: 55px; height: 55px; font-size: 20px;">
                                S
                            </div>
                        </div>
                        <div class="mb-2">
                            <h3 class="fw-bold mb-0 text-dark" style="font-size: 0.9rem;">Steven Hercules</h3>
                            <small class="text-muted d-block" style="font-size: 0.75rem;">Offroad Enthusiast</small>
                        </div>
                        <div class="text-warning mb-3" aria-label="Rating 4.5 bintang">
                            <i class="fas fa-star" aria-hidden="true"></i><i class="fas fa-star" aria-hidden="true"></i><i class="fas fa-star" aria-hidden="true"></i><i class="fas fa-star" aria-hidden="true"></i><i class="fas fa-star-half-alt" aria-hidden="true"></i>
                        </div>
                        <p class="card-text text-dark small fst-italic mb-0 text-truncate-3" style="font-size: 0.8rem; line-height: 1.4;">
                            "Kualitas barang OEM tapi harga jauh lebih bersahabat. Pengiriman ke luar kota juga aman dan rapi. Akhirnya nemu supplier pegas baja yang jujur."
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<style>
    .responsive-hero-title {
        font-size: clamp(1.4rem, 5vw, 3.5rem) !important;
        line-height: 1.2 !important;
    }
    .responsive-hero-badge {
        font-size: clamp(1.1rem, 3.5vw, 2.2rem) !important;
    }
    .responsive-hero-text {
        font-size: clamp(0.85rem, 2.5vw, 1.15rem) !important;
        line-height: 1.5 !important;
    }
    .product-img-container {
        height: clamp(120px, 20vw, 200px);
    }
    .text-truncate-3 {
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .bg-light {
        background-color: #f8fafc !important;
    }
    .custom-hero-btn {
        width: 50% !important;
    }
    .text-secondary {
        color: #4b5563 !important;
    }
    @media (max-width: 991.98px) {
        .container, .row, [class*="col-"] {
            text-align: left !important;
        }
        .custom-hero-btn {
        width: 100% !important;
        }
    }
</style>
@endsection
