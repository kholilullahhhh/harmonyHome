@extends('layouts/layoutMaster')

@section('title', 'Laporan Booking')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold mb-1">Laporan Booking</h4>
    <p class="text-muted mb-4">Rekapitulasi booking dalam periode terpilih.</p>

    <div class="row g-3 mb-4">
        @foreach (['pending' => 'warning', 'confirmed' => 'info', 'active' => 'primary', 'completed' => 'success', 'cancelled' => 'secondary', 'rejected' => 'dark', 'expired' => 'danger'] as $status => $warna)
            <div class="col-6 col-md-4 col-xl-3">
                <div class="card">
                    <div class="card-body py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="text-muted small text-uppercase">{{ $status }}</span>
                                <h5 class="mb-0 mt-1">{{ number_format($ringkasan->get($status, 0)) }}</h5>
                            </div>
                            <span class="badge bg-label-{{ $warna }} p-2"><i class="bx bx-calendar bx-sm"></i></span>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
        <div class="col-6 col-md-4 col-xl-3">
            <div class="card border-primary">
                <div class="card-body py-3">
                    <span class="text-muted small text-uppercase">Nilai Booking Aktif</span>
                    <h5 class="mb-0 mt-1 text-primary">Rp{{ number_format($nilaiAktif, 0, ',', '.') }}</h5>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><h5 class="mb-0">Detail Booking</h5></div>
        <div class="card-body border-bottom pt-0 pb-3">
            <form method="GET" action="{{ route('laporan.booking.index') }}" class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small mb-1">Dari Tanggal</label>
                    <input type="date" name="date_from" value="{{ $from->toDateString() }}" class="form-control">
                </div>
                <div class="col-md-3">
                    <label class="form-label small mb-1">Sampai Tanggal</label>
                    <input type="date" name="date_to" value="{{ $to->toDateString() }}" class="form-control">
                </div>
                <div class="col-md-3">
                    <label class="form-label small mb-1">Status</label>
                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        @foreach (['pending', 'confirmed', 'active', 'completed', 'cancelled', 'rejected', 'expired'] as $s)
                            <option value="{{ $s }}" {{ $filterStatus === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary w-100"><i class="bx bx-filter me-1"></i>Terapkan Filter</button>
                </div>
            </form>
        </div>
        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Kode Booking</th>
                        <th>Kost</th>
                        <th>Kamar</th>
                        <th>Penyewa</th>
                        <th>Tipe</th>
                        <th>Mulai</th>
                        <th>Durasi</th>
                        <th>Total</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse ($data as $item)
                        @php
                            $badge = ['pending' => 'warning', 'confirmed' => 'info', 'active' => 'primary', 'completed' => 'success', 'cancelled' => 'secondary', 'rejected' => 'dark', 'expired' => 'danger'][$item->status] ?? 'secondary';
                        @endphp
                        <tr>
                            <td><strong>{{ $item->booking_code }}</strong></td>
                            <td>{{ $item->kost->name ?? '-' }}</td>
                            <td>{{ $item->kamar->number ?? '-' }}</td>
                            <td>{{ $item->customerName() }}</td>
                            <td><span class="badge bg-label-{{ $item->booking_type === 'guest' ? 'warning' : 'primary' }}">{{ strtoupper($item->booking_type) }}</span></td>
                            <td>{{ $item->start_date?->format('d M Y') }}</td>
                            <td>{{ $item->duration_months }} bln</td>
                            <td>Rp{{ number_format($item->total_amount, 0, ',', '.') }}</td>
                            <td><span class="badge bg-label-{{ $badge }}">{{ ucfirst($item->status) }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="9" class="text-center py-4 text-muted">Tidak ada booking pada periode ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($data->isNotEmpty())
            <div class="card-footer text-muted small">
                Menampilkan {{ $data->count() }} booking ({{ $from->format('d M Y') }} — {{ $to->format('d M Y') }}).
            </div>
        @endif
    </div>
</div>
@endsection
