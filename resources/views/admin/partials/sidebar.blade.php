<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Menu</h5>
    </div>
    <div class="list-group list-group-flush sidebar-menu">
        <a href="{{ route('home') }}" class="list-group-item list-group-item-action">
            <i class="fas fa-home me-2"></i>Beranda
        </a>
        <a href="{{ route('admin.dashboard') }}" class="list-group-item list-group-item-action {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <i class="fas fa-tachometer-alt me-2"></i>Dashboard
        </a>
        <a href="{{ route('admin.groups') }}" class="list-group-item list-group-item-action {{ request()->routeIs('admin.groups*') ? 'active' : '' }}">
            <i class="fas fa-users me-2"></i>Daftar Kelompok
        </a>
        <a href="{{ route('admin.periods') }}" class="list-group-item list-group-item-action {{ request()->routeIs('admin.periods*') ? 'active' : '' }}">
            <i class="fas fa-calendar me-2"></i>Daftar Periode 
        </a>
        <a href="{{ route('admin.winners') }}" class="list-group-item list-group-item-action {{ request()->routeIs('admin.winners*') ? 'active' : '' }}">
            <i class="fas fa-trophy me-2"></i>Daftar Pemenang
        </a>
        <a href="{{ route('admin.saksi') }}" class="list-group-item list-group-item-action {{ (request()->routeIs('admin.saksi*')) ? 'active' : '' }}">
            <i class="fas fa-users-cog me-2"></i>Saksi
        </a>
        <a href="{{ route('admin.customer-service.index') }}" class="list-group-item list-group-item-action {{ (request()->routeIs('admin.customer-service*')) ? 'active' : '' }}">
            <i class="fas fa-headset me-2"></i>Customer Service
        </a>
        <a href="{{ route('admin.kta.settings') }}" class="list-group-item list-group-item-action {{ (request()->routeIs('admin.kta.settings')) ? 'active' : '' }}">
            <i class="fas fa-id-card me-2"></i>Kelola KTA
        </a>
        <a href="{{ route('admin.kta.scanner') }}" class="list-group-item list-group-item-action {{ (request()->routeIs('admin.kta.scanner')) ? 'active' : '' }}">
            <i class="fas fa-qrcode me-2"></i>Scan KTA
        </a>
        
        <!-- Divider -->
        <div class="border-top my-2"></div>
        
        <a href="{{ route('admin.profile') }}" class="list-group-item list-group-item-action {{ request()->routeIs('admin.profile') ? 'active' : '' }}">
            <i class="fas fa-user-cog me-2"></i>Profil Admin
        </a>
        <form action="{{ route('logout') }}" method="POST" class="m-0">
            @csrf
            <button type="submit" class="list-group-item list-group-item-action text-danger w-100 text-start border-0">
                <i class="fas fa-sign-out-alt me-2"></i>Logout
            </button>
        </form>
    </div>
</div>
