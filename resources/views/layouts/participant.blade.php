<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sistem Arisan Peserta')</title>
    <link rel="icon" type="image/png" href="{{ asset('img/logo.png') }}">
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="theme-color" content="#2c3e50">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #34495e;
            --accent-color: #3498db;
            --light-gray: #f8f9fa;
            --border-color: #e0e0e0;
            --success-color: #27ae60;
            --warning-color: #f39c12;
            --text-color: #333333;
            --text-muted: #6c757d;
        }
        
        body {
            background-color: #f5f7fa;
            color: var(--text-color);
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
        }
        
        /* Navbar Styling */
        .navbar {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%) !important;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 0.8rem 0;
        }
        
        .navbar-brand {
            font-weight: 700;
            letter-spacing: 0.2px;
            padding: 0.2rem 0;
            line-height: 1.1;
        }
        
        @media (max-width: 768px) {
            .navbar-brand span {
                font-size: 0.75rem !important;
                max-width: 180px !important;
                display: block;
            }
            .navbar .container-fluid {
                padding-left: 0.75rem !important;
                padding-right: 0.75rem !important;
            }
            .dropdown-toggle span {
                display: none;
            }
            .dropdown-toggle::after {
                display: none;
            }
            .dropdown-menu {
                position: absolute !important;
                right: 0 !important;
                left: auto !important;
                width: 200px !important;
            }
        }
        
        /* Card Styling */
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            margin-bottom: 1.5rem;
            overflow: hidden;
        }
        
        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        }
        
        .card-header {
            background-color: var(--primary-color);
            color: white;
            border-bottom: none;
            padding: 1rem 1.25rem;
            font-weight: 600;
        }
        
        .card-header.bg-info {
            background-color: #2980b9 !important;
        }
        
        /* Table Styling */
        .table {
            margin-bottom: 0;
        }
        
        .table thead th {
            background-color: var(--primary-color);
            color: white;
            border-bottom: none;
            font-weight: 600;
            padding: 1rem;
            white-space: nowrap;
        }
        
        .table tbody tr {
            transition: background-color 0.2s ease;
        }
        
        .table tbody tr:hover {
            background-color: rgba(52, 152, 219, 0.05);
        }
        
        .table-striped tbody tr:nth-of-type(odd) {
            background-color: rgba(0,0,0,0.02);
        }
        
        /* Badge Styling */
        .badge {
            padding: 0.35em 0.65em;
            font-weight: 500;
            border-radius: 4px;
        }
        
        .badge.bg-primary {
            background-color: #3498db !important;
        }
        
        .badge.bg-info {
            background-color: #7f8c8d !important;
        }
        
        /* Button Styling */
        .btn {
            border-radius: 6px;
            font-weight: 500;
            transition: all 0.2s ease;
            padding: 0.5rem 1rem;
        }
        
        .btn-primary {
            background-color: var(--accent-color);
            border-color: var(--accent-color);
        }
        
        .btn-primary:hover {
            background-color: #2980b9;
            border-color: #2980b9;
            transform: translateY(-1px);
        }
        
        .btn-outline-primary {
            color: var(--accent-color);
            border-color: var(--accent-color);
        }
        
        .btn-outline-primary:hover {
            background-color: var(--accent-color);
            border-color: var(--accent-color);
        }
        
        .btn-outline-success {
            color: var(--success-color);
            border-color: var(--success-color);
        }
        
        .btn-outline-success:hover {
            background-color: var(--success-color);
            color: white;
        }
        
        .btn-outline-warning {
            color: var(--warning-color);
            border-color: var(--warning-color);
        }
        
        .btn-outline-warning:hover {
            background-color: var(--warning-color);
            color: white;
        }
        
        .btn-outline-info {
            color: #3498db;
            border-color: #3498db;
        }
        
        .btn-outline-info:hover {
            background-color: #3498db;
            color: white;
        }
        
        /* Modal Styling */
        .modal-content {
            border: none;
            border-radius: 10px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.15);
        }
        
        .modal-header {
            background-color: var(--primary-color);
            color: white;
            border-radius: 10px 10px 0 0;
            padding: 1.25rem;
        }
        
        .modal-footer {
            background-color: var(--light-gray);
            border-top: 1px solid var(--border-color);
            padding: 1rem;
        }
        
        /* Form Styling */
        .form-control, .form-select {
            border: 1px solid var(--border-color);
            border-radius: 6px;
            padding: 0.625rem 0.75rem;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--accent-color);
            box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.1);
        }
        
        .input-group-text {
            background-color: var(--light-gray);
            border: 1px solid var(--border-color);
            color: var(--text-muted);
        }
        
        /* Typography */
        h2 {
            color: var(--primary-color);
            font-weight: 600;
            margin-bottom: 0.5rem;
            padding-bottom: 0.5rem;
        }
        
        .text-success {
            color: var(--success-color) !important;
        }
        
        /* Empty State */
        .empty-state {
            padding: 3rem 1rem;
            text-align: center;
            color: var(--text-muted);
        }
        
        .empty-state i {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }
        
        /* Responsive Adjustments */
        @media (max-width: 768px) {
            .table-responsive {
                border: 1px solid var(--border-color);
                border-radius: 8px;
            }
        }
        
        /* Logout Button */
        .btn-logout {
            color: rgba(255,255,255,0.9) !important;
            background: transparent;
            border: none;
        }
        
        .btn-logout:hover {
            color: white !important;
            background: rgba(255,255,255,0.1);
        }

        /* Sidebar Item Active Override */
        .list-group-item.active {
            background-color: var(--accent-color);
            border-color: var(--accent-color);
        }

        /* Sidebar Styling (Admin-like) */
        #sidebarContainer {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: sticky;
            top: 80px;
            height: calc(100vh - 100px);
            overflow-y: auto;
            overflow-x: hidden;
            z-index: 1030;
        }

        #sidebarContainer .card {
            height: 100%;
            margin-bottom: 0;
        }

        /* Sidebar Toggle States */
        /* Desktop */
        @media (min-width: 769px) {
            #sidebarContainer {
                width: 280px;
                flex-shrink: 0;
                margin-right: 1.5rem;
                transition: margin-left 0.3s ease;
            }

            /* Default Closed state for Desktop */
            #sidebarContainer.sidebar-collapsed {
                margin-left: -305px; /* Width + Margin to hide completely */
            }
            
            #contentContainer {
                flex-grow: 1;
                min-width: 0; /* Prevent flex blowout */
                transition: all 0.3s ease;
            }
        }

        /* Mobile */
        @media (max-width: 768px) {
            #sidebarContainer {
                position: fixed;
                top: 72px;
                left: 0;
                width: 280px;
                height: calc(100vh - 72px);
                z-index: 1040;
                background: white;
                box-shadow: 2px 0 10px rgba(0,0,0,0.1);
                transform: translateX(-100%); /* Default hidden on mobile */
                transition: transform 0.3s ease;
            }

            #sidebarContainer.sidebar-open {
                transform: translateX(0);
            }

            /* Overlay for mobile when sidebar is open */
            .sidebar-overlay {
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0,0,0,0.5);
                z-index: 1039;
                display: none;
            }
            
            .sidebar-overlay.show {
                display: block;
            }
        }

        /* Toggle Button */
        #sidebarToggle {
            transition: transform 0.3s ease;
        }

        #sidebarToggle:hover {
            transform: scale(1.1);
        }
    </style>
    @stack('styles')
</head>
<body>
    <nav class="navbar py-2 sticky-top">
        <div class="container-fluid d-flex justify-content-between align-items-center flex-nowrap px-3">
            <!-- Left Side: Toggle & Brand -->
            <div class="d-flex align-items-center flex-shrink-1 overflow-hidden">
                <a class="navbar-brand d-flex align-items-center text-white mb-0" href="{{ route('participant.dashboard') }}">
                    <span class="fw-bold text-wrap" style="max-width: 350px;">ARISAN SEPEDA MOTOR PRIMKOPKAR "PRIMA"</span>
                </a>
            </div>

            <!-- Right Side: Profile Dropdown -->
            @php
                $currentParticipant = Auth::guard('participant')->user();
                $relatedAccounts = \App\Models\Participant::where('nik', $currentParticipant->nik)
                                    ->where('id', '!=', $currentParticipant->id)
                                    ->with('group')
                                    ->get();
            @endphp
            <div class="dropdown flex-shrink-0">
                <a class="nav-link dropdown-toggle text-white d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    @if($currentParticipant->photo)
                        <img src="{{ asset('storage/' . $currentParticipant->photo) }}" alt="Avatar" class="rounded-circle me-1 border border-2 border-white-50" style="width: 35px; height: 35px; object-fit: cover;">
                    @else
                        <i class="fas fa-user-circle fa-2x me-1"></i>
                    @endif
                    <span class="d-none d-md-inline ms-1">{{ $currentParticipant->name }}</span>
                    <i class="fas fa-chevron-down small ms-1 opacity-50"></i>
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2">
                    <li><a class="dropdown-item py-2" href="{{ route('home') }}">
                        <i class="fas fa-home me-2 text-info"></i>Beranda
                    </a></li>
                    <li><a class="dropdown-item py-2" href="{{ route('participant.profile') }}">
                        <i class="fas fa-user-cog me-2 text-primary"></i>Profil Saya
                    </a></li>
                    <li><a class="dropdown-item py-2" href="{{ route('participant.terms') }}">
                        <i class="fas fa-file-contract me-2 text-secondary"></i>Syarat & Ketentuan
                    </a></li>
                    
                    @if($relatedAccounts->count() > 0)
                        <li><hr class="dropdown-divider"></li>
                        <li><h6 class="dropdown-header text-muted text-uppercase small fw-bold px-3 mt-1 mb-1">Beralih Akun</h6></li>
                        @foreach($relatedAccounts as $account)
                            <li>
                                <a class="dropdown-item py-2 d-flex justify-content-between align-items-center" 
                                   href="#" 
                                   onclick="event.preventDefault(); document.getElementById('switch-account-{{ $account->id }}').submit();">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-random me-2 text-info"></i>
                                        <span>{{ $account->lottery_number }}</span>
                                    </div>
                                    <span class="badge bg-secondary ms-2" style="font-size: 0.7rem;">{{ $account->group->name }}</span>
                                </a>
                                <form id="switch-account-{{ $account->id }}" action="{{ route('participant.switch-account', $account->id) }}" method="POST" style="display: none;">
                                    @csrf
                                </form>
                            </li>
                        @endforeach
                    @endif
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="dropdown-item py-2 text-danger">
                                <i class="fas fa-sign-out-alt me-2"></i>Logout
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
            <!-- Custom PWA Install Button -->
            <button id="pwaInstallBtn" class="btn btn-warning btn-sm d-none ms-2">
                <i class="fas fa-download me-1"></i> Instal
            </button>
        </div>
    </nav>

    <div class="container-fluid mt-4">
        <!-- Flex Container for Layout -->
        <div class="d-flex align-items-start position-relative">
            <!-- Sidebar with Default Collapsed -->
            
            
            <!-- Main Content -->
            <div id="contentContainer" class="w-100">
                @yield('content')
            </div>
        </div>
    </div>
    
    <!-- Mobile Overlay -->
    <div class="sidebar-overlay" id="mobileOverlay"></div>

    <!-- Modals -->
    @yield('modals')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebarContainer = document.getElementById('sidebarContainer');
            const mobileOverlay = document.getElementById('mobileOverlay');
            
            if (sidebarToggle && sidebarContainer) {
                // Check local storage for desktop preference
                if (window.innerWidth > 768) {
                    // Start CLOSED by default. Only open if explicitly set to 'true'.
                    const isOpen = localStorage.getItem('participantSidebarOpen') === 'true';
                    
                    if (isOpen) {
                        sidebarContainer.classList.remove('sidebar-collapsed');
                    } else {
                        // Ensure it is collapsed if logic dictates (though HTML has it by default)
                        sidebarContainer.classList.add('sidebar-collapsed');
                    }
                }

                sidebarToggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    if (window.innerWidth <= 768) {
                        // Mobile Behavior
                        sidebarContainer.classList.toggle('sidebar-open');
                        if (mobileOverlay) mobileOverlay.classList.toggle('show');
                    } else {
                        // Desktop Behavior
                        sidebarContainer.classList.toggle('sidebar-collapsed');
                        // If it contains collapsed class, it is CLOSED.
                        // So isOpen is !contains('sidebar-collapsed')
                        const isOpen = !sidebarContainer.classList.contains('sidebar-collapsed');
                        localStorage.setItem('participantSidebarOpen', isOpen);
                    }
                });
                
                // Close sidebar when clicking overlay on mobile
                if (mobileOverlay) {
                    mobileOverlay.addEventListener('click', function() {
                        sidebarContainer.classList.remove('sidebar-open');
                        mobileOverlay.classList.remove('show');
                    });
                }
            }
        });
    </script>
    <script>
        let deferredPrompt;
        const installBtn = document.getElementById('pwaInstallBtn');

        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            deferredPrompt = e;
            if (installBtn) {
                installBtn.classList.remove('d-none');
            }
        });

        if (installBtn) {
            installBtn.addEventListener('click', (e) => {
                installBtn.classList.add('d-none');
                deferredPrompt.prompt();
                deferredPrompt.userChoice.then((choiceResult) => {
                    if (choiceResult.outcome === 'accepted') {
                        console.log('User accepted the A2HS prompt');
                    } else {
                        console.log('User dismissed the A2HS prompt');
                    }
                    deferredPrompt = null;
                });
            });
        }

        window.addEventListener('appinstalled', (evt) => {
            console.log('PWA was installed');
            if (installBtn) {
                installBtn.classList.add('d-none');
            }
        });

        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js')
                    .then(reg => console.log('Service Worker registered', reg))
                    .catch(err => console.log('Service Worker registration failed', err));
            });
        }
    </script>
    @stack('scripts')
</body>
</html>
