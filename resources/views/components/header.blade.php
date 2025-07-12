@php
    use App\Models\Notifikasi;
    use Illuminate\Support\Facades\Auth;

    $notifikasis = collect();

    if (Auth::check()) {
        $notifikasis = Notifikasi::where('nik', Auth::user()->nik)
            ->where('dibaca', false) // hanya yg belum dibaca
            ->latest()
            ->take(5)
            ->get();
    }
@endphp

<nav class="navbar navbar-expand-lg navbar-light bg-white px-4 py-3">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="#">Kontrol Dashboard</a>

        <div class="d-flex align-items-center gap-3 ms-auto">

            {{-- Tombol Notifikasi --}}
            <div class="dropdown position-relative">
                <button class="btn btn-outline-success position-relative" id="notifBtn" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-bell"></i>
                    @php $belumDibaca = $notifikasis->count(); @endphp
                    @if($belumDibaca > 0)
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-success animate-pulse" id="notifBadge">
                            {{ $belumDibaca }}
                            <span class="visually-hidden">unread messages</span>
                        </span>
                    @endif
                </button>

                <ul class="dropdown-menu dropdown-menu-end shadow" style="width: 450px;">
                    <li class="dropdown-header fw-bold px-3 py-2 d-flex justify-content-between align-items-center">
                        <span>Notifikasi Terbaru</span>
                        @if($belumDibaca > 0)
                            <form action="{{ route('notifikasi.readall') }}" method="POST" id="markAllForm">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-link text-decoration-none text-success">
                                    Tandai Semua Dibaca
                                </button>
                            </form>
                        @endif
                    </li>
                    <li><hr class="dropdown-divider"></li>

                    @forelse($notifikasis as $notif)
                        <li>
                            <a class="dropdown-item" href="{{ route('notifikasi.read', $notif->id) }}"
                               onclick="event.preventDefault(); document.getElementById('read-form-{{ $notif->id }}').submit();"
                               style="font-weight: bold;">
                                <strong>{{ $notif->judul ?? 'Notifikasi' }}</strong>    
                                <br> {{ $notif->pesan }}<br>
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
                <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" id="dropdownUser" data-bs-toggle="dropdown" aria-expanded="false">
                    <img src="{{ asset('storage/' . Auth::user()->foto_profil) }}" alt="profil" width="32" height="32" class="rounded-circle me-2">
                    <span class="text-success">{{ Auth::user()->name }}</span> {{-- Nama hijau tanpa bold --}}
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow">
                    <li>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button class="dropdown-item" type="submit">Keluar</button>
                        </form>
                    </li>
                </ul>
            </div>

        </div>
    </div>
</nav>
