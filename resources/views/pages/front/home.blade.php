@extends('layouts/layoutPublic')

@section('title', 'KostKu — Cari & Pesan Kost Tanpa Ribet')

@section('content')

{{-- CUSTOM CSS STYLING UNTUK ELS-UI / LANDING PAGE --}}
<style>
    /* Gradient Hero & Glassmorphism */
    .hero-section {
        background: radial-gradient(100% 100% at 50% 0%, rgba(99, 102, 241, 0.08) 0%, rgba(255, 255, 255, 0) 100%), #ffffff;
        position: relative;
        overflow: hidden;
    }
    .hero-badge {
        background-color: rgba(99, 102, 241, 0.1);
        color: #4f46e5;
        font-weight: 600;
        font-size: 0.875rem;
        padding: 0.35rem 1rem;
        border-radius: 50rem;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    /* Search Mock Box */
    .search-discovery-card {
        background: #ffffff;
        border: 1px solid rgba(226, 232, 240, 0.8);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.05), 0 10px 10px -5px rgba(0, 0, 0, 0.02);
        border-radius: 1rem;
        padding: 0.75rem;
    }
    
    /* Stat Cards */
    .stat-card {
        border: 1px solid #f1f5f9;
        border-radius: 1rem;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        background: #ffffff;
    }
    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 20px -5px rgba(0, 0, 0, 0.06);
        border-color: rgba(99, 102, 241, 0.3);
    }
    .stat-icon-wrapper {
        width: 3.25rem;
        height: 3.25rem;
        border-radius: 0.75rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }

    /* Kost Property Cards */
    .kost-card {
        border: 1px solid #f1f5f9;
        border-radius: 1rem;
        overflow: hidden;
        transition: all 0.3s ease;
        background: #ffffff;
    }
    .kost-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 20px 25px -5px rgba(15, 23, 42, 0.08), 0 8px 10px -6px rgba(15, 23, 42, 0.04);
        border-color: rgba(99, 102, 241, 0.2);
    }
    .kost-thumb-wrapper {
        position: relative;
        overflow: hidden;
        height: 220px;
        background-color: #f8fafc;
    }
    .kost-thumb-wrapper img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }
    .kost-card:hover .kost-thumb-wrapper img {
        transform: scale(1.05);
    }
    .price-tag {
        font-size: 1.125rem;
        font-weight: 700;
        color: #4f46e5;
    }

    /* Benefit & Step Cards */
    .benefit-card, .step-card {
        border-radius: 1rem;
        padding: 2rem 1.5rem;
        background: #ffffff;
        border: 1px solid #f1f5f9;
        height: 100%;
        transition: all 0.3s ease;
    }
    .benefit-card:hover, .step-card:hover {
        border-color: rgba(99, 102, 241, 0.3);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05);
    }
    .step-number {
        font-size: 2rem;
        font-weight: 800;
        color: #c7d2fe;
        line-height: 1;
        margin-bottom: 0.75rem;
    }
    
    /* CTA Banner */
    .cta-banner {
        background: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%);
        border-radius: 1.5rem;
        color: #ffffff;
    }
</style>

{{-- 1. HERO SECTION --}}
<section class="hero-section py-5 py-lg-6 border-bottom">
    <div class="container py-4">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <div class="hero-badge mb-3">
                    <i class="bx bx-home-heart fs-5"></i> Platform Penyewaan Kost Modern
                </div>

                <h1 class="display-5 fw-bold text-dark lh-sm mb-3">
                    Temukan Kost Nyaman<br class="d-none d-md-block"> untuk Tempat Tinggalmu
                </h1>

                <p class="text-muted fs-5 mb-4">
                    Bandingkan harga, lihat fasilitas, dan pesan kamar dengan cepat.
                    <span class="d-block fw-semibold text-dark mt-1">
                        <i class="bx bx-check-circle text-success me-1"></i> Tidak perlu membuat akun untuk melakukan booking.
                    </span>
                </p>

                {{-- 2. SEARCH / DISCOVERY CTA BAR MOCKUP --}}
                <div class="search-discovery-card mb-4">
                    <form action="{{ route('front.kost.index') }}" method="GET" class="row g-2 align-items-center">
                        <div class="col-md-7">
                            <div class="input-group input-group-merge border-0">
                                <span class="input-group-text bg-transparent border-0 ps-3">
                                    <i class="bx bx-search fs-4 text-muted"></i>
                                </span>
                                <input 
                                    type="text" 
                                    class="form-control border-0 bg-transparent shadow-none" 
                                    placeholder="Mulai cari kost impianmu..."
                                    readonly
                                    onclick="window.location.href='{{ route('front.kost.index') }}'"
                                    style="cursor: pointer;"
                                >
                            </div>
                        </div>
                        <div class="col-md-5">
                            <a href="{{ route('front.kost.index') }}" class="btn btn-primary btn-lg w-100 fw-semibold rounded-3">
                                <i class="bx bx-search-alt me-1"></i> Cari Kost
                            </a>
                        </div>
                    </form>
                </div>

                <div class="d-flex align-items-center gap-3">
                    <a href="{{ route('front.cara-kerja') }}" class="btn btn-link text-decoration-none text-muted fw-semibold p-0">
                        <i class="bx bx-play-circle fs-4 align-middle text-primary me-1"></i> Lihat Cara Kerja Booking
                    </a>
                </div>
            </div>

            <div class="col-lg-6 d-none d-lg-block">
                <div class="position-relative">
                    <div class="rounded-4 overflow-hidden shadow-lg">
                        <img 
                            src="https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?auto=format&fit=crop&w=800&q=80" 
                            alt="Kostku Exterior Preview" 
                            class="img-fluid w-100 object-fit-cover"
                            style="height: 440px;"
                        >
                    </div>
                    {{-- Floating Glass Card Indicator --}}
                    <div class="position-absolute bottom-0 start-0 translate-middle-y bg-white p-3 rounded-3 shadow-lg ms-n3 border d-flex align-items-center gap-3" style="max-width: 260px;">
                        <div class="bg-label-primary p-2 rounded-circle">
                            <i class="bx bx-shield-quarter fs-3 text-primary"></i>
                        </div>
                        <div>
                            <p class="fw-bold mb-0 text-dark">Pesan Langsung</p>
                            <small class="text-muted">Proses instan tanpa akun</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="container py-5">
    
    {{-- 3. STATISTICS SECTION --}}
    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="stat-card p-4 h-100 d-flex align-items-center gap-3">
                <div class="stat-icon-wrapper bg-label-primary text-primary">
                    <i class="bx bx-building-house"></i>
                </div>
                <div>
                    <h2 class="fw-bold text-dark mb-0 fs-3">{{ $stats['kost'] }}</h2>
                    <span class="text-muted fw-medium">Kost Terdaftar</span>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="stat-card p-4 h-100 d-flex align-items-center gap-3">
                <div class="stat-icon-wrapper bg-label-success text-success">
                    <i class="bx bx-bed"></i>
                </div>
                <div>
                    <h2 class="fw-bold text-dark mb-0 fs-3">{{ $stats['kamar'] }}</h2>
                    <span class="text-muted fw-medium">Kamar Tersedia</span>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="stat-card p-4 h-100 d-flex align-items-center gap-3">
                <div class="stat-icon-wrapper bg-label-info text-info">
                    <i class="bx bx-map-pin"></i>
                </div>
                <div>
                    <h2 class="fw-bold text-dark mb-0 fs-3">{{ $stats['lokasi'] }}</h2>
                    <span class="text-muted fw-medium">Wilayah Tercover</span>
                </div>
            </div>
        </div>
    </div>

    {{-- 4. FEATURED KOST SECTION --}}
    <div class="d-flex justify-content-between align-items-end mb-4">
        <div>
            <span class="badge bg-label-primary mb-1">Rekomendasi Terbaik</span>
            <h2 class="fw-bold text-dark h3 mb-0">Kost Pilihan Paling Populer</h2>
        </div>
        <a href="{{ route('front.kost.index') }}" class="btn btn-outline-primary fw-semibold d-none d-sm-inline-flex align-items-center gap-1">
            Lihat Semua Kost <i class="bx bx-right-arrow-alt fs-4"></i>
        </a>
    </div>

    <div class="row g-4 mb-5">
        @forelse ($featured as $kost)
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 kost-card border-0">
                    <div class="kost-thumb-wrapper">
                        @if ($kost->cover)
                            <img
                                src="{{ Storage::url($kost->cover) }}"
                                class="card-img-top"
                                alt="{{ $kost->name }}"
                                loading="lazy"
                            >
                        @else
                            <div class="w-100 h-100 d-flex flex-column align-items-center justify-content-center text-muted bg-light">
                                <i class="bx bx-building-house fs-1 mb-2"></i>
                                <span class="small">Foto tidak tersedia</span>
                            </div>
                        @endif

                        <div class="position-absolute top-0 start-0 m-3">
                            <span class="badge bg-white text-primary shadow-sm px-3 py-2 fw-semibold rounded-pill">
                                <i class="bx bx-map me-1"></i>{{ $kost->lokasi->name ?? 'Indonesia' }}
                            </span>
                        </div>
                    </div>

                    <div class="card-body p-4">
                        <h3 class="h5 card-title fw-bold text-dark mb-2">
                            {{ \Illuminate\Support\Str::limit($kost->name, 40) }}
                        </h3>

                        <p class="text-muted small mb-3 text-truncate">
                            <i class="bx bx-map-pin text-primary me-1"></i>
                            {{ \Illuminate\Support\Str::limit($kost->address, 60) }}
                        </p>

                        <div class="pt-2 border-top d-flex align-items-center justify-content-between">
                            <div>
                                <small class="text-muted d-block">Mulai Dari</small>
                                <span class="price-tag">
                                    Rp{{ number_format($kost->kamar_min_price_monthly ?? 0, 0, ',', '.') }}<small class="fs-7 text-muted fw-normal">/bln</small>
                                </span>
                            </div>
                            <span class="badge bg-label-success rounded-pill px-2 py-1">
                                <i class="bx bx-check me-1"></i>{{ $kost->kamar_available_count }} kamar
                            </span>
                        </div>
                    </div>

                    <div class="card-footer bg-transparent border-0 px-4 pb-4 pt-0">
                        <a href="{{ route('front.kost.show', $kost->slug) }}" class="btn btn-primary w-100 fw-semibold rounded-3">
                            Lihat Detail
                        </a>
                    </div>
                </div>
            </div>
        @empty
            {{-- 5. EMPTY STATE --}}
            <div class="col-12">
                <div class="text-center py-5 bg-white rounded-4 border">
                    <div class="avatar avatar-xl bg-label-primary mx-auto mb-3" style="width: 80px; height: 80px;">
                        <i class="bx bx-home-alt fs-1 align-middle" style="line-height: 80px;"></i>
                    </div>
                    <h4 class="fw-bold text-dark mb-1">Belum Ada Kost Tersedia</h4>
                    <p class="text-muted mb-4">Data kost pilihan akan ditampilkan di sini ketika sudah dipublikasikan.</p>
                    <a href="{{ route('front.kost.index') }}" class="btn btn-outline-primary px-4 fw-semibold">
                        Jelajahi Semua Wilayah
                    </a>
                </div>
            </div>
        @endforelse
    </div>

    {{-- MOBILE BUTTON VIEW ALL --}}
    <div class="text-center d-sm-none mb-5">
        <a href="{{ route('front.kost.index') }}" class="btn btn-outline-primary w-100 fw-semibold">
            Lihat Semua Kost <i class="bx bx-right-arrow-alt fs-4 align-middle"></i>
        </a>
    </div>

    {{-- 6. KENAPA KOSTKU SECTION --}}
    <section class="py-5 my-3 border-top">
        <div class="text-center max-width-md mx-auto mb-5">
            <span class="badge bg-label-primary mb-2">Keunggulan Layanan</span>
            <h2 class="fw-bold text-dark h3">Kenapa Memilih KostKu?</h2>
            <p class="text-muted">Proses pencarian dan persewaan hunian dibuat secepat dan semudah mungkin.</p>
        </div>

        <div class="row g-4">
            <div class="col-md-6 col-lg-3">
                <div class="benefit-card text-center">
                    <div class="avatar bg-label-primary mx-auto mb-3" style="width: 60px; height: 60px;">
                        <i class="bx bx-user-x fs-2 text-primary" style="line-height: 60px;"></i>
                    </div>
                    <h4 class="h5 fw-bold text-dark mb-2">Tanpa Akun</h4>
                    <p class="text-muted small mb-0">Lakukan pemesanan langsung tanpa perlu repot mendaftar akun baru terlebih dahulu.</p>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="benefit-card text-center">
                    <div class="avatar bg-label-success mx-auto mb-3" style="width: 60px; height: 60px;">
                        <i class="bx bx-receipt fs-2 text-success" style="line-height: 60px;"></i>
                    </div>
                    <h4 class="h5 fw-bold text-dark mb-2">Harga Transparan</h4>
                    <p class="text-muted small mb-0">Seluruh rincian biaya sewa ditampilkan jujur tanpa adanya biaya tersembunyi.</p>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="benefit-card text-center">
                    <div class="avatar bg-label-info mx-auto mb-3" style="width: 60px; height: 60px;">
                        <i class="bx bx-building-house fs-2 text-info" style="line-height: 60px;"></i>
                    </div>
                    <h4 class="h5 fw-bold text-dark mb-2">Pilihan Beragam</h4>
                    <p class="text-muted small mb-0">Temukan berbagai opsi lokasi dan fasilitas kamar yang sesuai kebutuhan Anda.</p>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="benefit-card text-center">
                    <div class="avatar bg-label-warning mx-auto mb-3" style="width: 60px; height: 60px;">
                        <i class="bx bx-time-five fs-2 text-warning" style="line-height: 60px;"></i>
                    </div>
                    <h4 class="h5 fw-bold text-dark mb-2">Update Real-time</h4>
                    <p class="text-muted small mb-0">Ketersediaan unit kamar ter-update secara otomatis langsung dari pemilik.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- 7. CARA KERJA SECTION --}}
    <section class="py-5 my-3 border-top">
        <div class="text-center max-width-md mx-auto mb-5">
            <span class="badge bg-label-primary mb-2">Langkah Mudah</span>
            <h2 class="fw-bold text-dark h3">Cara Kerja Pemesanan Kost</h2>
            <p class="text-muted">Hanya 4 langkah praktis untuk mendapatkan kamar impianmu.</p>
        </div>

        <div class="row g-4">
            <div class="col-md-6 col-lg-3">
                <div class="step-card">
                    <div class="step-number">01</div>
                    <h4 class="h5 fw-bold text-dark mb-2">Cari Kost</h4>
                    <p class="text-muted small mb-0">Pilih lokasi dan sesuaikan dengan budget serta fasilitas yang kamu inginkan.</p>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="step-card">
                    <div class="step-number">02</div>
                    <h4 class="h5 fw-bold text-dark mb-2">Pilih Kamar</h4>
                    <p class="text-muted small mb-0">Lihat ketersediaan kamar secara real-time beserta foto detailnya.</p>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="step-card">
                    <div class="step-number">03</div>
                    <h4 class="h5 fw-bold text-dark mb-2">Isi Data Booking</h4>
                    <p class="text-muted small mb-0">Lengkapi formulir pemesanan singkat tanpa perlu melakukan registrasi.</p>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="step-card">
                    <div class="step-number">04</div>
                    <h4 class="h5 fw-bold text-dark mb-2">Konfirmasi</h4>
                    <p class="text-muted small mb-0">Dapatkan konfirmasi booking dan siap untuk menempati kamar barumu.</p>
                </div>
            </div>
        </div>

        <div class="text-center mt-5">
            <a href="{{ route('front.cara-kerja') }}" class="btn btn-outline-primary px-4 fw-semibold">
                Lihat Panduan Selengkapnya
            </a>
        </div>
    </section>

    {{-- 8. CTA SECTION --}}
    <section class="my-5">
        <div class="cta-banner p-4 p-md-5 text-center position-relative overflow-hidden">
            <div class="position-relative z-1 max-width-md mx-auto">
                <h2 class="display-6 fw-bold text-white mb-3">Sudah Menemukan Kost yang Cocok?</h2>
                <p class="text-white-50 fs-5 mb-4">Mulai cari kost sekarang dan lakukan booking dengan mudah tanpa persyaratan yang rumit.</p>
                <a href="{{ route('front.kost.index') }}" class="btn btn-light btn-lg px-5 text-primary fw-bold rounded-3 shadow">
                    <i class="bx bx-search-alt me-1"></i> Cari Kost Sekarang
                </a>
            </div>
        </div>
    </section>

</div>

@endsection