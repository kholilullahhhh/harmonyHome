@extends('layouts/layoutMaster')

@section('title', 'Manajemen Kost')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">
            <span class="text-muted fw-light">Master Data /</span> Data Kost
        </h4>
        <a href="{{ route('kost.create') }}" class="btn btn-primary">
            <i class="ri-add-line me-1"></i> Tambah Kost
        </a>
    </div>

    <div class="card">
        <div class="card-header border-bottom">
            <h5 class="card-title mb-0">Daftar Kost</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th style="width: 50px">#</th>
                        <th style="width: 70px">Cover</th>
                        <th>Nama Kost</th>
                        <th>Pemilik</th>
                        <th>Lokasi</th>
                        <th>Kamar</th>
                        <th>Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($data as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                @if ($item->cover)
                                    <img src="{{ Storage::url($item->cover) }}" alt="cover" class="rounded"
                                        style="width: 50px; height: 50px; object-fit: cover;">
                                @else
                                    <div class="bg-light rounded d-flex align-items-center justify-content-center"
                                        style="width: 50px; height: 50px">
                                        <i class="ri-building-2-line text-muted"></i>
                                    </div>
                                @endif
                            </td>
                            <td>
                                <span class="fw-bold">{{ $item->name }}</span><br>
                                <small class="text-muted">{{ $item->address }}</small>
                            </td>
                            <td>{{ $item->pemilik?->name ?? '-' }}</td>
                            <td>{{ $item->lokasi?->name ?? '-' }}</td>
                            <td><span class="badge bg-label-info">{{ $item->kamar_count }} kamar</span></td>
                            <td>
                                @if ($item->status === 'active')
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-secondary">Non-Aktif</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="{{ route('kost.show', $item->id) }}" class="btn btn-sm btn-outline-info"
                                        title="Detail"><i class="ri-eye-line"></i></a>
                                    <a href="{{ route('kamar.index', ['kost_id' => $item->id]) }}"
                                        class="btn btn-sm btn-outline-success" title="Kelola Kamar"><i
                                            class="ri-door-open-line"></i></a>
                                    <a href="{{ route('kost.edit', $item->id) }}" class="btn btn-sm btn-outline-primary"
                                        title="Edit"><i class="ri-pencil-line"></i></a>
                                    <button type="button" class="btn btn-sm btn-outline-danger delete-record"
                                        data-id="{{ $item->id }}" data-name="{{ $item->name }}"
                                        title="Hapus"><i class="ri-delete-bin-line"></i></button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="ri-building-2-line ri-3x mb-2"></i>
                                    <p>Belum ada data kost yang tersedia.</p>
                                </div>
                            </td>
                        </tr>
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
            let id = $(this).data('id');
            let name = $(this).data('name');
            let url = "{{ route('kost.destroy', ':id') }}".replace(':id', id);

            window.AlertHandler.confirm(
                'Hapus Kost?',
                `Apakah Anda yakin ingin menghapus "${name}"? Seluruh data kamar di dalamnya juga tidak akan tampil.`,
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
