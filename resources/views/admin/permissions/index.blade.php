@extends('layouts.app')

@section('title', 'Permission Management')

@section('content')

    <div class="d-flex justify-content-between align-items-center mb-6">
        <div>
            <h4 class="mb-0">Permission Management</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Permissions</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('permissions.create') }}" class="btn btn-primary">
            <i class="ti ti-plus me-1"></i> Tambah Permission
        </a>
    </div>

    <div class="card card-lg">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-centered mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nama Permission</th>
                            <th>Slug</th>
                            <th>Role yang Memiliki</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($permissions as $permission)
                            <tr>
                                <td>{{ $permissions->firstItem() + $loop->index }}</td>
                                <td>
                                    <strong>{{ $permission->name }}</strong>
                                </td>
                                <td>
                                    <code class="bg-light px-2 py-1 rounded">{{ $permission->slug }}</code>
                                </td>
                                <td>
                                    @forelse ($permission->roles as $role)
                                        <span class="badge bg-primary-subtle text-primary-emphasis me-1">
                                            {{ $role->name }}
                                        </span>
                                    @empty
                                        <span class="text-muted">Tidak ada role</span>
                                    @endforelse
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('permissions.edit', $permission) }}"
                                            class="btn btn-sm btn-white">
                                            <i class="ti ti-edit"></i>
                                        </a>
                                        <form action="{{ route('permissions.destroy', $permission) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn btn-sm btn-white text-danger"
                                                data-confirm="Yakin hapus permission {{ $permission->name }}?">
                                                <i class="ti ti-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-6">
                                    Belum ada permission.
                                    <a href="{{ route('permissions.create') }}">Tambah sekarang</a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($permissions->hasPages())
                <div class="px-4 py-3 border-top">
                    {{ $permissions->links() }}
                </div>
            @endif
        </div>
    </div>

@endsection