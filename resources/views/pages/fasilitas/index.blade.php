@extends('layouts/layoutMaster')

@section('title', 'Manajemen Fasilitas')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">
            <span class="text-muted fw-light">Master Data /</span> Fasilitas
        </h4>
        <a href="{{ route('fasilitas.create') }}" class="btn btn-primary">
            <i class="ri-add-line me-1"></i> Tambah Fasilitas
        </a>
    </div>

    <div class="card">
        <div class="card-header border-bottom">
            <h5 class="card-title mb-0">Daftar Fasilitas</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th style="width: 50px">#</th>
                        <th style="width: 70px">Icon</th>
                        <th>Nama</th>
                        <th>Slug</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($data as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                @if ($item->icon)
                                    <i class="{{ $item->icon }} ri-lg text-primary"></i>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td><span class="fw-bold">{{ $item->name }}</span></td>
                            <td><code>{{ $item->slug }}</code></td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="{{ route('fasilitas.show', $item->id) }}"
                                        class="btn btn-sm btn-outline-info"><i class="ri-eye-line"></i></a>
                                    <a href="{{ route('fasilitas.edit', $item->id) }}"
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
                            <td colspan="5" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="ri-checkbox-multiple-line ri-3x mb-2"></i>
                                    <p>Belum ada fasilitas yang tersedia.</p>
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
            let url = "{{ route('fasilitas.destroy', ':id') }}".replace(':id', id);

            window.AlertHandler.confirm(
                'Hapus Fasilitas?',
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
