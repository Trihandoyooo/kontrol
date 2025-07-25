@extends('layouts.app')

@section('content')
<style>
    body {
        background: rgb(236, 244, 239) !important;
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
    .img-profile {
        width: 32px;
        height: 32px;
        object-fit: cover;
        border-radius: 50%;
        margin-right: 8px;
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

            <div class="card shadow-sm border-0 mb-2">
                <div class="card-body">
                    <h2 class="card-title mb-2">Manajemen User</h2>
                    <p class="text-muted mb-0">Halaman untuk melihat dan mengelola akun user yang terdaftar dalam sistem.</p>
                </div>

                <div class="card-body">
                    <form method="GET" action="{{ route('admin.users.index') }}">
                        <div class="row align-items-end border-bottom pb-3 mb-4">
                            <div class="col-md-3">
                                <label for="search" class="form-label">Search</label>
                                <input type="text" name="search" id="search" class="form-control"
                                    placeholder="Cari nama, NIK, atau email..." value="{{ request('search') }}">
                            </div>
                            <div class="col-md-3">
                                <label for="role" class="form-label">Role</label>
                                <select name="role" id="role" class="form-select" onchange="this.form.submit()">
                                    <option value="all" {{ request('role') == 'all' ? 'selected' : '' }}>Semua Role</option>
                                    <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                                    <option value="ketua" {{ request('role') == 'ketua' ? 'selected' : '' }}>Ketua</option>
                                    <option value="user" {{ request('role') == 'user' ? 'selected' : '' }}>User</option>
                                </select>
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <button type="submit" class="btn btn-outline-primary">
                                    <i class="bi bi-search"></i> Cari
                                </button>
                            </div>
                            <div class="col-md-3 text-md-end mt-3 mt-md-0">
                                <a href="{{ route('admin.users.create') }}" class="btn btn-outline-success w-100 w-md-auto">
                                    <i class="bi bi-plus-circle me-1"></i> Tambah User
                                </a>
                            </div>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th><i class="bi bi-person-badge me-1"></i>NIK</th>
                                    <th><i class="bi bi-person me-1"></i>Nama</th>
                                    <th><i class="bi bi-envelope me-1"></i>Email</th>
                                    <th><i class="bi bi-shield-lock me-1"></i>Role</th>
                                    <th><i class="bi bi-geo me-1"></i>Dapil</th>
                                    <th><i class="bi bi-gear me-1"></i>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($users as $user)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="{{ $user->foto_profil ? asset('storage/' . $user->foto_profil) : asset('assets/default-avatar.png') }}" alt="Foto Profil" class="img-profile">
                                                {{ $user->nik }}
                                            </div>
                                        </td>
                                        <td>
                                            {{ $user->gelar_depan ? $user->gelar_depan . ' ' : '' }}
                                            {{ $user->name }}
                                            {{ $user->gelar_belakang ? ', ' . $user->gelar_belakang : '' }}
                                        </td>
                                        <td>{{ $user->email }}</td>
                                        <td>{{ ucfirst($user->role) }}</td>
                                        <td>{{ $user->role === 'user' ? ($user->dapil ?? '-') : '-' }}</td>
                                        <td class="d-flex flex-wrap">
                                            <a href="{{ route('admin.users.show', $user->nik) }}" class="btn btn-outline-custom btn-outline-info btn-sm me-1 mb-1">
                                                <i class="bi bi-eye"></i> Show
                                            </a>

                                            <a href="{{ route('admin.users.edit', $user->nik) }}" class="btn btn-outline-custom btn-outline-warning-custom btn-sm me-1 mb-1">
                                                <i class="bi bi-pencil me-1"></i> Edit
                                            </a>

                                            <form action="{{ route('admin.users.destroy', $user->nik) }}" method="POST" class="d-inline me-1 mb-1" onsubmit="return confirm('Yakin ingin menghapus user ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-outline-custom btn-outline-danger-custom btn-sm">
                                                    <i class="bi bi-trash me-1"></i> Hapus
                                                </button>
                                            </form>

                                            @if($user->role === 'user')
                                                <button type="button" class="btn btn-outline-custom btn-outline-warning-custom btn-sm me-1 mb-1" data-bs-toggle="modal" data-bs-target="#peringatanModal{{ $user->nik }}">
                                                    <i class="bi bi-exclamation-triangle"></i> Peringatan
                                                </button>

                                                <!-- Tombol Kontribusi -->
                                                <button type="button" class="btn btn-outline-custom btn-outline-primary btn-sm mb-1" data-bs-toggle="modal" data-bs-target="#modalKontribusi{{ $user->nik }}">
                                                    <i class="bi bi-bar-chart-line"></i> Kontribusi
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-muted">Tidak ada user ditemukan.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-center mt-4">
                        {{ $users->withQueryString()->links() }}
                    </div>
                </div>
            </div>

            {{-- Modal Peringatan dan Kontribusi --}}
            @foreach ($users as $user)
                @if ($user->role === 'user')
                    <!-- Modal Peringatan -->
                    <div class="modal fade" id="peringatanModal{{ $user->nik }}" tabindex="-1" aria-labelledby="peringatanModalLabel{{ $user->nik }}" aria-hidden="true">
                        <div class="modal-dialog">
                            <form method="POST" action="{{ route('notifikasi.store') }}">
                                @csrf
                                <input type="hidden" name="nik" value="{{ $user->nik }}">
                                <input type="hidden" name="tipe" value="rapat">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="peringatanModalLabel{{ $user->nik }}">Kirim Peringatan</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label class="form-label">Judul</label>
                                            <input type="text" class="form-control" name="judul" placeholder="Contoh: Data Rapat Tidak Lengkap" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Pesan</label>
                                            <textarea class="form-control" name="pesan" rows="3" placeholder="Harap segera melengkapi data rapat Anda."></textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-success">Kirim</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Modal Kontribusi -->
                    <div class="modal fade" id="modalKontribusi{{ $user->nik }}" tabindex="-1" aria-labelledby="kontribusiLabel{{ $user->nik }}" aria-hidden="true">
                        <div class="modal-dialog modal-lg modal-dialog-centered">
                            <div class="modal-content rounded-4">
                                <div class="modal-header bg-light">
                                    <h5 class="modal-title" id="kontribusiLabel{{ $user->nik }}">Detail Kontribusi: {{ $user->name }}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-md-5 border-end">
                                            <p><strong>NIK:</strong> {{ $user->nik }}</p>
                                            <p><strong>Email:</strong> {{ $user->email ?? '-' }}</p>
                                            <p><strong>Dapil:</strong> {{ $user->dapil ?? '-' }}</p>
                                            <p><strong>Jumlah Suara:</strong> {{ $user->jumlah_suara ?? '0' }}</p>
                                        </div>
                                        <div class="col-md-7">
                                            <p><strong>Total Rapat:</strong> {{ $user->rapats->count() }} kegiatan</p>
                                            <p><strong>Total Iuran:</strong> {{ $user->iurans->count() }} kali</p>
                                            <p><strong>Total Kaderisasi:</strong> {{ $user->kaderisasis->count() }} kegiatan</p>
                                            <p><strong>Total Outcome:</strong> {{ $user->outcomes->count() }} data</p>
                                            <div class="mt-2">
                                                <a href="{{ route('admin.rapat.index') }}?search={{ $user->name }}" class="btn btn-sm btn-outline-danger me-1 mb-1">Lihat Rapat</a>
                                                <a href="{{ route('admin.iuran.index') }}?search={{ $user->name }}" class="btn btn-sm btn-outline-success me-1 mb-1">Lihat Iuran</a>
                                                <a href="{{ route('kaderisasi.admin.index') }}?search={{ $user->name }}" class="btn btn-sm btn-outline-warning me-1 mb-1">Lihat Kaderisasi</a>
                                                <a href="{{ route('admin.outcome.index') }}?search={{ $user->name }}" class="btn btn-sm btn-outline-primary me-1 mb-1">Lihat Outcome</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach

        </div>
    </section>
</div>
@endsection
