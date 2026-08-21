@extends('layouts/layoutPublic')

@section('title', 'Tentang Kami')

@section('content')
<section class="py-5">
    <div class="container" style="max-width: 760px;">
        <h1 class="h3 fw-bold mb-3">Tentang KostKu</h1>
        <p class="text-muted">
            KostKu adalah platform penyewaan kost yang menghubungkan pemilik kost dengan pencari hunian
            secara mudah dan transparan. Kami percaya proses mencari tempat tinggal seharusnya sederhana:
            cari, bandingkan, pesan — tanpa perantara yang rumit.
        </p>
        <p class="text-muted">
            Sistem kami mendukung pemesanan <strong>dengan atau tanpa akun</strong>. Setiap transaksi dilengkapi
            kode booking dan invoice yang dapat dilacak kapan saja.
        </p>
        <a href="{{ route('front.kost.index') }}" class="btn btn-primary">Mulai Cari Kost</a>
    </div>
</section>
@endsection
