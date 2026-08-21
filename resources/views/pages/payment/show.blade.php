@extends('layouts/layoutMaster')

@section('title', 'Detail Pembayaran')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold mb-4">
        <span class="text-muted fw-light">Transaksi / <a href="{{ route('payment.index') }}">Data Pembayaran</a> /</span>
        {{ $payment->invoice_no }}
    </h4>

    <div class="row g-4">
        <div class="col-md-7">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Invoice Information</h5>
                    <span class="badge bg-label-secondary">{{ ucfirst($payment->status) }}</span>
                </div>
                <div class="card-body">
                    <table class="table table-borderless mb-0">
                        <tr><td class="w-40 text-muted">No. Invoice</td><td><strong>{{ $payment->invoice_no }}</strong></td></tr>
                        <tr><td class="text-muted">Booking</td>
                            <td><a href="{{ route('booking.show', $payment->booking->id) }}">{{ $payment->booking->booking_code }}</a>
                                <span class="badge bg-label-{{ $payment->booking->booking_type === 'guest' ? 'warning' : 'primary' }} ms-1">
                                    {{ strtoupper($payment->booking->booking_type) }}
                                </span>
                            </td></tr>
                        <tr><td class="text-muted">Penyewa</td><td>{{ $payment->booking->customerName() }}</td></tr>
                        <tr><td class="text-muted">Nominal</td><td><strong>Rp{{ number_format($payment->amount, 0, ',', '.') }}</strong></td></tr>
                        <tr><td class="text-muted">Metode</td><td>{{ ucfirst($payment->method) }}</td></tr>
                        <tr><td class="text-muted">Dibayar Pada</td><td>{{ $payment->paid_at?->format('d F Y H:i') ?? '-' }}</td></tr>
                        <tr><td class="text-muted">Kedaluwarsa</td><td>{{ $payment->expired_at?->format('d F Y H:i') ?? '-' }}</td></tr>
                        <tr><td class="text-muted">Order ID (Midtrans)</td><td>{{ $payment->order_id ?? '-' }}</td></tr>
                    </table>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h5 class="mb-0">Property & Room</h5></div>
                <div class="card-body">
                    <table class="table table-borderless mb-0">
                        <tr><td class="w-40 text-muted">Kost</td><td>{{ $payment->booking->kost->name ?? '-' }}</td></tr>
                        <tr><td class="text-muted">Kamar</td><td>{{ $payment->booking->kamar->number ?? '-' }}</td></tr>
                        <tr><td class="text-muted">Periode</td>
                            <td>{{ $payment->booking->start_date->format('d M Y') }} — {{ $payment->booking->end_date->format('d M Y') }}
                                ({{ $payment->booking->duration_months }} bln)</td></tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-5">
            <div class="card">
                <div class="card-header"><h5 class="mb-0">Aksi</h5></div>
                <div class="card-body d-grid gap-2">
                    @if ($payment->status === 'pending')
                        <form action="{{ route('payment.mark-paid', $payment->id) }}" method="POST"
                            onsubmit="return confirm('Tandai invoice ini LUNAS? Booking akan dikonfirmasi.')">
                            @csrf
                            <button class="btn btn-success w-100"><i class="bx bx-check-circle me-1"></i>Mark as Paid</button>
                        </form>
                    @else
                        <p class="text-muted mb-0 small">Tidak ada aksi tersedia untuk invoice berstatus {{ ucfirst($payment->status) }}.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
