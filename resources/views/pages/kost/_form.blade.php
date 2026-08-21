@php
    $isEdit = isset($data);
    $selectedFasilitas = old('fasilitas', $isEdit ? $data->fasilitas->pluck('id')->all() : []);
@endphp

<form action="{{ $isEdit ? route('kost.update', $data->id) : route('kost.store') }}" method="POST"
    enctype="multipart/form-data">
    @csrf
    @if ($isEdit)
        @method('PUT')
    @endif
    <div class="row g-4">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header"><h5 class="mb-0">Informasi Kost</h5></div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label" for="name">Nama Kost <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                                name="name" value="{{ old('name', $data->name ?? '') }}" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="status">Status</label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status">
                                <option value="active" {{ old('status', $data->status ?? 'active') === 'active' ? 'selected' : '' }}>Aktif</option>
                                <option value="inactive" {{ old('status', $data->status ?? '') === 'inactive' ? 'selected' : '' }}>Non-Aktif</option>
                            </select>
                            @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="user_id">Pemilik <span class="text-danger">*</span></label>
                            <select class="form-select @error('user_id') is-invalid @enderror" id="user_id"
                                name="user_id" required>
                                <option value="">-- Pilih Pemilik --</option>
                                @foreach ($pemilikList as $pemilik)
                                    <option value="{{ $pemilik->id }}" {{ old('user_id', $data->user_id ?? '') == $pemilik->id ? 'selected' : '' }}>
                                        {{ $pemilik->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('user_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="lokasi_id">Lokasi/Wilayah <span class="text-danger">*</span></label>
                            <select class="form-select @error('lokasi_id') is-invalid @enderror" id="lokasi_id"
                                name="lokasi_id" required>
                                <option value="">-- Pilih Lokasi --</option>
                                @foreach ($lokasiList as $lokasi)
                                    <option value="{{ $lokasi->id }}" {{ old('lokasi_id', $data->lokasi_id ?? '') == $lokasi->id ? 'selected' : '' }}>
                                        {{ $lokasi->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('lokasi_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="phone">Nomor Telepon</label>
                            <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone"
                                name="phone" value="{{ old('phone', $data->phone ?? '') }}">
                            @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-3">
                            <label class="form-label" for="latitude">Latitude</label>
                            <input type="text" class="form-control @error('latitude') is-invalid @enderror"
                                id="latitude" name="latitude" value="{{ old('latitude', $data->latitude ?? '') }}"
                                placeholder="-5.1470">
                            @error('latitude')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-3">
                            <label class="form-label" for="longitude">Longitude</label>
                            <input type="text" class="form-control @error('longitude') is-invalid @enderror"
                                id="longitude" name="longitude" value="{{ old('longitude', $data->longitude ?? '') }}"
                                placeholder="119.4320">
                            @error('longitude')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header"><h5 class="mb-0">Alamat</h5></div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label" for="address">Alamat Lengkap <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('address') is-invalid @enderror" id="address"
                                name="address" rows="2" required>{{ old('address', $data->address ?? '') }}</textarea>
                            @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="kelurahan">Kelurahan</label>
                            <input type="text" class="form-control @error('kelurahan') is-invalid @enderror"
                                id="kelurahan" name="kelurahan" value="{{ old('kelurahan', $data->kelurahan ?? '') }}">
                            @error('kelurahan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="kecamatan">Kecamatan</label>
                            <input type="text" class="form-control @error('kecamatan') is-invalid @enderror"
                                id="kecamatan" name="kecamatan" value="{{ old('kecamatan', $data->kecamatan ?? '') }}">
                            @error('kecamatan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="kode_pos">Kode Pos</label>
                            <input type="text" class="form-control @error('kode_pos') is-invalid @enderror"
                                id="kode_pos" name="kode_pos" value="{{ old('kode_pos', $data->kode_pos ?? '') }}">
                            @error('kode_pos')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h5 class="mb-0">Deskripsi & Peraturan</h5></div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label" for="description">Deskripsi</label>
                            <textarea class="form-control @error('description') is-invalid @enderror"
                                id="description" name="description" rows="3">{{ old('description', $data->description ?? '') }}</textarea>
                            @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-8">
                            <label class="form-label" for="rules">Peraturan Kost</label>
                            <textarea class="form-control @error('rules') is-invalid @enderror" id="rules"
                                name="rules" rows="3" placeholder="Satu peraturan per baris">{{ old('rules', $data->rules ?? '') }}</textarea>
                            @error('rules')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="access_hours">Jam Akses</label>
                            <input type="text" class="form-control @error('access_hours') is-invalid @enderror"
                                id="access_hours" name="access_hours" value="{{ old('access_hours', $data->access_hours ?? '') }}"
                                placeholder="24 Jam">
                            @error('access_hours')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header"><h5 class="mb-0">Foto Utama</h5></div>
                <div class="card-body">
                    @php
                        $coverUrl = $isEdit && $data->cover ? Storage::url($data->cover) : null;
                    @endphp
                    <img src="{{ old('cover_preview', $coverUrl) }}" alt="preview" id="cover-preview"
                        class="img-fluid rounded mb-3 {{ $coverUrl ? '' : 'd-none' }}"
                        style="max-height: 200px; object-fit: cover; width: 100%;">
                    <input type="file" class="form-control @error('cover') is-invalid @enderror" id="cover"
                        name="cover" accept="image/jpeg,image/png,image/webp">
                    <div class="form-text">JPG/PNG/WEBP, maksimal 2MB.</div>
                    @error('cover')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h5 class="mb-0">Fasilitas</h5></div>
                <div class="card-body">
                    <div class="row g-2">
                        @foreach ($fasilitasList as $fasilitas)
                            <div class="col-6">
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
                    @error('fasilitas.*')<div class="text-danger small mt-2">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-grow-1">
                    {{ $isEdit ? 'Update Kost' : 'Simpan Kost' }}
                </button>
                <a href="{{ route('kost.index') }}" class="btn btn-outline-secondary">Batal</a>
            </div>
        </div>
    </div>
</form>
