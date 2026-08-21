@extends('layouts/layoutMaster')

@section('title', 'Manajemen Tipe Kamar')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">
            <span class="text-muted fw-light">Master Data /</span> Tipe Kamar
        </h4>
        <a href="{{ route('tipe-kamar.create') }}" class="btn btn-primary">
            <i class="ri-add-line me-1"></i> Tambah Tipe Kamar
        </a>
    </div>

    <div class="card">
        <div class="card-header border-bottom">
            <h5 class="card-title mb-0">Daftar Tipe Kamar</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th style="width: 50px">#</th>
                        <th>Nama</th>
                        <th>Deskripsi</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($data as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td><span class="fw-bold">{{ $item->name }}</span></td>
                            <td>{{ Str::limit($item->description, 80) }}</td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="{{ route('tipe-kamar.show', $item->id) }}"
                                        class="btn btn-sm btn-outline-info"><i class="ri-eye-line"></i></a>
                                    <a href="{{ route('tipe-kamar.edit', $item->id) }}"
                                        class="btn btn-sm btn-outline-primary"><i class="ri-pencil-line"></i></a>
                                    <button type="button" class="btn btn-sm btn-outline-danger delete-record"
                                        data-id="{{ $item->id }}" data-name="{{ $item->name }}">
                                        <i class="ri-delete-bin-line"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="ri-layout-masonry-line ri-3x mb-2"></i>
                                    <p>Belum ada tipe kamar yang tersedia.</p>
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
            let url = "{{ route('tipe-kamar.destroy', ':id') }}".replace(':id', id);

            window.AlertHandler.confirm(
                'Hapus Tipe Kamar?',
                `Apakah Anda yakin ingin menghapus "${name}"?`,
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
