@extends('layouts/layoutPublic')

@section('title', 'Cek Status Booking')

@section('content')
<section class="py-5">
    <div class="container" style="max-width: 520px;">
        <div class="card">
            <div class="card-body p-4">
                <h1 class="h4 fw-bold mb-1">Cek Status Booking</h1>
                <p class="text-muted small mb-4">Masukkan kode booking beserta email atau nomor HP yang Anda gunakan saat memesan.</p>

                @if (session('error'))
                    <div class="alert alert-danger small">{{ session('error') }}</div>
                @endif

                <form action="{{ route('cek-booking.check') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label" for="booking_code">Kode Booking <span class="text-danger">*</span></label>
                        <input type="text" class="form-control text-uppercase" id="booking_code" name="booking_code"
                            value="{{ old('booking_code') }}" placeholder="BK-20260821-X8A92" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label" for="contact">Email / Nomor HP <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="contact" name="contact"
                            value="{{ old('contact') }}" placeholder="email@anda.com atau 08xx" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bx bx-search-alt me-1"></i>Cek Booking
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection
