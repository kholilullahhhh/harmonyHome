@extends('layouts/layoutPublic')

@section('title', 'Kontak')

@section('content')
<section class="py-5">
    <div class="container" style="max-width: 700px;">
        <h1 class="h3 fw-bold mb-3">Hubungi Kami</h1>
        <p class="text-muted">Ada pertanyaan seputar pemesanan atau penyewaan? Hubungi kami melalui:</p>
        <ul class="list-unstyled">
            <li class="mb-2"><i class="bx bx-envelope text-primary me-2"></i>support@kostku.id</li>
            <li class="mb-2"><i class="bx bx-phone text-primary me-2"></i>+62 812-3456-7890</li>
            <li class="mb-2"><i class="bx bx-map text-primary me-2"></i>Makassar, Sulawesi Selatan</li>
        </ul>
        <div class="alert alert-light border small mt-4 mb-0">
            Untuk konfirmasi pembayaran manual, sertakan <strong>kode booking</strong> Anda pada setiap komunikasi.
        </div>
    </div>
</section>
@endsection
