@extends('layouts/layoutPublic')

@section('title', 'Cara Kerja')

@section('content')
<section class="py-5">
    <div class="container" style="max-width: 760px;">
        <h1 class="h3 fw-bold mb-4">Cara Kerja</h1>
        @php $steps = [
            ['Cari Kost', 'Gunakan pencarian dan filter untuk menemukan kost sesuai kebutuhan Anda.'],
            ['Pilih Kamar', 'Lihat detail kamar: harga, fasilitas, dan status ketersediaan.'],
            ['Isi Data Penyewa', 'Isi nama, kontak, dan identitas. Tidak perlu membuat akun.'],
            ['Konfirmasi Booking', 'Periksa ringkasan pesanan lalu konfirmasi. Kamar ditahan 24 jam.'],
            ['Bayar', 'Selesaikan pembayaran sebelum masa tenggang berakhir.'],
            ['Terima Kode Booking', 'Simpan kode booking & invoice untuk melacak status sewa Anda.'],
        ]; @endphp
        <div class="row g-3">
            @foreach ($steps as $i => [$title, $desc])
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-body">
                            <span class="badge bg-primary rounded-circle p-2 mb-2">{{ $i + 1 }}</span>
                            <h6 class="fw-bold">{{ $title }}</h6>
                            <p class="small text-muted mb-0">{{ $desc }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endsection
