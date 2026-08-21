@extends('layouts/layoutPublic')

@section('title', 'Status Booking '.$booking->booking_code)

@section('content')
<section class="py-4">
    <div class="container" style="max-width: 760px;">
        <h1 class="h3 fw-bold mb-4">Status Booking</h1>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="card mb-4">
            <div class="card-body">
                <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                    <div>
                        <small class="text-muted d-block">Kode Booking</small>
                        <strong class="fs-4">{{ $booking->booking_code }}</strong>
                    </div>
                    <div class="text-end">
                        @php
                            $sBadge = ['pending' => 'warning', 'confirmed' => 'info', 'active' => 'primary', 'completed' => 'success', 'cancelled' => 'secondary', 'rejected' => 'danger', 'expired' => 'dark'][$booking->status] ?? 'secondary';
                            $pay = $booking->latestPayment;
                            $pBadge = ['pending' => 'warning', 'paid' => 'success', 'failed' => 'danger', 'expired' => 'dark'][$pay?->status ?? 'pending'] ?? 'secondary';
                        @endphp
                        <span class="badge bg-label-{{ $sBadge }} mb-1">Booking: {{ ucfirst($booking->status) }}</span><br>
                        <span class="badge bg-label-{{ $pBadge }}">Pembayaran: {{ ucfirst($pay?->status ?? 'pending') }}</span>
                    </div>
                </div>

                <table class="table table-sm small mb-0">
                    <tr><td class="text-muted w-40">Penyewa</td><td>{{ $booking->customerName() }}</td></tr>
                    <tr><td class="text-muted">Kost</td><td>{{ $booking->kost->name }} — {{ $booking->kost->lokasi->name ?? '' }}</td></tr>
                    <tr><td class="text-muted">Kamar</td><td>{{ $booking->kamar->number }} ({{ $booking->kamar->tipeKamar->name ?? '-' }})</td></tr>
                    <tr><td class="text-muted">Periode Sewa</td>
                        <td>{{ $booking->start_date->translatedFormat('d F Y') }} — {{ $booking->end_date->translatedFormat('d F Y') }}
                            ({{ $booking->duration_months }} bulan)</td></tr>
                    <tr><td class="text-muted">Total Pembayaran</td><td><strong>Rp{{ number_format($booking->total_amount, 0, ',', '.') }}</strong></td></tr>
                    <tr><td class="text-muted">No. Invoice</td><td>{{ $pay?->invoice_no ?? '-' }}</td></tr>
                </table>
            </div>
        </div>

        @if ($pay && $pay->status === 'pending')
            <div class="card border-warning mb-4">
                <div class="card-body">
                    <h6 class="fw-bold"><i class="bx bx-info-circle text-warning me-1"></i>Instruksi Pembayaran</h6>
                    <p class="small text-muted mb-2">
                        Lakukan pembayaran sebelum <strong>{{ $pay->expired_at?->format('d M Y H:i') }}</strong>.
                        Setelah itu booking otomatis kedaluwarsa dan kamar dilepas.
                    </p>
                    <p class="small mb-0">
                        Transfer ke rekening: <strong>Bank BRI 1234-5678-9012 a.n. KostKu</strong>,
                        nominal tepat <strong>Rp{{ number_format($pay->amount, 0, ',', '.') }}</strong>.
                        Konfirmasi pembayaran melalui kontak pengelola kost.
                    </p>
                </div>
            </div>
        @endif

        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('invoice.show', $booking->access_token) }}" class="btn btn-primary"><i class="bx bx-receipt me-1"></i>Lihat Invoice</a>
            <a href="{{ route('cek-booking.index') }}" class="btn btn-outline-secondary">Cek Booking Lain</a>
            <button onclick="window.print()" class="btn btn-outline-secondary"><i class="bx bx-printer me-1"></i>Cetak</button>
        </div>
    </div>
</section>
@endsection
