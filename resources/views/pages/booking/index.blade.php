@extends('layouts/layoutMaster')

@section('title', 'Data Booking')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold mb-1">Transaksi</h4>
    <p class="text-muted mb-4">Kelola booking member dan guest.</p>

    <div class="card">
        <div class="card-header"><h5 class="mb-0">Data Booking</h5></div>
        <div class="card-body border-bottom pt-0 pb-3">
            <form method="GET" action="{{ route('booking.index') }}" class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small mb-1">Booking Type</label>
                    <select name="type" class="form-select">
                        <option value="">All</option>
                        <option value="member" {{ $filterType === 'member' ? 'selected' : '' }}>Member</option>
                        <option value="guest" {{ $filterType === 'guest' ? 'selected' : '' }}>Guest</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small mb-1">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All</option>
                        @foreach (['pending', 'confirmed', 'active', 'completed', 'cancelled', 'rejected', 'expired'] as $s)
                            <option value="{{ $s }}" {{ $filterStatus === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small mb-1">Payment</label>
                    <select name="payment" class="form-select">
                        <option value="">All</option>
                        @foreach (['pending', 'paid', 'failed', 'expired'] as $s)
                            <option value="{{ $s }}" {{ $filterPayment === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
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
                        <th>Kode</th>
                        <th>Tipe</th>
                        <th>Penyewa</th>
                        <th>Kost / Kamar</th>
                        <th>Periode</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Bayar</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse ($data as $item)
                        @php
                            $statusBadge = [
                                'pending' => 'warning',
                                'confirmed' => 'info',
                                'active' => 'primary',
                                'completed' => 'success',
                                'cancelled' => 'secondary',
                                'rejected' => 'danger',
                                'expired' => 'dark',
                            ][$item->status] ?? 'secondary';
                            $pay = $item->latestPayment;
                            $payBadge = ['pending' => 'warning', 'paid' => 'success', 'failed' => 'danger', 'expired' => 'dark'][$pay?->status ?? 'pending'] ?? 'secondary';
                        @endphp
                        <tr>
                            <td><a href="{{ route('booking.show', $item->id) }}"><strong>{{ $item->booking_code }}</strong></a></td>
                            <td><span class="badge bg-label-{{ $item->booking_type === 'guest' ? 'warning' : 'primary' }}">{{ strtoupper($item->booking_type) }}</span></td>
                            <td>
                                <span class="d-block">{{ $item->customerName() }}</span>
                                <small class="text-muted">{{ $item->customerEmail() }}</small>
                            </td>
                            <td>{{ $item->kost->name ?? '-' }}<br><small class="text-muted">Kamar {{ $item->kamar->number ?? '-' }}</small></td>
                            <td>{{ $item->start_date->format('d M Y') }}<br><small class="text-muted">{{ $item->duration_months }} bln</small></td>
                            <td>Rp{{ number_format($item->total_amount, 0, ',', '.') }}</td>
                            <td><span class="badge bg-label-{{ $statusBadge }}">{{ ucfirst($item->status) }}</span></td>
                            <td><span class="badge bg-label-{{ $payBadge }}">{{ ucfirst($pay?->status ?? 'pending') }}</span></td>
                            <td><a href="{{ route('booking.show', $item->id) }}" class="btn btn-sm btn-icon btn-outline-secondary"><i class="bx bx-show"></i></a></td>
                        </tr>
                    @empty
                        <tr><td colspan="9" class="text-center py-4 text-muted">Belum ada booking.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
