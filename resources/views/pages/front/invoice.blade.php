@extends('layouts/layoutPublic')

@section('title', 'Invoice '.$booking->latestPayment?->invoice_no)

@section('content')
<section class="py-4">
    <div class="container" style="max-width: 760px;">
        <div class="card" id="invoice-card">
            <div class="card-body p-4">
                <div class="d-flex flex-wrap justify-content-between mb-4">
                    <div>
                        <h1 class="h3 fw-bold text-primary mb-0"><i class="bx bx-home-heart"></i> KostKu</h1>
                        <small class="text-muted">Bukti Pemesanan Penyewaan Kost</small>
                    </div>
                    <div class="text-end">
                        <small class="text-muted d-block">No. Invoice</small>
                        <strong>{{ $booking->latestPayment?->invoice_no }}</strong><br>
                        <small class="text-muted d-block mt-1">Tanggal</small>
                        <small>{{ $booking->created_at->format('d M Y') }}</small>
                    </div>
                </div>

                <hr>

                <table class="table table-sm small">
                    <tr>
                        <td class="text-muted w-30">Kode Booking</td>
                        <td><strong>{{ $booking->booking_code }}</strong></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Nama Penyewa</td>
                        <td>{{ $booking->customerName() }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Email / HP</td>
                        <td>{{ $booking->customerEmail() }} / {{ $booking->customerPhone() }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Kost</td>
                        <td>{{ $booking->kost->name }} — {{ $booking->kost->lokasi->name ?? '' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Kamar</td>
                        <td>{{ $booking->kamar->number }} ({{ $booking->kamar->tipeKamar->name ?? '-' }})</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Periode Sewa</td>
                        <td>{{ $booking->start_date->translatedFormat('d F Y') }} — {{ $booking->end_date->translatedFormat('d F Y') }}
                            ({{ $booking->duration_months }} bulan)</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Status Booking</td>
                        <td>{{ ucfirst($booking->status) }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Status Pembayaran</td>
                        <td>{{ ucfirst($booking->latestPayment?->status ?? 'pending') }}</td>
                    </tr>
                </table>

                <table class="table table-sm small">
                    <tr><td>Harga per bulan</td><td class="text-end">Rp{{ number_format($booking->price_per_month, 0, ',', '.') }}</td></tr>
                    <tr><td>Subtotal ({{ $booking->duration_months }} bln)</td><td class="text-end">Rp{{ number_format($booking->subtotal, 0, ',', '.') }}</td></tr>
                    <tr><td>Diskon</td><td class="text-end">Rp{{ number_format($booking->discount, 0, ',', '.') }}</td></tr>
                    <tr><td>Biaya tambahan</td><td class="text-end">Rp{{ number_format($booking->additional_fee, 0, ',', '.') }}</td></tr>
                    <tr class="border-top">
                        <td><strong>TOTAL</strong></td>
                        <td class="text-end"><strong class="fs-5">Rp{{ number_format($booking->total_amount, 0, ',', '.') }}</strong></td>
                    </tr>
                </table>

                @if (($booking->latestPayment?->status ?? 'pending') === 'pending')
                    <div class="alert alert-warning small mb-0">
                        Pembayaran dapat dilakukan hingga <strong>{{ $booking->latestPayment?->expired_at?->format('d M Y H:i') }}</strong>.
                        Transfer ke Bank BRI 1234-5678-9012 a.n. KostKu dengan nominal yang tepat.
                    </div>
                @else
                    <div class="alert alert-success small mb-0">
                        Pembayaran telah diterima pada {{ $booking->latestPayment?->paid_at?->format('d F Y H:i') }}. Terima kasih!
                    </div>
                @endif
            </div>
        </div>

        <div class="mt-3 d-print-none">
            <button onclick="window.print()" class="btn btn-primary"><i class="bx bx-printer me-1"></i>Cetak Invoice</button>
            <a href="{{ route('guest.booking.status', $booking->access_token) }}" class="btn btn-outline-secondary">Kembali ke Status Booking</a>
        </div>
    </div>
</section>
@endsection
