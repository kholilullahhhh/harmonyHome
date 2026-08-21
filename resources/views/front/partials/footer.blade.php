<footer class="bg-white border-top mt-auto py-4">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-4">
                <h5 class="fw-bold text-primary"><i class="bx bx-home-heart me-1"></i>KostKu</h5>
                <p class="text-muted small mb-0">
                    Platform penyewaan kost yang mudah dan transparan.
                    Cari kost, pesan kamar, dan bayar tanpa perlu membuat akun.
                </p>
            </div>
            <div class="col-md-3">
                <h6 class="fw-bold">Navigasi</h6>
                <ul class="list-unstyled small">
                    <li><a href="{{ route('home') }}" class="text-muted">Home</a></li>
                    <li><a href="{{ route('front.kost.index') }}" class="text-muted">Cari Kost</a></li>
                    <li><a href="{{ route('front.cara-kerja') }}" class="text-muted">Cara Kerja</a></li>
                    <li><a href="{{ route('cek-booking.index') }}" class="text-muted">Cek Booking</a></li>
                </ul>
            </div>
            <div class="col-md-3">
                <h6 class="fw-bold">Bantuan</h6>
                <ul class="list-unstyled small">
                    <li><a href="{{ route('front.tentang') }}" class="text-muted">Tentang Kami</a></li>
                    <li><a href="{{ route('front.kontak') }}" class="text-muted">Kontak</a></li>
                </ul>
            </div>
            <div class="col-md-2">
                <h6 class="fw-bold">Akun</h6>
                <ul class="list-unstyled small">
                    <li><a href="{{ route('login') }}" class="text-muted">Login</a></li>
                    <li><a href="{{ route('register') }}" class="text-muted">Daftar</a></li>
                </ul>
            </div>
        </div>
        <hr class="my-3">
        <p class="text-muted small mb-0 text-center">&copy; {{ date('Y') }} KostKu. Seluruh hak cipta dilindungi.</p>
    </div>
</footer>
