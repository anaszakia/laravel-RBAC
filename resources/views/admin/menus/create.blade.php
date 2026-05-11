@extends('layouts.app')

@section('title', 'Tambah Menu')

@section('content')

    <div class="d-flex justify-content-between align-items-center mb-6">
        <div>
            <h4 class="mb-0">Tambah Menu</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('menus.index') }}">Menus</a></li>
                    <li class="breadcrumb-item active">Tambah</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('menus.index') }}" class="btn btn-white">
            <i class="ti ti-arrow-left me-1"></i> Kembali
        </a>
    </div>

    <div class="row">
        <div class="col-xl-8">
            <div class="card card-lg">
                <div class="card-body">
                    <form action="{{ route('menus.store') }}" method="POST">
                        @csrf

                        <div class="mb-4">
                            <label class="form-label">Nama Menu <span class="text-danger">*</span></label>
                            <input type="text" name="name"
                                class="form-control @error('name') is-invalid @enderror"
                                value="{{ old('name') }}" placeholder="Contoh: Dashboard" required />
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label">URL</label>
                            <input type="text" name="url"
                                class="form-control @error('url') is-invalid @enderror"
                                value="{{ old('url') }}" placeholder="Contoh: /dashboard" />
                            @error('url')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Kosongkan jika menu ini hanya sebagai header/grup.</div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Icon</label>
                            <input type="text" name="icon"
                                class="form-control @error('icon') is-invalid @enderror"
                                value="{{ old('icon') }}" placeholder="Contoh: ti ti-dashboard" />
                            @error('icon')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                Gunakan class icon dari
                                <a href="https://tabler.io/icons" target="_blank">Tabler Icons</a>.
                                Contoh: <code>ti ti-home</code>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label">Parent Menu</label>
                                <select name="parent_id" class="form-select">
                                    <option value="">-- Tidak ada (menu utama) --</option>
                                    @foreach ($parents as $parent)
                                        <option value="{{ $parent->id }}"
                                            {{ old('parent_id') == $parent->id ? 'selected' : '' }}>
                                            {{ $parent->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Urutan</label>
                                <input type="number" name="order"
                                    class="form-control"
                                    value="{{ old('order', 0) }}" min="0" />
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Role yang bisa akses</label>
                            <div class="d-flex flex-wrap gap-3">
                                @foreach ($roles as $role)
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox"
                                            name="roles[]" value="{{ $role->id }}"
                                            id="role_{{ $role->id }}"
                                            {{ in_array($role->id, old('roles', [])) ? 'checked' : '' }} />
                                        <label class="form-check-label" for="role_{{ $role->id }}">
                                            {{ $role->name }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                            <div class="form-text">Kosongkan = semua role bisa akses.</div>
                        </div>

                        <div class="mb-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox"
                                    name="is_active" id="is_active"
                                    {{ old('is_active', true) ? 'checked' : '' }} />
                                <label class="form-check-label" for="is_active">Menu Aktif</label>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="ti ti-check me-1"></i> Simpan
                            </button>
                            <a href="{{ route('menus.index') }}" class="btn btn-white">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Preview icon --}}
        <div class="col-xl-4">
            <div class="card card-lg">
                <div class="card-body">
                    <h6 class="mb-4">Preview Icon</h6>
                    <div class="d-flex align-items-center gap-3 p-4 bg-light rounded-3">
                        <i id="iconPreview" class="ti ti-menu-2 fs-3"></i>
                        <span id="namePreview" class="fw-semibold">Nama Menu</span>
                    </div>
                    <div class="mt-3">
                        <small class="text-muted">Ketik icon dan nama untuk preview</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script>
    // Preview icon & nama
    document.querySelector('[name="icon"]').addEventListener('input', function() {
        document.getElementById('iconPreview').className = this.value || 'ti ti-menu-2 fs-3';
    });
    document.querySelector('[name="name"]').addEventListener('input', function() {
        document.getElementById('namePreview').textContent = this.value || 'Nama Menu';
    });
</script>
@endpush