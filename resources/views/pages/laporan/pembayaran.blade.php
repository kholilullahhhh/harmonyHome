@extends('layouts/layoutMaster')

@section('title', 'Laporan Pembayaran')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold mb-1">Laporan Pembayaran</h4>
    <p class="text-muted mb-4">Rekapitulasi invoice dan status pembayaran.</p>

    <div class="row g-3 mb-4">
        @php
            $kartu = ['paid' => ['success', 'bx-check-circle'], 'pending' => ['warning', 'bx-time-five'], 'failed' => ['danger', 'bx-x-circle'], 'expired' => ['dark', 'bx-timer']];
        @endphp
        @foreach ($kartu as $status => [$warna, $ikon])
            <div class="col-6 col-xl-3">
                <div class="card">
                    <div class="card-body py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="text-muted small text-uppercase">{{ $status }}</span>
                                <h5 class="mb-0 mt-1">Rp{{ number_format($ringkasan->get($status)['total'] ?? 0, 0, ',', '.') }}</h5>
                                <small class="text-muted">{{ number_format($ringkasan->get($status)['count'] ?? 0) }} invoice</small>
                            </div>
                            <span class="badge bg-label-{{ $warna }} p-2"><i class="bx {{ $ikon }} bx-sm"></i></span>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="card">
        <div class="card-header"><h5 class="mb-0">Detail Pembayaran</h5></div>
        <div class="card-body border-bottom pt-0 pb-3">
            <form method="GET" action="{{ route('laporan.pembayaran.index') }}" class="row g-2 align-items-end">
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
                        @foreach (['pending', 'paid', 'failed', 'expired'] as $s)
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
                        <th>Invoice</th>
                        <th>Booking</th>
                        <th>Kost</th>
                        <th>Tipe</th>
                        <th>Penyewa</th>
                        <th>Nominal</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse ($data as $item)
                        @php
                            $badge = ['pending' => 'warning', 'paid' => 'success', 'failed' => 'danger', 'expired' => 'dark'][$item->status] ?? 'secondary';
                        @endphp
                        <tr>
                            <td><strong>{{ $item->invoice_no }}</strong></td>
                            <td>{{ $item->booking->booking_code ?? '-' }}</td>
                            <td>{{ $item->booking->kost->name ?? '-' }}</td>
                            <td><span class="badge bg-label-{{ $item->booking?->booking_type === 'guest' ? 'warning' : 'primary' }}">{{ strtoupper($item->booking->booking_type ?? '-') }}</span></td>
                            <td>{{ $item->booking?->customerName() ?? '-' }}</td>
                            <td>Rp{{ number_format($item->amount, 0, ',', '.') }}</td>
                            <td><span class="badge bg-label-{{ $badge }}">{{ ucfirst($item->status) }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center py-4 text-muted">Tidak ada pembayaran pada periode ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($data->isNotEmpty())
            <div class="card-footer text-muted small">
                Menampilkan {{ $data->count() }} pembayaran ({{ $from->format('d M Y') }} — {{ $to->format('d M Y') }}).
            </div>
        @endif
    </div>
</div>
@endsection
