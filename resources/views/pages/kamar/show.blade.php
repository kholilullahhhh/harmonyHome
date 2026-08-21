@extends('layouts/layoutMaster')

@section('title', 'Detail Kamar')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold mb-4">
        <span class="text-muted fw-light">Master Data / <a href="{{ route('kamar.index') }}">Data Kamar</a> /</span>
        Detail: {{ $data->number }}
    </h4>

    <div class="row g-4">
        <div class="col-md-5">
            <div class="card">
                <div class="card-body text-center">
                    @if ($data->photo)
                        <img src="{{ Storage::url($data->photo) }}" alt="{{ $data->number }}"
                            class="img-fluid rounded" style="max-height: 320px;">
                    @else
                        <div class="bg-light d-flex align-items-center justify-content-center rounded"
                            style="height: 220px;">
                            <i class="bx bx-bed bx-lg text-muted"></i>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-7">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Informasi Kamar</h5>
                    @php
                        $badge = ['available' => 'success', 'reserved' => 'warning', 'occupied' => 'info', 'maintenance' => 'danger'][$data->status] ?? 'secondary';
                    @endphp
                    <span class="badge bg-label-{{ $badge }}">{{ ucfirst($data->status) }}</span>
                </div>
                <div class="card-body">
                    <table class="table table-borderless mb-0">
                        <tr><td class="w-25 text-muted">Kost</td><td>{{ $data->kost->name ?? '-' }}</td></tr>
                        <tr><td class="text-muted">Tipe Kamar</td><td>{{ $data->tipeKamar->name ?? '-' }}</td></tr>
                        <tr><td class="text-muted">Nomor</td><td><strong>{{ $data->number }}</strong></td></tr>
                        <tr><td class="text-muted">Harga/Bulan</td><td><strong>Rp{{ number_format($data->price_monthly, 0, ',', '.') }}</strong></td></tr>
                        <tr><td class="text-muted">Ukuran</td><td>{{ $data->size ?? '-' }}</td></tr>
                        <tr><td class="text-muted">Lantai</td><td>{{ $data->floor ?? '-' }}</td></tr>
                        <tr><td class="text-muted">Deskripsi</td><td>{{ $data->description ?? '-' }}</td></tr>
                    </table>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h5 class="mb-0">Fasilitas</h5></div>
                <div class="card-body">
                    @forelse ($data->fasilitas as $fasilitas)
                        <span class="badge bg-label-primary me-1 mb-1">{{ $fasilitas->name }}</span>
                    @empty
                        <span class="text-muted">Tidak ada fasilitas.</span>
                    @endforelse
                </div>
            </div>

            <div class="mt-4">
                <a href="{{ route('kamar.edit', $data->id) }}" class="btn btn-primary"><i class="bx bx-edit me-1"></i>Edit</a>
                <a href="{{ route('kamar.index', ['kost_id' => $data->kost_id]) }}" class="btn btn-outline-secondary">Kembali</a>
            </div>
        </div>
    </div>
</div>
@endsection
