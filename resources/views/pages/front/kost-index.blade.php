@extends('layouts/layoutPublic')

@section('title', 'Cari Kost')

@section('content')
<section class="py-4">
    <div class="container">
        <h1 class="h3 fw-bold mb-4">Cari Kost</h1>
        <div class="row g-4">
            <div class="col-lg-3">
                <form method="GET" action="{{ route('front.kost.index') }}" class="card">
                    <div class="card-body">
                        <h6 class="fw-bold mb-3">Filter</h6>
                        <div class="mb-3">
                            <label class="form-label small" for="q">Kata Kunci</label>
                            <input type="text" class="form-control form-control-sm" id="q" name="q"
                                value="{{ request('q') }}" placeholder="Nama kost / wilayah">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small" for="lokasi_id">Lokasi</label>
                            <select class="form-select form-select-sm" id="lokasi_id" name="lokasi_id">
                                <option value="">Semua Lokasi</option>
                                @foreach ($lokasiList as $lokasi)
                                    <option value="{{ $lokasi->id }}" {{ request('lokasi_id') == $lokasi->id ? 'selected' : '' }}>{{ $lokasi->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small">Rentang Harga (per bulan)</label>
                            <div class="d-flex gap-2">
                                <input type="number" class="form-control form-control-sm" name="min_price"
                                    value="{{ request('min_price') }}" placeholder="Min">
                                <input type="number" class="form-control form-control-sm" name="max_price"
                                    value="{{ request('max_price') }}" placeholder="Maks">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small" for="tipe_kamar_id">Tipe Kamar</label>
                            <select class="form-select form-select-sm" id="tipe_kamar_id" name="tipe_kamar_id">
                                <option value="">Semua Tipe</option>
                                @foreach ($tipeList as $tipe)
                                    <option value="{{ $tipe->id }}" {{ request('tipe_kamar_id') == $tipe->id ? 'selected' : '' }}>{{ $tipe->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small d-block">Fasilitas</label>
                            @foreach ($fasilitasList as $fasilitas)
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="fasilitas[]"
                                        value="{{ $fasilitas->id }}" id="f-{{ $fasilitas->id }}"
                                        {{ in_array($fasilitas->id, (array) request('fasilitas')) ? 'checked' : '' }}>
                                    <label class="form-check-label small" for="f-{{ $fasilitas->id }}">{{ $fasilitas->name }}</label>
                                </div>
                            @endforeach
                        </div>
                        <div class="mb-3">
                            <label class="form-label small" for="sort">Urutkan</label>
                            <select class="form-select form-select-sm" id="sort" name="sort">
                                <option value="">Terbaru</option>
                                <option value="price_asc" {{ request('sort') === 'price_asc' ? 'selected' : '' }}>Harga Terendah</option>
                                <option value="price_desc" {{ request('sort') === 'price_desc' ? 'selected' : '' }}>Harga Tertinggi</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 btn-sm">Terapkan Filter</button>
                        <a href="{{ route('front.kost.index') }}" class="btn btn-link btn-sm w-100 text-muted mt-1">Reset</a>
                    </div>
                </form>
            </div>

            <div class="col-lg-9">
                <p class="text-muted small">{{ $kosts->total() }} kost ditemukan</p>
                <div class="row g-4">
                    @forelse ($kosts as $kost)
                        <div class="col-md-6">
                            <div class="card h-100">
                                @if ($kost->cover)
                                    <img src="{{ Storage::url($kost->cover) }}" class="card-img-top" alt="{{ $kost->name }}"
                                        style="height: 180px; object-fit: cover;" loading="lazy">
                                @else
                                    <div class="bg-light d-flex align-items-center justify-content-center" style="height: 180px;">
                                        <i class="bx bx-building-house bx-lg text-muted"></i>
                                    </div>
                                @endif
                                <div class="card-body">
                                    <span class="badge bg-label-primary mb-2">{{ $kost->lokasi->name ?? '-' }}</span>
                                    <h5 class="h6 fw-bold mb-1">{{ \Illuminate\Support\Str::limit($kost->name, 45) }}</h5>
                                    <p class="text-muted small mb-2"><i class="bx bx-map"></i> {{ \Illuminate\Support\Str::limit($kost->address, 55) }}</p>
                                    <p class="mb-0"><strong class="text-primary">Mulai Rp{{ number_format($kost->kamar_min_price_monthly ?? 0, 0, ',', '.') }}/bulan</strong></p>
                                </div>
                                <div class="card-footer bg-transparent d-flex justify-content-between align-items-center">
                                    <small class="text-muted">{{ $kost->kamar_available_count }} kamar tersedia</small>
                                    <a href="{{ route('front.kost.show', $kost->slug) }}" class="btn btn-sm btn-outline-primary">Detail</a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12 text-center text-muted py-5">
                            Tidak ada kost yang cocok dengan filter Anda.
                        </div>
                    @endforelse
                </div>

                <div class="mt-4 d-flex justify-content-center">
                    {{ $kosts->links() }}
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
