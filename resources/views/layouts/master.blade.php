<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SparttaPOS - @yield('title', 'Dashboard')</title>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Custom CSS - Nuansa Jawa Elegan -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">

    <!-- Modern Table CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/table-modern.css') }}">

    <!-- Stats Cards CSS -->
    <!-- <link rel="stylesheet" href="{{ asset('assets/css/stats-cards.css') }}"> -->

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    @stack('styles')
</head>

<body>
    <div class="wrapper">
        <!-- Sidebar Kayu Jati -->
        <nav id="sidebar">
            <div class="sidebar-header">
                <h3>SPARTTA POS</h3>
                <div class="subtitle">Inventory System</div>
            </div>

            <div class="sidebar-menu">
                <!-- Main Menu -->
                <div class="menu-section">
                    <div class="menu-title">Main Menu</div>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                                <i class="fas fa-tachometer-alt"></i>
                                <span>Dashboard</span>
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Management Menu (Super Admin & Admin) -->
                @if(auth()->user()->hasRole('super-admin') || auth()->user()->hasRole('admin'))
                <div class="menu-section">
                    <div class="menu-title">Management</div>
                    <ul class="nav flex-column">
                        @if(auth()->user()->hasRole('super-admin'))
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}" href="{{ route('users.index') }}">
                                <i class="fas fa-users"></i>
                                <span>Manajemen User</span>
                            </a>
                        </li>
                        @endif

                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('spareparts.*') ? 'active' : '' }}" href="{{ route('spareparts.index') }}">
                                <i class="fas fa-microchip"></i>
                                <span>Daftar Sparepart</span>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}" href="{{ route('categories.index') }}">
                                <i class="fas fa-tags"></i>
                                <span>Kategori</span>
                            </a>
                        </li>
                    </ul>
                </div>
                @endif

                <!-- Features Menu (Semua role bisa akses) -->
                <div class="menu-section">
                    <div class="menu-title">Features</div>
                    <ul class="nav flex-column">
                        <!-- POS / Kasir - Semua role bisa akses -->
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('pos.*') ? 'active' : '' }}" href="{{ route('pos.index') }}">
                                <i class="fas fa-shopping-cart"></i>
                                <span>Kasir / POS</span>
                            </a>
                        </li>

                        <!-- Generate Barcode - Semua role bisa akses -->
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('barcode.generate') ? 'active' : '' }}" href="{{ route('barcode.generate') }}">
                                <i class="fas fa-barcode"></i>
                                <span>Generate Barcode</span>
                            </a>
                        </li>

                        <!-- Stock Opname / Scan Barcode - Semua role bisa akses -->
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('stock.*') ? 'active' : '' }}" href="{{ route('stock.opname.index') }}">
                                <i class="fas fa-clipboard-list"></i>
                                <span>Stock Opname</span>
                            </a>
                        </li>

                        <!-- Laporan - Semua role bisa akses -->
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}" href="{{ route('reports.index') }}">
                                <i class="fas fa-chart-line"></i>
                                <span>Laporan</span>
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Settings Menu -->
                <div class="menu-section">
                    <div class="menu-title">Settings</div>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('settings.profile') ? 'active' : '' }}" href="{{ route('settings.profile') }}">
                                <i class="fas fa-user-cog"></i>
                                <span>Pengaturan</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Sidebar Footer - User Info -->
            <div class="sidebar-footer">
                <div class="user-info">
                    <div class="user-avatar">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="user-details">
                        <h6>{{ auth()->user()->name }}</h6>
                        <p>{{ auth()->user()->role->name }}</p>
                    </div>
                </div>
            </div>
        </nav>

        <!-- NOTIFICATION DROPDOWN - DIPINDAHKAN KE LUAR CONTENT -->
        <div class="notification-global-wrapper">
            <div class="dropdown notification-dropdown-wrapper">
                <a href="#" class="text-decoration-none dropdown-toggle"
                    style="color: var(--text-gold);"
                    data-bs-toggle="dropdown"
                    aria-expanded="false"
                    id="notificationDropdown">
                    <i class="fas fa-bell"></i>
                    <span class="badge bg-danger rounded-pill" id="notificationBadge" style="font-size: 0.6rem; display: none;">0</span>
                </a>

                <div class="dropdown-menu dropdown-menu-end notification-dropdown"
                    aria-labelledby="notificationDropdown">
                    <div class="dropdown-header d-flex justify-content-between align-items-center">
                        <strong><i class="fas fa-bell me-2"></i>Notifikasi</strong>
                        <button class="btn btn-sm btn-link text-decoration-none" id="markAllReadBtn" style="color: var(--gold); font-size: 0.7rem; padding: 0;">
                            <i class="fas fa-check-double me-1"></i>Tandai semua
                        </button>
                    </div>
                    <div id="notificationList">
                        <div class="text-center py-4">
                            <div class="spinner-border text-gold" style="color: var(--gold); width: 30px; height: 30px;" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                    </div>
                    <div class="dropdown-footer text-center">
                        <a href="{{ route('notifications.index') }}" class="text-decoration-none">
                            Lihat semua notifikasi <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div id="content">
            <!-- Top Navbar -->
            <div class="navbar-top d-flex justify-content-between align-items-center">
                <button type="button" id="sidebarCollapse" class="btn">
                    <i class="fas fa-bars"></i>
                </button>

                <!-- Empty div untuk menjaga layout -->
                <div></div>

                <!-- Logout saja di navbar -->
                <a href="#" class="text-decoration-none" style="color: var(--text-cream);"
                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="fas fa-sign-out-alt"></i>
                    <span class="ms-1">Logout</span>
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
            </div>

            <!-- Flash Messages -->
            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif

            @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif

            <!-- Dynamic Content -->
            @yield('content')
        </div>
    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Custom Dashboard JS -->
    <script src="{{ asset('assets/js/dashboard.js') }}"></script>

    <!-- Notification System JS -->
    <script src="{{ asset('assets/js/notifications.js') }}"></script>

    <!-- Spareparts JS -->
    <script src="{{ asset('assets/js/spareparts.js') }}"></script>

    <!-- Categories JS -->
    <script src="{{ asset('assets/js/categories.js') }}"></script>

    <!-- pos JS -->
    <script src="{{ asset('assets/js/pos.js') }}"></script>
    <!-- Users JS -->
    <script src="{{ asset('assets/js/users.js') }}"></script>
    @stack('scripts')

    <!-- Additional CSS for Global Notification -->
    <style>
        /* Notification Global Wrapper - Posisi fixed di pojok kanan atas */
        .notification-global-wrapper {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
        }

        /* Sembunyikan toggle arrow bawaan dropdown */
        .notification-dropdown-wrapper .dropdown-toggle::after {
            display: none;
        }

        /* Style untuk badge notifikasi */
        #notificationBadge {
            position: absolute;
            top: -8px;
            right: -8px;
            font-size: 0.6rem;
            padding: 2px 6px;
        }

        /* Dropdown menu */
        .notification-dropdown {
            position: absolute !important;
            top: 100% !important;
            right: 0 !important;
            left: auto !important;
            margin-top: 10px !important;
            z-index: 10000 !important;
            min-width: 350px;
            max-width: 90vw;
        }

        /* Toast notification */
        .toast-notification {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: #1a1a1a;
            border-left: 4px solid var(--gold);
            padding: 12px 20px;
            border-radius: 12px;
            color: white;
            font-size: 0.85rem;
            z-index: 10001;
            transform: translateX(400px);
            transition: transform 0.3s ease;
            box-shadow: var(--shadow-lg);
        }

        .toast-notification.show {
            transform: translateX(0);
        }

        .toast-notification.toast-success {
            border-left-color: #2ecc71;
        }

        .toast-notification.toast-warning {
            border-left-color: #f39c12;
        }

        .toast-notification.toast-danger {
            border-left-color: #e74c3c;
        }

        /* Responsive untuk mobile */
        @media (max-width: 768px) {
            .notification-global-wrapper {
                top: 10px;
                right: 10px;
            }

            .notification-dropdown {
                position: fixed !important;
                top: 60px !important;
                right: 10px !important;
                left: 10px !important;
                width: calc(100% - 20px) !important;
                min-width: auto !important;
            }
        }
    </style>
</body>

</html>