@extends('layouts.app')

@section('content')
<style>
    body {
        background:rgb(236, 244, 239) !important;
    }

    .btn-outline-custom {
        font-weight: 500;
        padding: 6px 12px;
        border-width: 1.5px;
    }

    .btn-outline-warning-custom {
        color: #ffc107;
        border-color: #ffc107;
    }

    .btn-outline-warning-custom:hover {
        background-color: #ffc107;
        color: white;
    }

    .btn-outline-danger-custom {
        color: #dc3545;
        border-color: #dc3545;
    }

    .btn-outline-danger-custom:hover {
        background-color: #dc3545;
        color: white;
    }
</style>

<div class="page-content">
    <section class="row">
        <div class="col-12">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="card shadow-sm border-0">
                <div class="card-header">
                    <h4 class="card-title"><i class="bi bi-people-fill me-2"></i>Manajemen User</h4>
                    <p class="text-subtitle text-muted mb-0">Halaman untuk melihat dan mengelola akun user yang terdaftar dalam sistem.</p>
                </div>

                <div class="card-body">
                    <div class="d-flex justify-content-end mb-3">
                        <a href="{{ route('admin.users.create') }}" class="btn btn-success btn-sm">
                            <i class="bi bi-plus-circle me-1"></i> Tambah User
                        </a>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th><i class="bi bi-person-badge me-1"></i> NIK</th>
                                    <th><i class="bi bi-person me-1"></i> Nama</th>
                                    <th><i class="bi bi-envelope me-1"></i> Email</th>
                                    <th><i class="bi bi-shield-lock me-1"></i> Role</th>
                                    <th><i class="bi bi-gear me-1"></i> Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($users as $user)
                                    <tr>
                                        <td>{{ $user->nik }}</td>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>{{ ucfirst($user->role) }}</td>
                                        <td>
                                            <a href="{{ route('admin.users.edit', $user->nik) }}" class="btn btn-outline-custom btn-outline-warning-custom btn-sm me-1">
                                                <i class="bi bi-pencil me-1"></i>Edit
                                            </a>
                                            <form action="{{ route('admin.users.destroy', $user->nik) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus user ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-outline-custom btn-outline-danger-custom btn-sm">
                                                    <i class="bi bi-trash me-1"></i>Hapus
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">Tidak ada user ditemukan.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </section>
</div>
@endsection
