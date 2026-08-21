@extends('layouts/layoutPublic')

@section('title', $kost->name)

@section('content')
<section class="py-4">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb small">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('front.kost.index') }}">Cari Kost</a></li>
                <li class="breadcrumb-item active">{{ \Illuminate\Support\Str::limit($kost->name, 30) }}</li>
            </ol>
        </nav>

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card mb-4">
                    @if ($kost->cover)
                        <img src="{{ Storage::url($kost->cover) }}" class="card-img-top" alt="{{ $kost->name }}"
                            style="max-height: 380px; object-fit: cover;">
                    @else
                        <div class="bg-light d-flex align-items-center justify-content-center" style="height: 280px;">
                            <i class="bx bx-building-house bx-lg text-muted"></i>
                        </div>
                    @endif
                    <div class="card-body">
                        <div class="d-flex flex-wrap justify-content-between gap-2 align-items-start">
                            <div>
                                <h1 class="h4 fw-bold mb-1">{{ $kost->name }}</h1>
                                <p class="text-muted small mb-0"><i class="bx bx-map"></i> {{ $kost->address }}, {{ $kost->kecamatan ?? '' }} {{ $kost->lokasi->name ?? '' }}</p>
                            </div>
                            <div class="text-end">
                                <span class="badge bg-label-success mb-1 d-block">{{ $kost->kamar_available_count }} Kamar Tersedia</span>
                                @if ($avgRating > 0)
                                    <small class="text-muted"><i class="bx bxs-star text-warning"></i> {{ $avgRating }}/5</small>
                                @endif
                            </div>
                        </div>
                        <hr>
                        <h6 class="fw-bold">Deskripsi</h6>
                        <p class="text-muted small">{{ $kost->description ?? 'Tidak ada deskripsi.' }}</p>

                        <h6 class="fw-bold mt-3">Peraturan</h6>
                        @if ($kost->rules)
                            <ul class="small text-muted mb-0">
                                @foreach (preg_split('/\r\n|\r|\n/', $kost->rules) as $rule)
                                    @if (trim($rule))
                                        <li>{{ trim($rule) }}</li>
                                    @endif
                                @endforeach
                            </ul>
                        @else
                            <p class="text-muted small mb-0">Tidak ada peraturan khusus.</p>
                        @endif
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header"><h6 class="fw-bold mb-0">Daftar Kamar</h6></div>
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead>
                                <tr><th>Kamar</th><th>Tipe</th><th>Harga/Bulan</th><th>Status</th><th></th></tr>
                            </thead>
                            <tbody>
                                @forelse ($kamars as $kamar)
                                    <tr>
                                        <td><strong>{{ $kamar->number }}</strong>@if($kamar->floor) <small class="text-muted">Lt. {{ $kamar->floor }}</small>@endif</td>
                                        <td>{{ $kamar->tipeKamar->name ?? '-' }}</td>
                                        <td>Rp{{ number_format($kamar->price_monthly, 0, ',', '.') }}</td>
                                        <td>
                                            @php $badge = ['available' => 'success', 'reserved' => 'warning', 'occupied' => 'info', 'maintenance' => 'danger'][$kamar->status] ?? 'secondary'; @endphp
                                            <span class="badge bg-label-{{ $badge }}">{{ ucfirst($kamar->status) }}</span>
                                        </td>
                                        <td class="text-end">
                                            <a href="{{ route('front.kamar.show', [$kost->slug, $kamar->id]) }}" class="btn btn-sm btn-outline-primary">Detail</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="text-center text-muted py-4">Belum ada kamar.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header"><h6 class="fw-bold mb-0">Ulasan Penghuni</h6></div>
                    <div class="card-body">
                        @forelse ($reviews as $review)
                            <div class="border-bottom pb-3 mb-3">
                                <div class="d-flex justify-content-between">
                                    <strong class="small">{{ $review->user->name ?? 'Penghuni' }}</strong>
                                    <small class="text-warning">
                                        @for ($i = 0; $i < 5; $i++)
                                            <i class="bx bxs-star{{ $i < $review->rating ? '' : '-half' }}"></i>
                                        @endfor
                                    </small>
                                </div>
                                <p class="small text-muted mb-0">{{ $review->comment }}</p>
                            </div>
                        @empty
                            <p class="text-muted small mb-0">Belum ada ulasan.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card sticky-top" style="top: 90px;">
                    <div class="card-body">
                        <h6 class="fw-bold mb-3">Fasilitas</h6>
                        <div class="mb-3">
                            @forelse ($kost->fasilitas as $fasilitas)
                                <span class="badge bg-label-primary me-1 mb-1">{{ $fasilitas->name }}</span>
                            @empty
                                <span class="text-muted small">Tidak ada fasilitas.</span>
                            @endforelse
                        </div>
                        <hr>
                        <dl class="small mb-3">
                            <dt class="text-muted fw-normal">Jam Akses</dt>
                            <dd>{{ $kost->access_hours ?? '-' }}</dd>
                            <dt class="text-muted fw-normal">Telepon</dt>
                            <dd>{{ $kost->phone ?? '-' }}</dd>
                        </dl>
                        <a href="#daftar-kamar" class="btn btn-primary w-100"
                            onclick="document.querySelector('.table-responsive').scrollIntoView({behavior:'smooth'});return false;">
                            Pesan Kamar
                        </a>
                        <p class="text-center text-muted small mt-2 mb-0">Tidak perlu membuat akun untuk memesan.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
