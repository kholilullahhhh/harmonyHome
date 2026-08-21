@php
    $isEdit = isset($data);
    $selectedFasilitas = old('fasilitas', $isEdit ? $data->fasilitas->pluck('id')->all() : []);
@endphp

<form action="{{ $isEdit ? route('kamar.update', $data->id) : route('kamar.store') }}" method="POST"
    enctype="multipart/form-data">
    @csrf
    @if ($isEdit)
        @method('PUT')
    @endif
    <div class="row g-4">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header"><h5 class="mb-0">Informasi Kamar</h5></div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" for="kost_id">Kost <span class="text-danger">*</span></label>
                            <select class="form-select @error('kost_id') is-invalid @enderror" id="kost_id"
                                name="kost_id" required>
                                <option value="">-- Pilih Kost --</option>
                                @foreach ($kostList as $kost)
                                    <option value="{{ $kost->id }}"
                                        {{ old('kost_id', $data->kost_id ?? $selectedKostId ?? '') == $kost->id ? 'selected' : '' }}>
                                        {{ $kost->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('kost_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="tipe_kamar_id">Tipe Kamar <span class="text-danger">*</span></label>
                            <select class="form-select @error('tipe_kamar_id') is-invalid @enderror"
                                id="tipe_kamar_id" name="tipe_kamar_id" required>
                                <option value="">-- Pilih Tipe --</option>
                                @foreach ($tipeList as $tipe)
                                    <option value="{{ $tipe->id }}" {{ old('tipe_kamar_id', $data->tipe_kamar_id ?? '') == $tipe->id ? 'selected' : '' }}>
                                        {{ $tipe->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('tipe_kamar_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="number">Nomor Kamar <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('number') is-invalid @enderror" id="number"
                                name="number" value="{{ old('number', $data->number ?? '') }}" placeholder="A-101" required>
                            @error('number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="price_monthly">Harga per Bulan (Rp) <span class="text-danger">*</span></label>
                            <input type="number" min="0" step="1000"
                                class="form-control @error('price_monthly') is-invalid @enderror" id="price_monthly"
                                name="price_monthly" value="{{ old('price_monthly', $data->price_monthly ?? '') }}" required>
                            @error('price_monthly')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-2">
                            <label class="form-label" for="floor">Lantai</label>
                            <input type="number" min="0" class="form-control @error('floor') is-invalid @enderror"
                                id="floor" name="floor" value="{{ old('floor', $data->floor ?? '') }}">
                            @error('floor')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-3">
                            <label class="form-label" for="size">Ukuran</label>
                            <input type="text" class="form-control @error('size') is-invalid @enderror" id="size"
                                name="size" value="{{ old('size', $data->size ?? '') }}" placeholder="3x4 m">
                            @error('size')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="status">Status</label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status">
                                @foreach (['available' => 'Available', 'reserved' => 'Reserved', 'occupied' => 'Occupied', 'maintenance' => 'Maintenance'] as $val => $label)
                                    <option value="{{ $val }}" {{ old('status', $data->status ?? 'available') === $val ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="description">Deskripsi</label>
                            <textarea class="form-control @error('description') is-invalid @enderror"
                                id="description" name="description" rows="3">{{ old('description', $data->description ?? '') }}</textarea>
                            @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h5 class="mb-0">Fasilitas Kamar</h5></div>
                <div class="card-body">
                    <div class="row g-2">
                        @foreach ($fasilitasList as $fasilitas)
                            <div class="col-md-3 col-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="fasilitas[]"
                                        value="{{ $fasilitas->id }}" id="fasilitas-{{ $fasilitas->id }}"
                                        {{ in_array($fasilitas->id, $selectedFasilitas) ? 'checked' : '' }}>
                                    <label class="form-check-label small"
                                        for="fasilitas-{{ $fasilitas->id }}">{{ $fasilitas->name }}</label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header"><h5 class="mb-0">Foto Kamar</h5></div>
                <div class="card-body">
                    @php
                        $photoUrl = $isEdit && $data->photo ? Storage::url($data->photo) : null;
                    @endphp
                    <img src="{{ $photoUrl }}" alt="preview" id="photo-preview"
                        class="img-fluid rounded mb-3 {{ $photoUrl ? '' : 'd-none' }}"
                        style="max-height: 200px; object-fit: cover; width: 100%;">
                    <input type="file" class="form-control @error('photo') is-invalid @enderror" id="photo"
                        name="photo" accept="image/jpeg,image/png,image/webp">
                    <div class="form-text">JPG/PNG/WEBP, maksimal 2MB.</div>
                    @error('photo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-grow-1">
                    {{ $isEdit ? 'Update Kamar' : 'Simpan Kamar' }}
                </button>
                <a href="{{ route('kamar.index', ['kost_id' => $data->kost_id ?? $selectedKostId]) }}"
                    class="btn btn-outline-secondary">Batal</a>
            </div>
        </div>
    </div>
</form>
