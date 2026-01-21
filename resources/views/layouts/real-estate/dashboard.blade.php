<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Real Estate Management') }} - @yield('title', 'Dashboard')</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Bootstrap 5 CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

        <!-- Bootstrap Icons CSS -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

        <!-- Select2 CSS -->
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/6.6.6/css/flag-icons.min.css">

        <!-- Custom CSS -->
        <style>
            :root {
                --font-sans: 'Figtree', sans-serif;
            }

            body {
                font-family: var(--font-sans);
                background-color: #f8f9fa;
            }

            .font-sans {
                font-family: var(--font-sans);
            }

            .sidebar {
                min-height: 100vh;
                background: linear-gradient(180deg, #1e3a5f 0%, #2d5a87 100%);
                box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
            }

            .sidebar-brand {
                padding: 1.5rem;
                border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            }

            .sidebar-brand h4 {
                color: #fff;
                font-weight: 600;
                margin: 0;
            }

            .sidebar-brand span {
                font-size: 0.75rem;
                color: rgba(255, 255, 255, 0.7);
            }

            .sidebar-nav {
                padding: 1rem 0;
            }

            .nav-item {
                margin: 0.25rem 0.75rem;
            }

            .nav-link {
                color: rgba(255, 255, 255, 0.8);
                padding: 0.75rem 1rem;
                border-radius: 0.5rem;
                transition: all 0.2s ease;
                display: flex;
                align-items: center;
                gap: 0.75rem;
            }

            .nav-link:hover {
                color: #fff;
                background: rgba(255, 255, 255, 0.1);
            }

            .nav-link.active {
                color: #fff;
                background: rgba(255, 255, 255, 0.15);
                font-weight: 500;
            }

            .nav-link i {
                font-size: 1.1rem;
                width: 1.5rem;
                text-align: center;
            }

            .nav-section-title {
                color: rgba(255, 255, 255, 0.4);
                font-size: 0.7rem;
                text-transform: uppercase;
                letter-spacing: 0.1em;
                padding: 1rem 1.5rem 0.5rem;
                margin: 0;
            }

            .main-content {
                padding: 2rem;
                min-height: 100vh;
            }

            .card {
                border: none;
                border-radius: 0.75rem;
                box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
                transition: box-shadow 0.2s ease;
            }

            .card:hover {
                box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
            }

            .card-header {
                background: #fff;
                border-bottom: 1px solid rgba(0, 0, 0, 0.05);
                font-weight: 600;
            }

            .stat-card {
                background: #fff;
                border-radius: 0.75rem;
                padding: 1.25rem;
                box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
                transition: transform 0.2s ease, box-shadow 0.2s ease;
            }

            .stat-card:hover {
                transform: translateY(-2px);
                box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
            }

            .stat-icon {
                width: 3rem;
                height: 3rem;
                border-radius: 0.5rem;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 1.25rem;
            }

            .stat-icon-primary {
                background: rgba(13, 110, 253, 0.1);
                color: #0d6efd;
            }

            .stat-icon-success {
                background: rgba(25, 135, 84, 0.1);
                color: #198754;
            }

            .stat-icon-warning {
                background: rgba(255, 193, 7, 0.1);
                color: #ffc107;
            }

            .stat-icon-info {
                background: rgba(13, 202, 240, 0.1);
                color: #0dcaf0;
            }

            .stat-icon-danger {
                background: rgba(220, 53, 69, 0.1);
                color: #dc3545;
            }

            .stat-icon-secondary {
                background: rgba(108, 117, 125, 0.1);
                color: #6c757d;
            }

            .page-header {
                margin-bottom: 1.5rem;
            }

            .page-header h1 {
                font-size: 1.5rem;
                font-weight: 600;
                color: #1e3a5f;
                margin: 0;
            }

            .page-header p {
                color: #6c757d;
                margin: 0.25rem 0 0;
                font-size: 0.875rem;
            }

            .btn-primary {
                background: #1e3a5f;
                border-color: #1e3a5f;
            }

            .btn-primary:hover {
                background: #2d5a87;
                border-color: #2d5a87;
            }

            .quick-action-card {
                background: #fff;
                border-radius: 0.75rem;
                padding: 1.5rem;
                text-align: center;
                text-decoration: none;
                color: #1e3a5f;
                border: 1px solid rgba(0, 0, 0, 0.05);
                transition: all 0.2s ease;
            }

            .quick-action-card:hover {
                background: #f8f9fa;
                border-color: #1e3a5f;
                color: #1e3a5f;
                transform: translateY(-2px);
            }

            .quick-action-card i {
                font-size: 2rem;
                margin-bottom: 0.5rem;
            }

            .activity-item {
                padding: 1rem;
                border-bottom: 1px solid #f0f0f0;
                transition: background 0.2s ease;
            }

            .activity-item:last-child {
                border-bottom: none;
            }

            .activity-item:hover {
                background: #f8f9fa;
            }

            .status-badge {
                padding: 0.25rem 0.75rem;
                border-radius: 50px;
                font-size: 0.75rem;
                font-weight: 500;
            }

            .status-active, .status-confirmed {
                background: rgba(25, 135, 84, 0.1);
                color: #198754;
            }

            .status-pending {
                background: rgba(255, 193, 7, 0.1);
                color: #ccac00;
            }

            .status-reserved {
                background: rgba(13, 202, 240, 0.1);
                color: #0dcaf0;
            }

            .status-inactive, .status-expired, .status-cancelled, .status-terminated {
                background: rgba(108, 117, 125, 0.1);
                color: #6c757d;
            }

            .status-rented, .status-maintenance {
                background: rgba(220, 53, 69, 0.1);
                color: #dc3545;
            }

            .revenue-bar {
                background: linear-gradient(180deg, #1e3a5f 0%, #2d5a87 100%);
                border-radius: 0.5rem 0.5rem 0 0;
                transition: height 0.3s ease;
            }

            .table-action-btn {
                padding: 0.25rem 0.5rem;
                font-size: 0.875rem;
                border-radius: 0.25rem;
            }

            .select2-container--default .select2-selection--single {
                height: 38px;
                padding: 6px 12px;
                border: 1px solid #ced4da;
                border-radius: 0.375rem;
            }

            .select2-container--default .select2-selection--single .select2-selection__rendered {
                line-height: 24px;
            }

            .select2-container--default .select2-selection--single .select2-selection__arrow {
                height: 36px;
            }

            .table-hover tbody tr:hover {
                background-color: #f8f9fa;
            }

            .breadcrumb {
                font-size: 0.875rem;
            }

            .breadcrumb-item a {
                color: #1e3a5f;
                text-decoration: none;
            }

            .breadcrumb-item a:hover {
                text-decoration: underline;
            }

            @media (max-width: 768px) {
                .sidebar {
                    position: fixed;
                    top: 0;
                    left: 0;
                    z-index: 1000;
                    width: 100%;
                    min-height: auto;
                    transform: translateX(-100%);
                    transition: transform 0.3s ease;
                }

                .sidebar.show {
                    transform: translateX(0);
                }

                .main-content {
                    padding: 1rem;
                }
            }
        </style>
    </head>
    <body class="font-sans antialiased">
        <div class="container-fluid">
            <div class="row">
                <!-- Sidebar -->
                <nav class="col-md-3 col-lg-2 d-md-block sidebar collapse show" id="sidebarMenu">
                    <div class="position-sticky pt-3">
                        <div class="sidebar-brand mb-3">
                            <h4><i class="bi bi-building"></i> Real Estate</h4>
                            <span>Management System</span>
                        </div>

                        <ul class="nav flex-column">
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('real-estate.dashboard') ? 'active' : '' }}"
                                   href="{{ route('real-estate.dashboard') }}">
                                    <i class="bi bi-speedometer2"></i>
                                    Dashboard
                                </a>
                            </li>
                        </ul>

                        <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-3 mb-1 text-muted">
                            <span>Property Management</span>
                        </h6>

                        <ul class="nav flex-column mb-2">
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('real-estate.buildings.*') ? 'active' : '' }}"
                                   href="{{ route('real-estate.buildings.index') }}">
                                    <i class="bi bi-building"></i>
                                    Buildings
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('real-estate.units.*') ? 'active' : '' }}"
                                   href="{{ route('real-estate.units.index') }}">
                                    <i class="bi bi-grid-3x3"></i>
                                    Units
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('real-estate.available-units') ? 'active' : '' }}"
                                   href="{{ route('real-estate.available-units') }}">
                                    <i class="bi bi-search"></i>
                                    Available Units
                                </a>
                            </li>
                        </ul>

                        <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-3 mb-1 text-muted">
                            <span>Tenant Management</span>
                        </h6>

                        <ul class="nav flex-column mb-2">
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('real-estate.tenants.*') ? 'active' : '' }}"
                                   href="{{ route('real-estate.tenants.index') }}">
                                    <i class="bi bi-people"></i>
                                    Tenants
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('real-estate.leases.*') ? 'active' : '' }}"
                                   href="{{ route('real-estate.leases.index') }}">
                                    <i class="bi bi-file-earmark-text"></i>
                                    Leases
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('real-estate.bookings.*') ? 'active' : '' }}"
                                   href="{{ route('real-estate.bookings.index') }}">
                                    <i class="bi bi-calendar-check"></i>
                                    Bookings
                                </a>
                            </li>
                        </ul>

                        <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-3 mb-1 text-muted">
                            <span>Reports</span>
                        </h6>

                        <ul class="nav flex-column mb-2">
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('real-estate.reports.financial') ? 'active' : '' }}"
                                   href="{{ route('real-estate.reports.financial') }}">
                                    <i class="bi bi-graph-up"></i>
                                    Financial Reports
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('real-estate.reports.agent-performance') ? 'active' : '' }}"
                                   href="{{ route('real-estate.reports.agent-performance') }}">
                                    <i class="bi bi-person-badge"></i>
                                    Agent Performance
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('real-estate.reports.occupancy') ? 'active' : '' }}"
                                   href="{{ route('real-estate.reports.occupancy') }}">
                                    <i class="bi bi-pie-chart"></i>
                                    Occupancy Report
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('real-estate.expenses.*') ? 'active' : '' }}"
                                   href="{{ route('real-estate.expenses.index') }}">
                                    <i class="bi bi-cash-stack"></i>
                                    Expenses
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('real-estate.ewa-bills.*') ? 'active' : '' }}"
                                   href="{{ route('real-estate.ewa-bills.index') }}">
                                    <i class="bi bi-lightning"></i>
                                    EWA Bills
                                </a>
                            </li>
                        </ul>

                        <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-3 mb-1 text-muted">
                            <span>CRM & Sales</span>
                        </h6>

                        <ul class="nav flex-column mb-2">
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('real-estate.leads.*') ? 'active' : '' }}"
                                   href="{{ route('real-estate.leads.index') }}">
                                    <i class="bi bi-people"></i>
                                    Leads
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('real-estate.agents.*') ? 'active' : '' }}"
                                   href="{{ route('real-estate.agents.index') }}">
                                    <i class="bi bi-person-badge"></i>
                                    Agents
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('real-estate.commissions.*') ? 'active' : '' }}"
                                   href="{{ route('real-estate.commissions.index') }}">
                                    <i class="bi bi-cash-coin"></i>
                                    Commissions
                                </a>
                            </li>
                        </ul>

                        <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-3 mb-1 text-muted">
                            <span>Documents</span>
                        </h6>

                        <ul class="nav flex-column mb-2">
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('real-estate.documents.*') ? 'active' : '' }}"
                                   href="{{ route('real-estate.documents.index') }}">
                                    <i class="bi bi-file-earmark-text"></i>
                                    Documents
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('real-estate.documents.expiring') ? 'active' : '' }}"
                                   href="{{ route('real-estate.documents.expiring') }}">
                                    <i class="bi bi-exclamation-triangle"></i>
                                    Expiring Soon
                                </a>
                            </li>
                        </ul>

                        <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-3 mb-1 text-muted">
                            <span>Administration</span>
                        </h6>

                        <ul class="nav flex-column mb-2">
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('real-estate.users.*') ? 'active' : '' }}"
                                   href="{{ route('real-estate.users.index') }}">
                                    <i class="bi bi-people"></i>
                                    Users
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('real-estate.audit-logs.*') ? 'active' : '' }}"
                                   href="{{ route('real-estate.audit-logs.index') }}">
                                    <i class="bi bi-clock-history"></i>
                                    Audit Logs
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('real-estate.roles.*') ? 'active' : '' }}"
                                   href="{{ route('real-estate.roles.index') }}">
                                    <i class="bi bi-shield-check"></i>
                                    Roles & Permissions
                                </a>
                            </li>
                        </ul>

                        <!-- User Section -->
                        <div class="sidebar-user mt-auto pt-3 px-3">
                            <hr style="border-color: rgba(255,255,255,0.1);">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="bg-white rounded-circle d-flex align-items-center justify-content-center"
                                         style="width: 40px; height: 40px; color: #1e3a5f; font-weight: 600;">
                                        {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-2 text-white">
                                    <div class="small fw-semibold">{{ Auth::user()->name ?? 'User' }}</div>
                                    <div class="small" style="color: rgba(255,255,255,0.6);">{{ Auth::user()->email ?? '' }}</div>
                                </div>
                                <div>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <a href="{{ route('logout') }}"
                                           onclick="event.preventDefault(); this.closest('form').submit();"
                                           class="text-white"
                                           title="Logout">
                                            <i class="bi bi-box-arrow-right"></i>
                                        </a>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </nav>

                <!-- Main Content -->
                <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 main-content">
                    <!-- Top Navbar -->
                    <div class="row align-items-center py-3 mb-4 border-bottom">
                        <div class="col">
                            <button class="btn btn-link d-md-none" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#sidebarMenu" aria-controls="sidebarMenu"
                                    aria-expanded="true" aria-label="Toggle navigation">
                                <i class="bi bi-list fs-3"></i>
                            </button>
                            <nav aria-label="breadcrumb" class="d-none d-md-flex align-items-center">
                                <ol class="breadcrumb mb-0">
                                    <li class="breadcrumb-item">
                                        <a href="{{ route('real-estate.dashboard') }}">Dashboard</a>
                                    </li>
                                    @yield('breadcrumb')
                                </ol>
                            </nav>
                        </div>
                        <div class="col-auto d-flex align-items-center gap-3">
                            <div class="dropdown">
                                <a class="btn btn-light position-relative" href="#" role="button" data-bs-toggle="dropdown">
                                    <i class="bi bi-bell"></i>
                                    <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle">
                                        <span class="visually-hidden">New alerts</span>
                                    </span>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><a class="dropdown-item" href="#">No new notifications</a></li>
                                </ul>
                            </div>
                            <div class="dropdown">
                                <a class="btn btn-light d-flex align-items-center gap-2" href="#" role="button" data-bs-toggle="dropdown">
                                    <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center"
                                         style="width: 32px; height: 32px; color: #fff; font-weight: 600; font-size: 0.875rem;">
                                        {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                                    </div>
                                    <span class="d-none d-sm-inline">{{ Auth::user()->name ?? 'User' }}</span>
                                    <i class="bi bi-chevron-down"></i>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><a class="dropdown-item" href="{{ route('profile.show') }}"><i class="bi bi-person me-2"></i>Profile</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <a class="dropdown-item" href="{{ route('logout') }}"
                                               onclick="event.preventDefault(); this.closest('form').submit();">
                                                <i class="bi bi-box-arrow-right me-2"></i>Logout
                                            </a>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Page Content -->
                    @yield('content')
                </main>
            </div>
        </div>

        <!-- Bootstrap 5 JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

        <!-- jQuery (required for Select2) -->
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

        <!-- Select2 JS -->
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

        <!-- Initialize Select2 -->
        <script>
            $(document).ready(function() {
                $('.select2').select2({
                    placeholder: 'Select an option',
                    allowClear: true
                });
            });
        </script>
    </body>
</html>

