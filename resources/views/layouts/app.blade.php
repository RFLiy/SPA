<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="PT.Sinar Perkasa Abadi.">
    <title>@yield('title', 'E-Commerce SPA')</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="icon" type="image/png" href="{{ asset('images/lgo.png') }}">

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        .navbar-treact {
            font-family: 'Inter', sans-serif;
        }

        .navbar-treact .nav-link {
            color: #000000ff !important;
            font-weight: 600;
            font-size: 1rem;
            transition: color 0.15s ease-in-out;
        }

        .navbar-treact .nav-link:hover {
            color: #FFD41D !important;
        }

        .nav-item-separator {
            color: #ccc;
            padding: 0 0.5rem;
            line-height: 1;
        }

        .transition-300 {
            transition: all 0.3s ease-in-out;
        }

        .hover-shadow-lg:hover {
            transform: translateY(-5px);
            box-shadow: 0 1rem 3rem rgba(0, 0, 0, 0.17) !important;
        }

        .nav-underline-hover {
            position: relative;
            text-decoration: none;
            transition: all 0.3s ease;

        }

        .nav-underline-hover::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: 0;
            left: 0;
            background-color: black;
            transition: width 0.3s ease;
            background-color: #FFD41D;
        }

        .nav-underline-hover:hover::after {
            width: 100%;
        }

        .navbar,
        .dropdown-menu {
            position: relative;
            z-index: 9999 !important;
        }

        .navbar-sticky {
            position: sticky;
            top: 0;
            z-index: 9999;
            background-color: rgba(255, 255, 255, 0.6);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
            transition: background-color 0.3s ease;
        }

        @media (max-width: 767.98px) {
            .navbar-brand {
                max-width: 70%;
            }
            .navbar-brand span {
                font-size: 1.1rem;
            }
            .navbar-brand small {
                font-size: 0.7rem;
            }
            .navbar-collapse {
                background: white;
                padding: 1rem 1.5rem;
                border-radius: 0.5rem;
                margin-top: 0.5rem;
                box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            }
        }

        .aos-init {
            opacity: 0;
        }

        .card-body {
            background-color: #d3d3d37a;
        }

        .menu-card {
            border-radius: 14px;
            transition: all 0.3s ease-in-out;
        }

        .menu-image-wrapper {
            position: relative;
        }

        .menu-image-wrapper img {
            transition: 0.3s ease-in-out;
        }

        .menu-hover-btn {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) scale(0.8);
            opacity: 0;
            pointer-events: none;
            transition: 0.3s ease-in-out;
            font-weight: bold;
        }

        .menu-card:hover img {
            opacity: 0.4;
            transform: scale(1.05);
        }

        .menu-card:hover .menu-hover-btn {
            opacity: 1;
            pointer-events: auto;
            transform: translate(-50%, -50%) scale(1);
        }

        .card-article {
            border: none;
            border-radius: 0.5rem;
            transition: transform 0.3s ease-in-out;
        }

        .card-article:hover {
            transform: translateY(-5px);
        }

        .featured-article .card-body {
            padding: 2rem;
        }

        .featured-article-title {
            font-size: 1.75rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .article-meta {
            margin-top: 1rem;
            display: flex;
            align-items: center;
        }

        .article-meta img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 0.5rem;
        }

        .dotted-bg-top {
            position: absolute;
            top: 0;
            right: 0;
            width: 150px;
            height: 150px;
            background: radial-gradient(circle, #ddd 1px, transparent 1px);
            background-size: 10px 10px;
            z-index: -1;
            opacity: 0.4;
            transform: translate(30%, -30%);
        }

        .dotted-bg-bottom {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 150px;
            height: 150px;
            background: radial-gradient(circle, #ddd 1px, transparent 1px);
            background-size: 10px 10px;
            z-index: -1;
            opacity: 0.4;
            transform: translate(-30%, 30%);
        }

        .bg-gold-light {
            background-color: #FFD41D;
        }

        .review-card {
            transition: all 0.3s ease;
            border: 1px solid rgba(0,0,0,0.05) !important;
        }

        .review-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 1rem 3rem rgba(0,0,0,0.1) !important;
        }

        .italic {
            font-style: italic;
        }

        .service-card-img {
            height: 220px;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .service-card:hover .service-card-img {
            transform: scale(1.1);
        }

        .btn-outline-gold {
            color: #FFD41D;
            border-color: #FFD41D;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-outline-gold:hover {
            background-color: #FFD41D;
            color: white;
            border-color: #FFD41D;
        }

        .icon-box {
            width: 80px;
            height: 80px;
            background-color: #fcfaf5;
            color: #FFD41D;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: transform 0.6s cubic-bezier(0.4, 0, 0.2, 1), background-color 0.3s;
            transform-style: preserve-3d;
        }

        .feature-card:hover .icon-box {
            transform: rotateY(360deg);
            background-color: #FFD41D;
            color: #fff;
        }

        .feature-card {
            transition: all 0.3s ease;
            background: #fff;
            border-bottom: 4px solid transparent !important;
        }

        .feature-card:hover {
            transform: translateY(-10px);
            border-bottom: 4px solid #FFD41D !important;
            box-shadow: 0 1rem 3rem rgba(0,0,0,0.1) !important;
        }

        .ls-3 {
            letter-spacing: 3px;
        }
        .product-img-container {
            height: 130px;
        }
        .layanan-img-container {
            height: 120px;
        }

        @media (min-width: 576px) {
            .product-img-container {
                height: 200px;
            }
            .layanan-img-container {
                height: 220px;
            }
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-md navbar-treact bg-blur shadow-sm navbar-sticky">
        <div class="container">
            <a class="navbar-brand d-flex flex-column align-items-start slide-in-up" id="logo" href="{{ url('/') }}">
                <span class="fw-bold text-dark">PT.Sinar Perkasa Abadi</span>
                <small class="text-muted" style="margin-top: -2px; font-size: 0.8rem;">Sparepart Manufacturing</small>
            </a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar"
                aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="mainNavbar">
                <ul class="navbar-nav mx-auto mb-2 mb-md-0 gap-md-3">
                    <li class="nav-item slide-in-up nav-underline-hover" id="nav-about">
                        <a class="nav-link text-dark" href="{{ url('/') }}#About">About</a>
                    </li>
                    <li class="nav-item slide-in-up nav-underline-hover" id="nav-product">
                        <a class="nav-link text-dark" href="{{ url('/') }}#Product">Product</a>
                    </li>
                    <li class="nav-item slide-in-up nav-underline-hover" id="nav-services">
                        <a class="nav-link text-dark" href="{{ url('/') }}#Services">Services</a>
                    </li>
                    <li class="nav-item slide-in-up nav-underline-hover" id="nav-contact">
                        <a class="nav-link text-dark" href="{{ url('/') }}#Contact">Contact</a>
                    </li>
                </ul>

                <ul class="navbar-nav align-items-md-center gap-2">
                    @guest
                        <li class="nav-item slide-in-up" id="nav-login">
                            <a class="nav-link text-dark fw-bold" href="{{ route('login') }}">Login</a>
                        </li>
                        <li class="nav-item slide-in-up" id="nav-signup">
                            <a class="btn btn-warning text-white fw-bold px-3 rounded-pill shadow-sm" href="{{ route('register') }}">Sign Up</a>
                        </li>
                    @endguest
                    @auth
                        @if (!Auth::user()->hasAnyRole(['super_admin', 'Owner']))
                            <li class="nav-item d-flex gap-2 mb-2 mb-md-0">
                                <a class="nav-link text-dark bg-light rounded-3 px-3 slide-in-up" id="cart" href="{{ route('cart.index') }}">
                                    <i class="fas fa-shopping-cart" style="color: #FFD41D;"></i>
                                    <span class="d-md-none ms-2">Keranjang</span>
                                </a>
                                <a class="nav-link text-dark bg-light rounded-3 px-3 slide-in-up" id="order" href="{{ route('orders.index') }}">
                                    <i class="fas fa-archive" style="color: #FFD41D;"></i>
                                    <span class="d-md-none ms-2">Pesanan</span>
                                </a>
                            </li>
                        @endif

                        <li class="nav-item dropdown slide-in-up" id="dropdown">
                            <a class="nav-link dropdown-toggle text-dark fw-bold bg-light rounded-5 px-3" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-user-circle me-1"></i> {{ Auth::user()->name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg mt-2">
                                <li>
                                    <a class="dropdown-item" href="{{ route('profile.show') }}">
                                        <i class="fas fa-id-card me-2"></i> Profile
                                    </a>
                                </li>

                                @if(Auth::user()->hasAnyRole(['super_admin', 'Owner', 'Manager']))
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <a class="dropdown-item text-warning fw-bold" href="/spa/login">
                                            <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                                        </a>
                                    </li>
                                @endif

                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item text-danger" href="{{ route('logout') }}"
                                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        <i class="fas fa-sign-out-alt me-2"></i> Logout
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    @yield('full_width_content')

    <div class="container my-4">
        @if(session('success'))
        <div class="alert alert-modern alert-modern-success alert-dismissible fade" role="alert" data-aos="slide-down" data-aos-once="true">
            <i class="bi bi-check-circle-fill me-2"></i>
            <strong>Sukses!</strong> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        @if(session('error'))
        <div class="alert alert-modern alert-modern-danger alert-dismissible fade" role="alert" data-aos="slide-down" data-aos-once="true">
            <i class="bi bi-x-octagon-fill me-2"></i>
            <strong>Gagal!</strong> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        @yield('content')
    </div>

<footer class="bg-light py-5 border-top border-bottom">
    <div class="container" data-aos="fade-left"
        data-aos-anchor="#example-anchor"
        data-aos-duration="800">
        <div class="row">

            <div class="col-lg-6 col-md-6 mb-4 mb-lg-0">
                <div class="d-flex align-items-center mb-3">
                    <span class="me-2 d-inline-flex align-items-center">
                        <img src="{{ asset('images/lgo.png') }}" alt="Logo PT Sinar Perkasa Abadi" style="height: 30px; width: auto; object-fit: contain;">
                    </span>
                    <span class="fs-5 fw-bold text-dark">PT. Sinar Perkasa Abadi.</span>
                </div>
                <p class="text-muted small">
                    Kekuatan Ekstrem, Daya Tahan Seumur Hidup! Dibuat dari baja paduan premium mengandung Kromium & Vanadium untuk menjamin ketahanan lelah 3x lebih tinggi dan performa suspensi yang konsisten.
                </p>

                <div class="d-flex mt-3">
                    <a href="#" class="btn btn-warning btn-sm rounded-circle me-2 d-flex align-items-center justify-content-center" style="width: 30px; height: 30px;">
                        <i class="fab fa-facebook-f text-white"></i>
                    </a>
                    <a href="#" class="btn btn-warning btn-sm rounded-circle me-2 d-flex align-items-center justify-content-center" style="width: 30px; height: 30px;">
                        <i class="fab fa-twitter text-white"></i>
                    </a>
                    <a href="#" class="btn btn-warning btn-sm rounded-circle d-flex align-items-center justify-content-center" style="width: 30px; height: 30px;">
                        <i class="fab fa-youtube text-white"></i>
                    </a>
                </div>
            </div>

            <div class="col-lg-2 col-md-6 mb-4 mb-lg-0">
                <h5 class="text-dark fw-bold mb-3 small">Quick Links</h5>
                <ul class="list-unstyled small">
                    <li class="mb-2"><a href="{{ url('/') }}#About" class="text-decoration-none text-muted">About Us</a></li>
                    <li class="mb-2"><a href="{{ url('/') }}#Services" class="text-decoration-none text-muted">Services</a></li>
                    <li class="mb-2"><a href="{{ url('/') }}#Contact" class="text-decoration-none text-muted">Contact</a></li>
                </ul>
            </div>

            <div class="col-lg-2 col-md-6 mb-4 mb-lg-0">
                <h5 class="text-dark fw-bold mb-3 small">Product</h5>
                <ul class="list-unstyled small">
                    <li class="mb-2"><a href="{{ route('products.index') }}" class="text-decoration-none text-muted">Katalog Produk</a></li>
                    <li class="mb-2"><a href="{{ route('cart.index') }}" class="text-decoration-none text-muted">Keranjang Belanja</a></li>
                    <li class="mb-2"><a href="{{ route('orders.index') }}" class="text-decoration-none text-muted">Pesanan Saya</a></li>
                </ul>
            </div>

            <div class="col-lg-2 col-md-6">
                <h5 class="text-dark fw-bold mb-3 small">FAQ</h5>
                <ul class="list-unstyled small">
                    <li class="mb-2"><a href="#" class="text-decoration-none text-muted">Privacy Policy</a></li>
                    <li class="mb-2"><a href="#" class="text-decoration-none text-muted">Terms of Service</a></li>
                    <li class="mb-2"><a href="#" class="text-decoration-none text-muted">FAQs</a></li>
                </ul>
            </div>
        </div>
    </div>
</footer>

<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

<script>
    AOS.init({
        duration: 600,
        once: true
    });
    document.addEventListener('DOMContentLoaded', () => {
        const elementsToAnimate = [
            'logo', 'nav-about', 'nav-blog', 'nav-services', 'nav-contact', 'nav-product', 'nav-rfq',
            'nav-login', 'nav-signup', 'nav-artikel',
            'hero-title-1', 'hero-text', 'hero-actions', 'hero-image-container',
            'hero-title-2', 'hero-text-2', 'hero-actions-2', 'hero-image-container-2', 'hero-image-container-3',
            'cart', 'order', 'navbarDropdown', 'dropdown'
        ];

        setTimeout(() => {
            elementsToAnimate.forEach(id => {
                const el = document.getElementById(id);
                if (el) el.classList.add('is-visible');
            });
        }, 100);
    });

    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(() => {
            const alerts = document.querySelectorAll('.alert-modern');
            alerts.forEach(alert => {
                alert.classList.add('show');
                alert.classList.add('show-alert');
            });
        }, 100);
    });

    document.addEventListener('DOMContentLoaded', function() {
        function updateQuantityAndSubmit(inputElement, change) {
            let currentVal = parseInt(inputElement.value);
            if (isNaN(currentVal)) {
                currentVal = 1;
            }
            let newVal = currentVal + change;
            let minVal = parseInt(inputElement.min);
            if (newVal >= minVal) {
                inputElement.value = newVal;
                inputElement.form.submit();
            }
        }
    document.querySelectorAll('.qty-plus, .qty-minus').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('data-target');
            const targetInput = document.getElementById(targetId);
            if (!targetInput) return;
            const change = this.classList.contains('qty-plus') ? 1 : -1;
            updateQuantityAndSubmit(targetInput, change);
            });
        });
    document.querySelectorAll('.quantity-control input[type="number"]').forEach(input => {
        input.addEventListener('change', function() {
            this.form.submit();
            });
        });
    });
</script>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
@stack('scripts')
</body>
</html>
