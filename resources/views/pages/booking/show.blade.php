@extends('layouts/layoutMaster')

@section('title', 'Detail Booking')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold mb-4">
        <span class="text-muted fw-light">Transaksi / <a href="{{ route('booking.index') }}">Data Booking</a> /</span>
        {{ $booking->booking_code }}
    </h4>

    <div class="row g-4">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Booking Information</h5>
                    <span class="badge bg-label-{{ $booking->booking_type === 'guest' ? 'warning' : 'primary' }}">
                        {{ strtoupper($booking->booking_type) }}
                    </span>
                </div>
                <div class="card-body">
                    <table class="table table-borderless mb-0">
                        <tr><td class="w-40 text-muted">Kode Booking</td><td><strong>{{ $booking->booking_code }}</strong></td></tr>
                        <tr><td class="text-muted">Status</td>
                            <td><span class="badge bg-label-secondary">{{ ucfirst($booking->status) }}</span></td></tr>
                        <tr><td class="text-muted">Tanggal Mulai</td><td>{{ $booking->start_date->translatedFormat('d F Y') }}</td></tr>
                        <tr><td class="text-muted">Tanggal Selesai</td><td>{{ $booking->end_date->translatedFormat('d F Y') }}</td></tr>
                        <tr><td class="text-muted">Durasi</td><td>{{ $booking->duration_months }} bulan</td></tr>
                        <tr><td class="text-muted">Catatan</td><td>{{ $booking->notes ?? '-' }}</td></tr>
                    </table>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header"><h5 class="mb-0">Customer Information</h5></div>
                <div class="card-body">
                    @if ($booking->isGuest())
                        <table class="table table-borderless mb-0">
                            <tr><td class="w-40 text-muted">User Account</td><td><em class="text-muted">Tidak ada (Guest)</em></td></tr>
                            <tr><td class="text-muted">Nama</td><td>{{ $booking->guest_name }}</td></tr>
                            <tr><td class="text-muted">Email</td><td>{{ $booking->guest_email }}</td></tr>
                            <tr><td class="text-muted">Phone</td><td>{{ $booking->guest_phone }}</td></tr>
                            <tr><td class="text-muted">Identity Number</td><td>{{ $booking->guest_identity_number ?? '-' }}</td></tr>
                            <tr><td class="text-muted">Jenis Kelamin</td><td>{{ ['L' => 'Laki-laki', 'P' => 'Perempuan'][$booking->guest_gender] ?? '-' }}</td></tr>
                            <tr><td class="text-muted">Tanggal Lahir</td><td>{{ $booking->guest_birth_date?->format('d F Y') ?? '-' }}</td></tr>
                            <tr><td class="text-muted">Alamat</td><td>{{ $booking->guest_address ?? '-' }}</td></tr>
                        </table>
                    @else
                        <table class="table table-borderless mb-0">
                            <tr><td class="w-40 text-muted">User Account</td><td>{{ $booking->user?->name }} ({{ $booking->user?->email }})</td></tr>
                            <tr><td class="text-muted">Phone</td><td>{{ $booking->user?->phone ?? '-' }}</td></tr>
                        </table>
                    @endif
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h5 class="mb-0">Payment Information</h5></div>
                <div class="card-body table-responsive">
                    <table class="table">
                        <thead>
                            <tr><th>Invoice</th><th>Amount</th><th>Method</th><th>Status</th><th>Paid At</th><th>Aksi</th></tr>
                        </thead>
                        <tbody>
                            @forelse ($booking->payments as $payment)
                                <tr>
                                    <td>{{ $payment->invoice_no }}</td>
                                    <td>Rp{{ number_format($payment->amount, 0, ',', '.') }}</td>
                                    <td>{{ ucfirst($payment->method) }}</td>
                                    <td><span class="badge bg-label-secondary">{{ ucfirst($payment->status) }}</span></td>
                                    <td>{{ $payment->paid_at?->format('d M Y H:i') ?? '-' }}</td>
                                    <td>
                                        @if ($payment->status === 'pending')
                                            <form action="{{ route('payment.mark-paid', $payment->id) }}" method="POST"
                                                onsubmit="return confirm('Tandai invoice ini LUNAS?')">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success">Mark Paid</button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="text-center text-muted">Belum ada pembayaran.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header"><h5 class="mb-0">Property & Room</h5></div>
                <div class="card-body">
                    <table class="table table-borderless mb-0">
                        <tr><td class="w-40 text-muted">Kost</td><td>{{ $booking->kost->name ?? '-' }}</td></tr>
                        <tr><td class="text-muted">Lokasi</td><td>{{ $booking->kost->lokasi->name ?? '-' }}</td></tr>
                        <tr><td class="text-muted">Kamar</td><td>{{ $booking->kamar->number ?? '-' }}</td></tr>
                        <tr><td class="text-muted">Tipe</td><td>{{ $booking->kamar->tipeKamar->name ?? '-' }}</td></tr>
                    </table>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header"><h5 class="mb-0">Ringkasan Harga</h5></div>
                <div class="card-body">
                    <table class="table table-borderless mb-0">
                        <tr><td>Harga/bulan</td><td class="text-end">Rp{{ number_format($booking->price_per_month, 0, ',', '.') }}</td></tr>
                        <tr><td>Subtotal ({{ $booking->duration_months }} bln)</td><td class="text-end">Rp{{ number_format($booking->subtotal, 0, ',', '.') }}</td></tr>
                        <tr><td>Diskon</td><td class="text-end">Rp{{ number_format($booking->discount, 0, ',', '.') }}</td></tr>
                        <tr><td>Biaya tambahan</td><td class="text-end">Rp{{ number_format($booking->additional_fee, 0, ',', '.') }}</td></tr>
                        <tr class="border-top"><td><strong>Total</strong></td>
                            <td class="text-end"><strong>Rp{{ number_format($booking->total_amount, 0, ',', '.') }}</strong></td></tr>
                    </table>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h5 class="mb-0">Aksi Status</h5></div>
                <div class="card-body d-grid gap-2">
                    @if ($booking->status === 'pending')
                        <form action="{{ route('booking.confirm', $booking->id) }}" method="POST">@csrf
                            <button class="btn btn-info w-100">Konfirmasi Booking</button></form>
                        <form action="{{ route('booking.reject', $booking->id) }}" method="POST"
                            onsubmit="return confirm('Tolak booking ini dan lepaskan kamar?')">@csrf
                            <button class="btn btn-outline-danger w-100">Tolak Booking</button></form>
                    @endif
                    @if ($booking->status === 'confirmed')
                        <form action="{{ route('booking.activate', $booking->id) }}" method="POST"
                            onsubmit="return confirm('Proses check-in dan jadikan kamar Occupied?')">@csrf
                            <button class="btn btn-primary w-100">Check-in (Activate)</button></form>
                        <form action="{{ route('booking.cancel', $booking->id) }}" method="POST"
                            onsubmit="return confirm('Batalkan booking ini dan lepaskan kamar?')">@csrf
                            <button class="btn btn-outline-danger w-100">Batalkan Booking</button></form>
                    @endif
                    @if ($booking->status === 'active')
                        <form action="{{ route('booking.complete', $booking->id) }}" method="POST"
                            onsubmit="return confirm('Proses check-out dan bebaskan kamar?')">@csrf
                            <button class="btn btn-success w-100">Check-out (Complete)</button></form>
                    @endif
                    @if (!in_array($booking->status, ['active', 'completed']))
                        <form action="{{ route('booking.destroy', $booking->id) }}" method="POST"
                            onsubmit="return confirm('Hapus permanen data booking ini?')">@csrf @method('DELETE')
                            <button class="btn btn-outline-secondary w-100">Hapus Data</button></form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
