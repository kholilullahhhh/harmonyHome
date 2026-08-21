@extends('layouts/layoutPublic')

@section('title', 'Pesan Kamar '.$kamar->number)

@section('content')
<section class="py-4">
    <div class="container">
        <h1 class="h3 fw-bold mb-1">Data Penyewa</h1>
        <p class="text-muted mb-4">Lengkapi data berikut untuk menyelesaikan pemesanan. Anda tidak perlu membuat akun.</p>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0 small">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('guest.booking.store', $kamar->id) }}" method="POST" id="checkout-form">
            @csrf
            <div class="row g-4">
                <div class="col-lg-7">
                    <div class="card mb-4">
                        <div class="card-header"><h6 class="fw-bold mb-0">Identitas Penyewa</h6></div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label" for="guest_name">Nama Lengkap <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="guest_name" name="guest_name"
                                        value="{{ old('guest_name', auth()->user()->name ?? '') }}" required maxlength="100">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="guest_email">Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" id="guest_email" name="guest_email"
                                        value="{{ old('guest_email', auth()->user()->email ?? '') }}" required>
                                    <div class="form-text">Kode booking & invoice dikirim ke email ini.</div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="guest_phone">Nomor HP <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="guest_phone" name="guest_phone"
                                        value="{{ old('guest_phone', auth()->user()->phone ?? '') }}" required placeholder="08xxxxxxxxxx">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="guest_identity_number">Nomor Identitas (KTP) <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="guest_identity_number" name="guest_identity_number"
                                        value="{{ old('guest_identity_number') }}" required maxlength="50">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label" for="guest_gender">Jenis Kelamin</label>
                                    <select class="form-select" id="guest_gender" name="guest_gender">
                                        <option value="">- Pilih -</option>
                                        <option value="L" {{ old('guest_gender') === 'L' ? 'selected' : '' }}>Laki-laki</option>
                                        <option value="P" {{ old('guest_gender') === 'P' ? 'selected' : '' }}>Perempuan</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label" for="guest_birth_date">Tanggal Lahir</label>
                                    <input type="date" class="form-control" id="guest_birth_date" name="guest_birth_date"
                                        value="{{ old('guest_birth_date') }}">
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label" for="guest_address">Alamat</label>
                                    <textarea class="form-control" id="guest_address" name="guest_address" rows="2">{{ old('guest_address') }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-4">
                        <div class="card-header"><h6 class="fw-bold mb-0">Periode Sewa</h6></div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label" for="start_date">Tanggal Mulai <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="start_date" name="start_date"
                                        value="{{ old('start_date', now()->toDateString()) }}" min="{{ now()->toDateString() }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="duration_months">Durasi Sewa <span class="text-danger">*</span></label>
                                    <select class="form-select" id="duration_months" name="duration_months" required>
                                        @for ($i = 1; $i <= 12; $i++)
                                            <option value="{{ $i }}" {{ old('duration_months', 1) == $i ? 'selected' : '' }}>{{ $i }} bulan</option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label" for="notes">Catatan untuk Pengelola</label>
                                    <textarea class="form-control" id="notes" name="notes" rows="2">{{ old('notes') }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-5">
                    <div class="card sticky-top" style="top: 90px;">
                        <div class="card-header"><h6 class="fw-bold mb-0">Detail Kamar</h6></div>
                        <div class="card-body">
                            <div class="d-flex gap-3 align-items-center mb-3">
                                @if ($kamar->photo)
                                    <img src="{{ Storage::url($kamar->photo) }}" alt="Kamar {{ $kamar->number }}"
                                        class="rounded" style="width: 90px; height: 70px; object-fit: cover;">
                                @else
                                    <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 90px; height: 70px;">
                                        <i class="bx bx-bed text-muted"></i>
                                    </div>
                                @endif
                                <div>
                                    <strong class="d-block">{{ $kamar->kost->name }}</strong>
                                    <small class="text-muted">Kamar {{ $kamar->number }} — {{ $kamar->tipeKamar->name ?? '-' }}</small>
                                </div>
                            </div>
                            <hr>
                            <table class="table table-sm table-borderless small mb-3">
                                <tr><td>Harga per bulan</td><td class="text-end">Rp{{ number_format($kamar->price_monthly, 0, ',', '.') }}</td></tr>
                                <tr><td>Durasi</td><td class="text-end"><span id="sum-duration">1</span> bulan</td></tr>
                                <tr><td>Subtotal</td><td class="text-end"><strong id="sum-subtotal">Rp{{ number_format($kamar->price_monthly, 0, ',', '.') }}</strong></td></tr>
                                <tr><td>Biaya tambahan</td><td class="text-end">Rp0</td></tr>
                                <tr class="border-top">
                                    <td><strong>Total</strong></td>
                                    <td class="text-end"><strong class="text-primary" id="sum-total">Rp{{ number_format($kamar->price_monthly, 0, ',', '.') }}</strong></td>
                                </tr>
                            </table>
                            <button type="submit" class="btn btn-primary btn-lg w-100">Konfirmasi Booking</button>
                            <p class="text-muted small text-center mt-2 mb-0">
                                Kamar ditahan selama 24 jam setelah booking dibuat. Selesaikan pembayaran sebelum masa tenggang berakhir.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const price = {{ $kamar->price_monthly }};
        const fmt = new Intl.NumberFormat('id-ID');
        function recalc() {
            const d = parseInt($('#duration_months').val()) || 1;
            const total = price * d;
            $('#sum-duration').text(d);
            $('#sum-subtotal').text('Rp' + fmt.format(total));
            $('#sum-total').text('Rp' + fmt.format(total));
        }
        $('#duration_months').on('change', recalc);
    });
</script>
@endsection
