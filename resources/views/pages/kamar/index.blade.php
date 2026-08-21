@extends('layouts/layoutMaster')

@section('title', 'Data Kamar')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold mb-1">Master Data</h4>
    <p class="text-muted mb-4">Kelola kamar untuk setiap kost.</p>

    <div class="card">
        <div class="card-header d-flex flex-wrap justify-content-between gap-2 align-items-center">
            <h5 class="mb-0">Data Kamar @if($filterKostId) — {{ \App\Models\Kost::find($filterKostId)?->name }} @endif</h5>
            <a href="{{ route('kamar.create', $filterKostId ? ['kost_id' => $filterKostId] : []) }}" class="btn btn-primary">
                <i class="bx bx-plus me-1"></i>Tambah Kamar
            </a>
        </div>
        <div class="card-body border-bottom pt-0 pb-3">
            <form method="GET" action="{{ route('kamar.index') }}" class="row g-2 align-items-end">
                <div class="col-md-5">
                    <label class="form-label small mb-1">Filter Kost</label>
                    <select name="kost_id" class="form-select">
                        <option value="">Semua Kost</option>
                        @foreach ($kostList as $kost)
                            <option value="{{ $kost->id }}" {{ $filterKostId == $kost->id ? 'selected' : '' }}>{{ $kost->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label small mb-1">Filter Status</label>
                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        @foreach (['available' => 'Available', 'reserved' => 'Reserved', 'occupied' => 'Occupied', 'maintenance' => 'Maintenance'] as $val => $label)
                            <option value="{{ $val }}" {{ $filterStatus === $val ? 'selected' : '' }}>{{ $label }}</option>
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
                        <th>Kost</th>
                        <th>No. Kamar</th>
                        <th>Tipe</th>
                        <th>Harga/Bulan</th>
                        <th>Lantai</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse ($data as $item)
                        @php
                            $badge = [
                                'available' => 'success',
                                'reserved' => 'warning',
                                'occupied' => 'info',
                                'maintenance' => 'danger',
                            ][$item->status] ?? 'secondary';
                        @endphp
                        <tr>
                            <td>{{ $item->kost->name ?? '-' }}</td>
                            <td><strong>{{ $item->number }}</strong></td>
                            <td>{{ $item->tipeKamar->name ?? '-' }}</td>
                            <td>Rp{{ number_format($item->price_monthly, 0, ',', '.') }}</td>
                            <td>{{ $item->floor ?? '-' }}</td>
                            <td><span class="badge bg-label-{{ $badge }}">{{ ucfirst($item->status) }}</span></td>
                            <td>
                                <div class="d-flex gap-1">
                                    <a href="{{ route('kamar.show', $item->id) }}" class="btn btn-sm btn-icon btn-outline-secondary" title="Detail"><i class="bx bx-show"></i></a>
                                    <a href="{{ route('kamar.edit', $item->id) }}" class="btn btn-sm btn-icon btn-outline-primary" title="Edit"><i class="bx bx-edit"></i></a>
                                    <button type="button" class="btn btn-sm btn-icon btn-outline-danger delete-record"
                                        data-url="{{ route('kamar.destroy', $item->id) }}"
                                        data-message="Yakin ingin menghapus kamar {{ $item->number }}?" title="Hapus"><i class="bx bx-trash"></i></button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center py-4 text-muted">Belum ada data kamar.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('page-script')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        $('.delete-record').on('click', function() {
            let url = $(this).data('url');
            let message = $(this).data('message');

            window.AlertHandler.confirm(
                'Hapus Kamar?',
                message,
                'Ya, Hapus!',
                function() {
                    $.ajax({
                        url: url,
                        method: 'DELETE',
                        dataType: 'json',
                        headers: { 'Accept': 'application/json' },
                        data: { _token: '{{ csrf_token() }}' },
                        success: function(response) {
                            window.AlertHandler.handle(response);
                            setTimeout(() => { window.location.reload(); }, 1500);
                        },
                        error: function(xhr) {
                            window.AlertHandler.handle(xhr.responseJSON);
                        }
                    });
                }
            );
        });
    });
</script>
@endsection
