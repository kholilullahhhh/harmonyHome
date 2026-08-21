@extends('layouts/layoutPublic')

@section('title', 'Booking Berhasil')

@section('content')
<section class="py-5">
    <div class="container" style="max-width: 720px;">
        <div class="card text-center">
            <div class="card-body p-4">
                <i class="bx bx-check-circle text-success" style="font-size: 4rem;"></i>
                <h1 class="h3 fw-bold mt-2">Booking Berhasil!</h1>
                <p class="text-muted">Simpan kode booking ini untuk melihat status penyewaan Anda.</p>

                <div class="bg-light rounded p-3 my-3">
                    <small class="text-muted d-block">Kode Booking</small>
                    <h2 class="fw-bold text-primary mb-1">{{ $booking->booking_code }}</h2>
                    <small class="text-muted">Invoice: {{ $booking->latestPayment?->invoice_no }}</small>
                </div>

                <table class="table table-sm small text-start mb-4">
                    <tr><td class="text-muted">Penyewa</td><td>{{ $booking->customerName() }}</td></tr>
                    <tr><td class="text-muted">Kost</td><td>{{ $booking->kost->name }}</td></tr>
                    <tr><td class="text-muted">Kamar</td><td>{{ $booking->kamar->number }}</td></tr>
                    <tr><td class="text-muted">Periode</td>
                        <td>{{ $booking->start_date->format('d M Y') }} — {{ $booking->end_date->format('d M Y') }} ({{ $booking->duration_months }} bln)</td></tr>
                    <tr><td class="text-muted">Total</td><td><strong>Rp{{ number_format($booking->total_amount, 0, ',', '.') }}</strong></td></tr>
                    <tr><td class="text-muted">Status</td>
                        <td><span class="badge bg-label-warning">Menunggu Pembayaran</span></td></tr>
                </table>

                <div class="d-grid gap-2">
                    <a href="{{ route('invoice.show', $booking->access_token) }}" class="btn btn-primary">
                        <i class="bx bx-receipt me-1"></i>Lihat Invoice
                    </a>
                    <a href="{{ route('guest.booking.status', $booking->access_token) }}" class="btn btn-outline-primary">
                        Cek Status Booking
                    </a>
                </div>

                @guest
                    <hr class="my-4">
                    <p class="small text-muted mb-2">Sudah selesai melakukan penyewaan? Buat akun untuk mempermudah melihat transaksi berikutnya (opsional).</p>
                    <a href="{{ route('register') }}" class="btn btn-link btn-sm">Buat Akun</a>
                @endguest
            </div>
        </div>
    </div>
</section>
@endsection
