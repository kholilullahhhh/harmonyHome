<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom sticky-top py-2">
    <div class="container">
        <a class="navbar-brand fw-bold text-primary d-flex align-items-center gap-2" href="{{ route('home') }}">
            <i class="bx bx-home-heart bx-sm"></i> KostKu
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#publicNavbar"
            aria-controls="publicNavbar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="publicNavbar">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('front.kost.*') || request()->routeIs('front.kamar.*') ? 'active' : '' }}"
                        href="{{ route('front.kost.index') }}">Cari Kost</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('front.cara-kerja') ? 'active' : '' }}" href="{{ route('front.cara-kerja') }}">Cara Kerja</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('front.tentang') ? 'active' : '' }}" href="{{ route('front.tentang') }}">Tentang Kami</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('front.kontak') ? 'active' : '' }}" href="{{ route('front.kontak') }}">Kontak</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('cek-booking.*') ? 'active' : '' }}" href="{{ route('cek-booking.index') }}">Cek Booking</a>
                </li>
            </ul>
            <div class="d-flex gap-2">
                @auth
                    <a href="{{ route('dashboard') }}" class="btn btn-outline-primary">Dashboard</a>
                @endauth
                @guest
                    <a href="{{ route('login') }}" class="btn btn-outline-primary">Login</a>
                    <a href="{{ route('register') }}" class="btn btn-primary">Daftar</a>
                @endguest
            </div>
        </div>
    </div>
</nav>
