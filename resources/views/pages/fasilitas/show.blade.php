@extends('layouts/layoutMaster')

@section('title', 'Detail Fasilitas')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold mb-4">
        <span class="text-muted fw-light">Master Data / <a href="{{ route('fasilitas.index') }}">Fasilitas</a> /</span> Detail
    </h4>

    <div class="row">
        <div class="col-md-6">
            <div class="card p-4">
                <div class="d-flex justify-content-between align-items-start border-bottom pb-3 mb-3">
                    <div>
                        <h4 class="mb-1 text-primary">
                            @if ($data->icon)<i class="{{ $data->icon }} me-2"></i>@endif{{ $data->name }}
                        </h4>
                        <p class="text-muted mb-0">Slug: <code>{{ $data->slug }}</code></p>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('fasilitas.edit', $data->id) }}" class="btn btn-primary">Edit</a>
                        <a href="{{ route('fasilitas.index') }}" class="btn btn-outline-secondary">Kembali</a>
                    </div>
                </div>
                <table class="table table-borderless">
                    <tr>
                        <th width="150" class="text-muted">Dibuat Pada</th>
                        <td>: {{ $data->created_at->format('d F Y H:i') }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
