@component('mail::message')
# Terima kasih, {{ $booking->customerName() }}!

Booking Anda berhasil dibuat. Simpan **kode booking** berikut untuk melihat status penyewaan Anda kapan saja tanpa perlu login.

@component('mail::panel')
**Kode Booking: {{ $booking->booking_code }}**
@endcomponent

## Detail Penyewaan

- **Kost:** {{ $booking->kost->name }}
- **Kamar:** {{ $booking->kamar->number }}
- **Tanggal Mulai:** {{ $booking->start_date->translatedFormat('d F Y') }}
- **Tanggal Selesai:** {{ $booking->end_date->translatedFormat('d F Y') }}
- **Durasi:** {{ $booking->duration_months }} bulan
- **Total Pembayaran:** Rp{{ number_format($booking->total_amount, 0, ',', '.') }}
- **Status Booking:** {{ ucfirst($booking->status) }}
- **Status Pembayaran:** {{ ucfirst($booking->latestPayment?->status ?? 'pending') }}

@component('mail::button', ['url' => route('invoice.show', $booking->access_token)])
Lihat Invoice
@endcomponent

@component('mail::button', ['url' => route('cek-booking.index')])
Cek Status Booking
@endcomponent

Invoice Anda akan aktif setelah pembayaran diterima. Pembayaran dapat dilakukan dalam waktu **24 jam**, setelah itu booking otomatis kedaluwarsa dan kamar dilepas.

Terima kasih,<br>
{{ config('app.name') }}
@endcomponent
