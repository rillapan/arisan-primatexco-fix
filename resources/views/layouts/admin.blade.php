<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sistem Arisan Admin')</title>
    <link rel="icon" type="image/png" href="{{ asset('img/logo.png') }}">
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="theme-color" content="#2c3e50">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
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
            font-weight: 600;
            letter-spacing: 0.5px;
            padding: 0.5rem 0;
        }
        
        .navbar-brand i {
            color: #ecf0f1;
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
            margin-bottom: 0.5rem; /* Adjusted for hierarchy */
            padding-bottom: 0.5rem;
        }
        
        .text-success {
            color: var(--success-color) !important;
        }
        
        /* Quick Actions Grid */
        .quick-actions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 1rem;
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
            .quick-actions-grid {
                grid-template-columns: 1fr;
            }
            
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

        /* Profile Button */
        .btn-profile {
            color: rgba(255,255,255,0.9) !important;
            background: rgba(255,255,255,0.1);
            border: 1px solid rgba(255,255,255,0.2);
        }
        
        .btn-profile:hover {
            color: white !important;
            background: rgba(255,255,255,0.2);
            border-color: rgba(255,255,255,0.3);
        }

        /* Sidebar Item Active Override */
        .list-group-item.active {
            background-color: var(--accent-color);
            border-color: var(--accent-color);
        }

        /* Sidebar Styling */
        #sidebarContainer {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: sticky;
            top: 80px;
            height: calc(100vh - 100px);
            overflow-y: auto;
            overflow-x: hidden;
        }

        #sidebarContainer .card {
            height: 100%;
            margin-bottom: 0;
        }

        .sidebar-menu {
            height: calc(100% - 60px);
            overflow-y: auto;
        }

        .sidebar-menu::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar-menu::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        .sidebar-menu::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 3px;
        }

        .sidebar-menu::-webkit-scrollbar-thumb:hover {
            background: #555;
        }

        .list-group-item {
            border: none;
            border-left: 3px solid transparent;
            transition: all 0.2s ease;
            padding: 0.875rem 1.25rem;
        }

        .list-group-item:hover {
            background-color: rgba(52, 152, 219, 0.08);
            border-left-color: var(--accent-color);
            transform: translateX(2px);
        }

        .list-group-item.active {
            background-color: rgba(52, 152, 219, 0.15) !important;
            border-left-color: var(--accent-color) !important;
            color: var(--accent-color) !important;
            font-weight: 600;
        }

        .list-group-item i {
            width: 20px;
            text-align: center;
        }

        /* Content Container */
        #contentContainer {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Sidebar Toggle States */
        .sidebar-collapsed {
            width: 0 !important;
            padding: 0 !important;
            margin: 0 !important;
            opacity: 0;
            flex: 0 0 0% !important; 
            max-width: 0% !important;
            overflow: hidden;
        }

        .content-expanded {
            width: 100% !important;
            flex: 0 0 100% !important;
            max-width: 100% !important;
        }

        /* Toggle Button */
        #sidebarToggle {
            transition: transform 0.3s ease;
        }

        #sidebarToggle:hover {
            transform: scale(1.1);
        }

            /* Responsive */
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
                transform: translateX(-100%);
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

        /* Desktop Sidebar */
        @media (min-width: 769px) {
            #sidebarContainer {
                width: 280px;
                flex-shrink: 0;
                position: sticky;
                top: 80px;
                height: calc(100vh - 100px);
                overflow-y: auto;
                margin-right: 1.5rem;
                transition: margin-left 0.3s ease;
            }

            #sidebarContainer.sidebar-collapsed {
                margin-left: -305px; /* Width + Margin to hide completely */
            }
            
            #contentContainer {
                flex-grow: 1;
                min-width: 0; /* Prevent flex blowout */
                transition: all 0.3s ease;
            }
        }
    </style>
    @stack('styles')
</head>
<body>


    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <div class="d-flex align-items-center">
                <button class="btn btn-link text-white me-3 p-0" id="sidebarToggle">
                    <i class="fas fa-bars fa-lg"></i>
                </button>
                <a class="navbar-brand" href="{{ route('admin.dashboard') }}" style="color: white;">
                    SISTEM ARISAN PRIMKOPKAR PRIMA
                </a>
            </div>
            <!-- Custom PWA Install Button -->
            <button id="pwaInstallBtn" class="btn btn-warning btn-sm d-none">
                <i class="fas fa-download me-1"></i> Instal Aplikasi
            </button>
        </div>
    </nav>

    <div class="container-fluid mt-4">
        <!-- Flex Container for Layout -->
        <div class="d-flex align-items-start position-relative">
            <!-- Sidebar -->
            <div id="sidebarContainer">
                @include('admin.partials.sidebar')
            </div>
            
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
                    const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
                    if (isCollapsed) {
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
                        localStorage.setItem('sidebarCollapsed', sidebarContainer.classList.contains('sidebar-collapsed'));
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
            // Prevent Chrome 67 and earlier from automatically showing the prompt
            e.preventDefault();
            // Stash the event so it can be triggered later.
            deferredPrompt = e;
            // Update UI notify the user they can install the PWA
            if (installBtn) {
                installBtn.classList.remove('d-none');
            }
        });

        if (installBtn) {
            installBtn.addEventListener('click', (e) => {
                // hide our user interface that shows our A2HS button
                installBtn.classList.add('d-none');
                // Show the prompt
                deferredPrompt.prompt();
                // Wait for the user to respond to the prompt
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
