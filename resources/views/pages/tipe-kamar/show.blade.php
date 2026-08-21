@extends('layouts/layoutMaster')

@section('title', 'Detail Tipe Kamar')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold mb-4">
        <span class="text-muted fw-light">Master Data / <a href="{{ route('tipe-kamar.index') }}">Tipe Kamar</a> /</span> Detail
    </h4>

    <div class="row">
        <div class="col-md-6">
            <div class="card p-4">
                <div class="d-flex justify-content-between align-items-start border-bottom pb-3 mb-3">
                    <h4 class="mb-0 text-primary">{{ $data->name }}</h4>
                    <div class="d-flex gap-2">
                        <a href="{{ route('tipe-kamar.edit', $data->id) }}" class="btn btn-primary">Edit</a>
                        <a href="{{ route('tipe-kamar.index') }}" class="btn btn-outline-secondary">Kembali</a>
                    </div>
                </div>
                <table class="table table-borderless">
                    <tr>
                        <th width="150" class="text-muted">Deskripsi</th>
                        <td>: {{ $data->description ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Dibuat Pada</th>
                        <td>: {{ $data->created_at->format('d F Y H:i') }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
