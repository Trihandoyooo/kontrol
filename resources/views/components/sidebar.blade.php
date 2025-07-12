<div id="sidebar" class="active sidebar sidebar bg-light">
    <div class="sidebar-wrapper active">
        <div class="sidebar-header d-flex justify-content-center py-10">
<div class="text-center mb-2">
    <img src="{{ asset('templates/assets/compiled/jpg/simoleglogo.png') }}" alt="Logo Besar" style="max-width: 100px; width: 100%; height: auto;" />
</div>
        </div>
        <div class="sidebar-menu">
            <ul class="menu list-unstyled">
                    
                <li class="sidebar-item {{ request()->is('dashboard') ? 'active' : '' }}">
                    <a href="{{ url('/dashboard') }}" class="sidebar-link d-flex align-items-center px-3 py-2">
                        <i class="bi bi-grid-fill me-2"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                
                {{-- Menu Rapat dan Koordinasi --}}
                <li class="sidebar-item {{ request()->is('rapat*') || request()->is('admin/rapat*') ? 'active' : '' }}">
                    @if(auth()->user() && auth()->user()->role === 'admin')
                        <a href="{{ url('admin/rapat') }}" class="sidebar-link d-flex align-items-center px-3 py-2">
                            <i class="bi bi-stack me-2"></i>
                            <span>Rapat dan Koordinasi</span>
                        </a>
                    @else
                        <a href="{{ url('/rapat') }}" class="sidebar-link d-flex align-items-center px-3 py-2">
                            <i class="bi bi-stack me-2"></i>
                            <span>Rapat dan Koordinasi</span>
                        </a>
                    @endif
                </li>

                {{-- Menu Iuran --}}
                <li class="sidebar-item {{ request()->is('iuran*') || request()->is('admin/iuran*') ? 'active' : '' }}">
                        @if(auth()->user() && auth()->user()->role === 'admin')
                        <a href="{{ url('admin/iuran') }}" class="sidebar-link d-flex align-items-center px-3 py-2">
                            <i class="bi bi-cash me-2"></i>
                            <span>Iuran</span>
                        </a>
                    @else
                <li class="sidebar-item {{ request()->is('iuran*') ? 'active' : '' }}">
                    <a href="{{ url('/iuran') }}" class="sidebar-link d-flex align-items-center px-3 py-2">
                        <i class="bi bi-cash me-2"></i>
                        <span>Iuran</span>
                    </a>
                    @endif
                </li>

                {{-- Menu Kaderisasi --}}
                        <li class="sidebar-item {{ request()->is('kaderisasi*') || request()->is('admin/kaderisasi*') ? 'active' : '' }}">
                        @if(auth()->user() && auth()->user()->role === 'admin')
                        <a href="{{ url('admin/kaderisasi') }}" class="sidebar-link d-flex align-items-center px-3 py-2">
                            <i class="bi bi-person-badge-fill me-2"></i>
                            <span>Kaderisasi</span>
                        </a>
                    @else
                <li class="sidebar-item {{ request()->is('kaderisasi*') ? 'active' : '' }}">
                    <a href="{{ url('/kaderisasi') }}" class="sidebar-link d-flex align-items-center px-3 py-2">
                        <i class="bi bi-person-badge-fill me-2"></i>
                        <span>Kaderisasi</span>
                    </a>
                </li>
                @endif

                {{-- Menu Outcomes --}}
<li class="sidebar-item {{ request()->is('outcome*') || request()->is('admin/outcome*') ? 'active' : '' }}">
    @if(auth()->user() && auth()->user()->role === 'admin')
        <a href="{{ url('admin/outcome') }}" class="sidebar-link d-flex align-items-center px-3 py-2">
            <i class="bi bi-award-fill me-2"></i>
            <span>Outcome</span>
        </a>
    @else
        <a href="{{ url('/outcome') }}" class="sidebar-link d-flex align-items-center px-3 py-2">
            <i class="bi bi-award-fill me-2"></i>
            <span>Outcome</span>
        </a>
    @endif
</li>

                {{-- Menu Manajemen Akun (admin only) --}}
                @if(auth()->user() && auth()->user()->role === 'admin')
                    <li class="sidebar-item {{ request()->is('admin/users*') ? 'active' : '' }}">
                        <a href="{{ route('admin.users.index') }}" class="sidebar-link d-flex align-items-center px-3 py-2">
                            <i class="bi bi-person-lock me-2"></i>
                            <span>Manajemen Akun</span>
                        </a>
                    </li>
                @endif

                {{-- Logout --}}
                <li class="sidebar-item">
                    <form method="POST" action="{{ route('logout') }}" id="logout-form">
                        @csrf
                        <a href="#" 
                           class="sidebar-link d-flex align-items-center px-3 py-2"
                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="bi bi-box-arrow-right me-2"></i>
                            <span>Logout</span>
                        </a>
                    </form>
                </li>

            </ul>
        </div>
    </div>
</div>
