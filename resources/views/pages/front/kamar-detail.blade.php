@extends('layouts/layoutPublic')

@section('title', 'Kamar '.$kamar->number.' — '.$kost->name)

@section('content')
<section class="py-4">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb small">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('front.kost.index') }}">Cari Kost</a></li>
                <li class="breadcrumb-item"><a href="{{ route('front.kost.show', $kost->slug) }}">{{ \Illuminate\Support\Str::limit($kost->name, 25) }}</a></li>
                <li class="breadcrumb-item active">Kamar {{ $kamar->number }}</li>
            </ol>
        </nav>

        <div class="row g-4">
            <div class="col-lg-7">
                <div class="card mb-4">
                    @if ($kamar->photo)
                        <img src="{{ Storage::url($kamar->photo) }}" class="card-img-top" alt="Kamar {{ $kamar->number }}"
                            style="max-height: 360px; object-fit: cover;">
                    @else
                        <div class="bg-light d-flex align-items-center justify-content-center" style="height: 260px;">
                            <i class="bx bx-bed bx-lg text-muted"></i>
                        </div>
                    @endif
                    <div class="card-body">
                        <h1 class="h4 fw-bold mb-1">Kamar {{ $kamar->number }}</h1>
                        <p class="text-muted small">{{ $kost->name }} — {{ $kost->lokasi->name ?? '' }}</p>
                        <p class="small text-muted">{{ $kamar->description ?? 'Tidak ada deskripsi kamar.' }}</p>

                        <h6 class="fw-bold mt-3">Fasilitas Kamar</h6>
                        @forelse ($kamar->fasilitas as $fasilitas)
                            <span class="badge bg-label-primary me-1 mb-1">{{ $fasilitas->name }}</span>
                        @empty
                            <span class="text-muted small">Tidak ada fasilitas khusus.</span>
                        @endforelse

                        <hr>
                        <h6 class="fw-bold">Peraturan Kost</h6>
                        @if ($kost->rules)
                            <ul class="small text-muted mb-0">
                                @foreach (array_slice(preg_split('/\r\n|\r|\n/', $kost->rules) ?: [], 0, 5) as $rule)
                                    @if (trim($rule))
                                        <li>{{ trim($rule) }}</li>
                                    @endif
                                @endforeach
                            </ul>
                        @else
                            <p class="text-muted small mb-0">Mengikuti peraturan umum kost.</p>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="card sticky-top" style="top: 90px;">
                    <div class="card-body">
                        <table class="table table-sm table-borderless small mb-3">
                            <tr><td class="text-muted">Harga per Bulan</td><td class="text-end"><strong>Rp{{ number_format($kamar->price_monthly, 0, ',', '.') }}</strong></td></tr>
                            <tr><td class="text-muted">Tipe</td><td class="text-end">{{ $kamar->tipeKamar->name ?? '-' }}</td></tr>
                            <tr><td class="text-muted">Ukuran</td><td class="text-end">{{ $kamar->size ?? '-' }}</td></tr>
                            <tr><td class="text-muted">Lantai</td><td class="text-end">{{ $kamar->floor ?? '-' }}</td></tr>
                            <tr><td class="text-muted">Status</td>
                                <td class="text-end">
                                    @php $badge = ['available' => 'success', 'reserved' => 'warning', 'occupied' => 'info', 'maintenance' => 'danger'][$kamar->status] ?? 'secondary'; @endphp
                                    <span class="badge bg-label-{{ $badge }}">{{ ucfirst($kamar->status) }}</span>
                                </td></tr>
                        </table>

                        @if ($kamar->status === 'available')
                            <a href="{{ route('guest.booking.checkout', $kamar->id) }}" class="btn btn-primary btn-lg w-100">
                                Pesan Sekarang
                            </a>
                            <p class="text-center text-muted small mt-2 mb-0">Tanpa login. Cukup isi data penyewa.</p>
                        @else
                            <button class="btn btn-secondary btn-lg w-100" disabled>Kamar Tidak Tersedia</button>
                            <a href="{{ route('front.kost.show', $kost->slug) }}" class="btn btn-outline-primary w-100 mt-2">
                                Lihat Kamar Lain
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
