@php
    use App\Models\Notifikasi;
    use Illuminate\Support\Facades\Auth;

    $notifikasis = collect();

    if (Auth::check()) {
        $notifikasis = Notifikasi::where('nik', Auth::user()->nik)
            ->where('dibaca', false)
            ->latest()
            ->take(5)
            ->get();
    }
@endphp

<nav class="navbar navbar-expand-lg navbar-light bg-white px-4 py-3">
    <div class="container-fluid">

        <div class="d-flex align-items-center gap-3 ms-auto">

            <div class="d-flex gap-4 align-items-center">
                {{-- Tombol Notifikasi --}}
                <div class="dropdown position-relative">
                    <button class="btn btn-outline-success position-relative px-2 py-2" id="notifBtn" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-bell fs-4"></i>
                        @php $belumDibaca = $notifikasis->count(); @endphp
                        @if($belumDibaca > 0)
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-success animate-pulse" id="notifBadge">
                                {{ $belumDibaca }}
                                <span class="visually-hidden">unread messages</span>
                            </span>
                        @endif
                    </button>

                    <ul class="dropdown-menu dropdown-menu-end shadow notif-dropdown">
                        <li class="dropdown-header fw-bold px-3 py-2 d-flex justify-content-between align-items-center text-success">
                            <span>Notifikasi Terbaru</span>
                            @if($belumDibaca > 0)
                                <form action="{{ route('notifikasi.readall') }}" method="POST" id="markAllForm" class="m-0 p-0">
                                    @csrf
                                    <button type="submit" class="icon-button" title="Tandai semua dibaca">
                                        <i class="bi bi-eye-fill fs-5 text-success"></i>
                                    </button>
                                </form>
                            @endif
                        </li>
                        <li><hr class="dropdown-divider"></li>

                        @forelse($notifikasis as $notif)
                            <li>
                                <a class="dropdown-item text-success notif-item" href="{{ route('notifikasi.read', $notif->id) }}"
                                   onclick="event.preventDefault(); document.getElementById('read-form-{{ $notif->id }}').submit();">
                                    <i class="bi bi-chat-left-text-fill me-2 text-success"></i>
                                    <strong>{{ $notif->judul ?? 'Notifikasi' }}</strong><br>
                                    <span class="d-block">{{ $notif->pesan }}</span>
                                    <small class="text-muted">{{ $notif->created_at->diffForHumans() }}</small>
                                </a>
                                <form id="read-form-{{ $notif->id }}" action="{{ route('notifikasi.read', $notif->id) }}" method="POST" style="display: none;">
                                    @csrf
                                    @method('PATCH')
                                </form>
                            </li>
                        @empty
                            <li><span class="dropdown-item text-muted">Tidak ada notifikasi</span></li>
                        @endforelse
                    </ul>
                </div>

                {{-- Icon Profil + Nama --}}
                <div class="dropdown">
                    <a href="#" class="d-inline-flex align-items-center text-decoration-none dropdown-toggle" id="dropdownUser" data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="{{ asset('storage/' . Auth::user()->foto_profil) }}" alt="profil" width="36" height="36" class="rounded-circle me-2">
                        <span class="text-success fw-semibold" style="white-space:nowrap;">{{ Auth::user()->name }}</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow py-2">
                        <li>
                            <form action="{{ route('logout') }}" method="POST" id="logoutForm" class="m-0">
                                @csrf
                                <button class="dropdown-item d-flex align-items-center gap-2 text-danger" type="submit"
                                        onclick="return confirm('Yakin ingin keluar dari akun?')">
                                    <span>Logout</span>
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</nav>

<style>
.notif-dropdown {
    width: 300px !important;       /* PAKSA LEBAR LEBIH BESAR */
    max-width: 95vw;               /* tetap responsif di mobile */
    max-height: 80vh;              /* lebih tinggi agar teks terlihat */
    overflow-y: auto;
}

@media (max-width: 200px) {
    .notif-dropdown {
        width: 95vw !important;
        border-radius: 0;
    }
    .dropdown-item {
        padding: 0.5rem 0.75rem;
    }
}

#notifBadge {
    top: 0.3rem !important;
    right: 0.4rem !important;
}

.icon-button {
    background: transparent;
    border: none;
    padding: 0.25rem;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    transition: background 0.2s ease;
}

.icon-button:hover {
    background: rgba(0, 0, 0, 0.05);
    cursor: pointer;
}

.notif-item {
    white-space: normal !important;
    overflow-wrap: break-word;
    word-break: break-word;
    font-size: 1rem;               /* teks lebih besar agar terbaca */
    padding-top: 0.75rem;          /* padding agar lega */
    padding-bottom: 0.75rem;
}
</style>
