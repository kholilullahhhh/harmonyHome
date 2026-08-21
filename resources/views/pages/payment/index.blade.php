@extends('layouts/layoutMaster')

@section('title', 'Data Pembayaran')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold mb-1">Transaksi</h4>
    <p class="text-muted mb-4">Kelola invoice dan status pembayaran.</p>

    <div class="card">
        <div class="card-header"><h5 class="mb-0">Data Pembayaran</h5></div>
        <div class="card-body border-bottom pt-0 pb-3">
            <form method="GET" action="{{ route('payment.index') }}" class="row g-2 align-items-end">
                <div class="col-md-4">
                    <label class="form-label small mb-1">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All</option>
                        @foreach (['pending', 'paid', 'failed', 'expired'] as $s)
                            <option value="{{ $s }}" {{ $filterStatus === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-outline-primary w-100"><i class="bx bx-filter me-1"></i>Terapkan</button>
                </div>
            </form>
        </div>
        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Invoice</th>
                        <th>Booking</th>
                        <th>Tipe</th>
                        <th>Penyewa</th>
                        <th>Nominal</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse ($data as $item)
                        @php
                            $badge = ['pending' => 'warning', 'paid' => 'success', 'failed' => 'danger', 'expired' => 'dark'][$item->status] ?? 'secondary';
                        @endphp
                        <tr>
                            <td><a href="{{ route('payment.show', $item->id) }}"><strong>{{ $item->invoice_no }}</strong></a></td>
                            <td>{{ $item->booking->booking_code ?? '-' }}</td>
                            <td><span class="badge bg-label-{{ $item->booking?->booking_type === 'guest' ? 'warning' : 'primary' }}">{{ strtoupper($item->booking->booking_type ?? '-') }}</span></td>
                            <td>{{ $item->booking->customerName() ?? '-' }}</td>
                            <td>Rp{{ number_format($item->amount, 0, ',', '.') }}</td>
                            <td><span class="badge bg-label-{{ $badge }}">{{ ucfirst($item->status) }}</span></td>
                            <td><a href="{{ route('payment.show', $item->id) }}" class="btn btn-sm btn-icon btn-outline-secondary"><i class="bx bx-show"></i></a></td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center py-4 text-muted">Belum ada pembayaran.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
