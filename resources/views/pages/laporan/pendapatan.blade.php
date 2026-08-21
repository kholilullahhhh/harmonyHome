@extends('layouts/layoutMaster')

@section('title', 'Laporan Pendapatan')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold mb-1">Laporan Pendapatan</h4>
    <p class="text-muted mb-4">Pendapatan dari pembayaran yang telah lunas.</p>

    <div class="row g-3 mb-4">
        <div class="col-6 col-xl-3">
            <div class="card border-primary">
                <div class="card-body py-3">
                    <span class="text-muted small text-uppercase">Total Pendapatan</span>
                    <h5 class="mb-0 mt-1 text-primary">Rp{{ number_format($totalPendapatan, 0, ',', '.') }}</h5>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="card">
                <div class="card-body py-3">
                    <span class="text-muted small text-uppercase">Jumlah Transaksi Lunas</span>
                    <h5 class="mb-0 mt-1">{{ number_format($jumlahTransaksi) }}</h5>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header"><h5 class="mb-0">Pendapatan per Bulan</h5></div>
        <div class="card-body border-bottom pt-0 pb-3">
            <form method="GET" action="{{ route('laporan.pendapatan.index') }}" class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small mb-1">Dari Tanggal</label>
                    <input type="date" name="date_from" value="{{ $from->toDateString() }}" class="form-control">
                </div>
                <div class="col-md-3">
                    <label class="form-label small mb-1">Sampai Tanggal</label>
                    <input type="date" name="date_to" value="{{ $to->toDateString() }}" class="form-control">
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
                        <th>Bulan</th>
                        <th>Jumlah Transaksi</th>
                        <th>Total Pendapatan</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse ($perBulan as $baris)
                        <tr>
                            <td><strong>{{ $baris->bulan }}</strong></td>
                            <td>{{ number_format($baris->jumlah) }}</td>
                            <td>Rp{{ number_format($baris->total, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="text-center py-4 text-muted">Belum ada pendapatan pada periode ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><h5 class="mb-0">Pendapatan per Kost</h5></div>
        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Kost</th>
                        <th>Jumlah Transaksi</th>
                        <th>Total Pendapatan</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse ($perKost as $baris)
                        <tr>
                            <td><strong>{{ $baris->kost }}</strong></td>
                            <td>{{ number_format($baris->jumlah) }}</td>
                            <td>Rp{{ number_format($baris->total, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="text-center py-4 text-muted">Belum ada data.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
