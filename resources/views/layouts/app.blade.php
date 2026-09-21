<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GMU - Research Project Management System</title>
    <!-- Bootstrap 5.3 CSS (local) -->
    <link href="{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <!-- Bootstrap Icons (local) -->
    <link href="{{ asset('vendor/bootstrap/css/bootstrap-icons.css') }}" rel="stylesheet">
    <!-- Page Transition Loader -->
    <style>
        #page-loader {
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(15, 62, 46, 0.85);
            z-index: 99999;
            display: none;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            gap: 16px;
            backdrop-filter: blur(4px);
        }
        #page-loader.active { display: flex; }
        #page-loader .spinner {
            width: 40px; height: 40px;
            border: 4px solid rgba(255,255,255,0.2);
            border-top-color: #ffffff;
            border-radius: 50%;
            animation: spin 0.7s linear infinite;
        }
        #page-loader .loader-text {
            color: #ffffff;
            font-size: 0.85rem;
            font-weight: 500;
            letter-spacing: 0.5px;
        }
        @keyframes spin { to { transform: rotate(360deg); } }
    </style>
    <style>
        :root {
            --gmu-primary: #0f3e2e;
            --gmu-primary-dark: #0a2b20;
            --gmu-secondary: #1e5641;
            --gmu-accent: #2e8b57;
            --gmu-gold: #d4af37;
            --gmu-gold-dark: #b89324;
            --gmu-gold-light: #fef9e7;
            --gmu-bg: #f6f8fa;
            --gmu-card-border: #e2e8f0;
        }
        html {
            overflow-x: hidden;
        }
        body {
            background-color: var(--gmu-bg);
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            color: #1e293b;
            letter-spacing: -0.01em;
        }
        /* Navbar Styling */
        .navbar-gmu {
            background: linear-gradient(135deg, var(--gmu-primary) 0%, var(--gmu-primary-dark) 100%);
            box-shadow: 0 4px 20px -2px rgba(15, 62, 46, 0.35);
            border-bottom: 2px solid var(--gmu-gold);
        }
        /* Fixed navbar on desktop */

        .brand-text {
            color: #ffffff;
            font-weight: 800;
            letter-spacing: 0.8px;
            font-size: 0.95rem;
        }
        .brand-sub {
            color: var(--gmu-gold);
            font-size: 0.72rem;
            font-weight: 500;
            letter-spacing: 0.2px;
        }
        .badge-call-cycle {
            background: rgba(212, 175, 55, 0.15);
            color: var(--gmu-gold);
            border: 1px solid rgba(212, 175, 55, 0.4);
            font-weight: 600;
        }
        .badge-role {
            background: rgba(255, 255, 255, 0.15);
            color: #ffffff;
            border: 1px solid rgba(255, 255, 255, 0.3);
            font-weight: 700;
            letter-spacing: 0.5px;
        }
        /* Navigation Links */
        .navbar-gmu .nav-link {
            color: rgba(255, 255, 255, 0.85) !important;
            font-weight: 500;
            font-size: 0.88rem;
            padding: 0.45rem 0.75rem !important;
            border-radius: 6px;
            transition: all 0.2s ease-in-out;
            white-space: nowrap;
        }
        .navbar-gmu .nav-link:hover {
            color: #ffffff !important;
            background: rgba(255, 255, 255, 0.08);
        }
        .navbar-gmu .nav-link.active-link {
            color: var(--gmu-gold) !important;
            background: rgba(212, 175, 55, 0.15);
            font-weight: 700 !important;
        }
        #navbarOffcanvas .nav-link.active-link {
            color: var(--gmu-gold) !important;
            background: rgba(212, 175, 55, 0.2);
            border-left: 3px solid var(--gmu-gold);
            font-weight: 700 !important;
        }
        /* Card Styling */
        .card-custom {
            border: 1px solid var(--gmu-card-border);
            border-radius: 12px;
            background: #ffffff;
            box-shadow: 0 2px 12px rgba(15, 62, 46, 0.04);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .card-custom:hover {
            box-shadow: 0 6px 20px rgba(15, 62, 46, 0.08);
        }
        .card-custom-header {
            background: #ffffff;
            border-bottom: 1px solid #edf2f7;
            padding: 1rem 1.25rem;
            border-top-left-radius: 12px;
            border-top-right-radius: 12px;
        }
        /* Metric KPI Cards (Clean Academic Minimalist) */
        .stat-bubble {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            background: #f8fafc;
            color: #334155;
            border: 1px solid #e2e8f0;
        }

        /* Tables & Lists */
        .table {
            --bs-table-hover-bg: rgba(15, 62, 46, 0.02);
        }
        .table > thead th {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            font-weight: 700;
            color: #64748b;
            background-color: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            padding: 0.75rem 1rem;
        }
        .table > tbody td {
            padding: 0.85rem 1rem;
            vertical-align: middle;
            border-bottom: 1px solid #f1f5f9;
        }
        /* Clean Minimalist Badges */
        .badge {
            font-weight: 600 !important;
            border-radius: 6px !important;
            box-shadow: none !important;
        }
        /* Preserve navbar role and call badges */
        .badge-role {
            background: rgba(255, 255, 255, 0.15) !important;
            color: #ffffff !important;
            border: 1px solid rgba(255, 255, 255, 0.3) !important;
        }
        .badge-call-cycle {
            background: rgba(212, 175, 55, 0.15) !important;
            color: var(--gmu-gold) !important;
            border: 1px solid rgba(212, 175, 55, 0.4) !important;
        }
        /* Clean Dark / White Buttons across all pages */
        .btn-success, .btn-primary, .btn-dark {
            background-color: #0f172a !important;
            border-color: #0f172a !important;
            color: #ffffff !important;
        }
        .btn-success:hover, .btn-primary:hover, .btn-dark:hover {
            background-color: #334155 !important;
            border-color: #334155 !important;
            color: #ffffff !important;
        }
        .btn-warning {
            background-color: #ffffff !important;
            border-color: #cbd5e1 !important;
            color: #0f172a !important;
        }
        .btn-warning:hover {
            background-color: #f1f5f9 !important;
            border-color: #94a3b8 !important;
            color: #0f172a !important;
        }
        .btn-outline-success, .btn-outline-primary, .btn-outline-dark, .btn-outline-secondary {
            background-color: #ffffff !important;
            border-color: #cbd5e1 !important;
            color: #0f172a !important;
        }
        .btn-outline-success:hover, .btn-outline-primary:hover, .btn-outline-dark:hover, .btn-outline-secondary:hover {
            background-color: #f1f5f9 !important;
            border-color: #94a3b8 !important;
            color: #0f172a !important;
        }
        /* Dropdowns */
        .dropdown-menu {
            border: 1px solid #e2e8f0;
            box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.12);
            border-radius: 10px;
        }
    /* Select Dropdowns - Modern Dark Theme */
    .custom-select-wrapper {
        position: relative;
        width: 100%;
    }
    .custom-select-wrapper .form-select {
        position: absolute !important;
        opacity: 0 !important;
        height: 0 !important;
        width: 0 !important;
        min-height: 0 !important;
        min-width: 0 !important;
        padding: 0 !important;
        margin: 0 !important;
        border: none !important;
        background: none !important;
        background-image: none !important;
        pointer-events: none !important;
        overflow: hidden !important;
    }
    .custom-select-trigger {
        display: flex !important;
        align-items: center;
        justify-content: space-between;
        width: 100%;
        padding: 0.45rem 0.75rem;
        font-size: 0.85rem;
        font-weight: 500;
        color: #0f172a !important;
        background-color: #ffffff !important;
        background-image: none !important;
        border: 1.5px solid #cbd5e1 !important;
        border-radius: 8px !important;
        cursor: pointer;
        transition: all 0.2s ease;
        min-height: 38px;
        box-shadow: none !important;
    }
    .custom-select-trigger:hover {
        border-color: #94a3b8 !important;
        box-shadow: 0 1px 4px rgba(0,0,0,0.08) !important;
    }
    .custom-select-trigger.open {
        border-color: var(--gmu-primary, #0f3e2e) !important;
        box-shadow: 0 0 0 3px rgba(15,62,46,0.12) !important;
    }
    .custom-select-trigger .placeholder {
        color: #94a3b8;
        font-weight: 400;
    }
    .custom-select-trigger .arrow {
        font-size: 0.6rem;
        color: #64748b;
        transition: transform 0.2s ease;
        margin-left: 8px;
    }
    .custom-select-trigger.open .arrow {
        transform: rotate(180deg);
    }
    .custom-select-options {
        position: fixed;
        background: #ffffff;
        border: 1.5px solid #e2e8f0;
        border-radius: 8px;
        box-shadow: 0 8px 24px -4px rgba(0,0,0,0.15);
        z-index: 99999;
        max-height: 200px;
        overflow-y: auto;
        display: none;
        padding: 4px;
    }
    .custom-select-options.show {
        display: block;
    }
    .custom-select-option {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 8px 12px;
        font-size: 0.85rem;
        color: #0f172a;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.15s ease;
    }
    .custom-select-option:hover {
        background: #f1f5f9;
        color: var(--gmu-primary, #0f3e2e);
    }
    .custom-select-option.selected {
        background: var(--gmu-primary, #0f3e2e);
        color: #ffffff;
    }
    .custom-select-option.selected:hover {
        background: #1e5641;
        color: #ffffff;
    }
    .custom-select-option .check-icon {
        margin-left: auto;
        display: none;
        font-size: 0.75rem;
    }
    .custom-select-option.selected .check-icon {
        display: inline;
    }
    .custom-select-sm .custom-select-trigger {
        padding: 0.3rem 0.6rem;
        font-size: 0.8rem;
        min-height: 32px;
        border-radius: 6px;
    }
    .custom-select-sm .custom-select-option {
        padding: 6px 10px;
        font-size: 0.8rem;
    }
        /* Profile dropdown: fixed so it doesn't shift page content */
        .profile-dropdown-menu.show {
            position: fixed !important;
            right: 1rem !important;
            left: auto !important;
        }
        .dropdown-item {
            font-size: 0.86rem;
            border-radius: 6px;
            padding: 0.45rem 0.85rem;
        }
        .dropdown-item:hover {
            background-color: #f1f5f9;
            color: var(--gmu-primary);
        }

        /* Mobile Responsive Media Queries */
        @media (max-width: 991.98px) {
            .brand-text {
                font-size: 0.82rem !important;
                letter-spacing: 0.3px !important;
            }
            .brand-sub {
                font-size: 0.65rem !important;
            }
            .navbar-brand {
                max-width: 72vw;
            }
            .navbar-brand i {
                font-size: 1.35rem !important;
            }
            .card-custom {
                border-radius: 8px;
                padding: 0.75rem !important;
            }
            .table-responsive {
                border-radius: 8px;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }
            .table-responsive table {
                min-width: 560px;
            }
            .dropdown-menu {
                max-width: 92vw !important;
            }
            #navbarNav {
                display: none !important;
            }
            /* Fixed navbar on mobile */
            .navbar-gmu {
                position: fixed !important;
                top: 0;
                left: 0;
                right: 0;
                z-index: 1060 !important;
                width: 100%;
            }
            /* Add padding below navbar for fixed position */
            body {
                padding-top: 70px !important;
            }
            /* Smaller headings on mobile */
            h3.fw-bold {
                font-size: 1.15rem !important;
            }
            h5.fw-bold, h6.fw-bold {
                font-size: 0.95rem !important;
            }
            /* Smaller badges on mobile */
            .badge {
                font-size: 0.7rem !important;
                padding: 0.3em 0.55em !important;
            }
            /* Hide subtitle text on mobile header */
            .navbar-gmu .d-flex .text-muted.small {
                display: none !important;
            }
            /* Stack buttons cleanly with wrapping */
            .d-flex.gap-2 {
                flex-wrap: wrap;
            }
            /* Page action headers wrap cleanly on mobile */
            .page-header-actions,
            .mb-4.d-flex.justify-content-between.align-items-center,
            .mb-3.d-flex.justify-content-between.align-items-center {
                flex-wrap: wrap !important;
                gap: 0.75rem !important;
            }
            /* Compact KPI cards */
            .card-custom.p-4 {
                padding: 0.85rem !important;
            }
            /* Compact quick actions */
            .d-flex.flex-wrap.gap-2 .btn {
                font-size: 0.8rem !important;
                padding: 0.4rem 0.65rem !important;
            }
            /* Smaller table text */
            .table td, .table th {
                padding: 0.6rem 0.5rem !important;
                font-size: 0.82rem !important;
            }
            /* KPI cards 2x2 on mobile (dashboard only) */
            #kpi-cards > div[class*="col-"] {
                flex: 0 0 50% !important;
                max-width: 50% !important;
            }
            .stat-bubble {
                width: 32px !important;
                height: 32px !important;
                border-radius: 6px !important;
            }
            .stat-bubble i {
                font-size: 0.9rem !important;
            }
            .card-custom.p-3 {
                padding: 0.6rem !important;
            }
            /* Modals: mobile friendly width */
            .modal-dialog {
                max-width: 95vw !important;
                margin: 0.5rem auto !important;
            }
            .modal-content {
                border-radius: 10px !important;
            }
        }

        /* Project Lifecycle Timeline */
        .timeline-container {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            position: relative;
            padding: 20px 0;
        }
        .timeline-container::before {
            content: '';
            position: absolute;
            top: 38px;
            left: 5%;
            right: 5%;
            height: 4px;
            background: #e9ecef;
            z-index: 0;
        }
        @media (max-width: 767.98px) {
            .timeline-container {
                flex-wrap: wrap;
                row-gap: 30px;
            }
            .timeline-container::before {
                display: none; /* Hide main line on mobile */
            }
        }
        .timeline-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            flex: 1;
            position: relative;
            z-index: 1;
        }
        @media (max-width: 767.98px) {
            .timeline-item {
                flex: 0 0 33.33%; /* 3 items per row */
            }
            /* Segment lines between items on mobile */
            .timeline-item::before {
                content: '';
                position: absolute;
                top: 22px; /* Center of the 48px circle */
                left: 50%;
                width: 100%;
                height: 4px;
                background: #e9ecef;
                z-index: -1;
            }
            /* Hide the line going out of the 3rd and 6th items (end of row) */
            .timeline-item:nth-child(3n)::before {
                display: none;
            }
        }
        .timeline-circle {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            border: 3px solid #dee2e6;
            background: #ffffff;
            color: #6c757d;
            margin-bottom: 8px;
            transition: all 0.3s ease;
        }
        .timeline-circle.completed {
            background: #198754;
            border-color: #198754;
            color: #ffffff;
        }
        .timeline-circle.current {
            background: #198754;
            border-color: #198754;
            color: #ffffff;
            box-shadow: 0 0 0 4px rgba(25, 135, 84, 0.25);
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(25, 135, 84, 0.4); }
            70% { box-shadow: 0 0 0 10px rgba(25, 135, 84, 0); }
            100% { box-shadow: 0 0 0 0 rgba(25, 135, 84, 0); }
        }
        .timeline-label {
            font-size: 0.78rem;
            font-weight: 500;
            color: #6c757d;
            max-width: 90px;
        }
        .timeline-label.active {
            font-weight: 700;
            color: #198754;
        }
        .timeline-date {
            font-size: 0.68rem;
            color: #adb5bd;
            margin-top: 2px;
        }
    </style>
    @yield('styles')
</head>
<body class="d-flex flex-column min-vh-100">

    <!-- Page Transition Loader -->
    <div id="page-loader">
        <div class="spinner"></div>
        <div class="loader-text">Loading...</div>
    </div>

    <!-- Navigation Header -->
    <nav class="navbar navbar-expand-lg navbar-gmu py-2 sticky-lg-top @yield('navbar-class')" style="z-index: 1050;">
        <div class="container @yield('navbar-container-class')">
            <!-- Brand Identity -->
            <a class="navbar-brand d-flex align-items-center me-4" href="{{ route('dashboard') }}">
                <i class="bi bi-journal-bookmark-fill fs-2 text-warning me-2"></i>
                <div>
                    <div class="brand-text fs-6">GAMBELLA UNIVERSITY</div>
                    <div class="brand-sub">Research Project Management System</div>
                </div>
            </a>

            @auth
            <!-- Mobile Right Hamburger Toggler -->
            <div class="d-flex align-items-center gap-2 d-lg-none ms-auto">
                <button id="mobileNavToggler" class="btn text-white p-1 border-0" type="button" data-bs-toggle="offcanvas" data-bs-target="#navbarOffcanvas" aria-controls="navbarOffcanvas" aria-label="Toggle navigation">
                    <i id="mobileNavIcon" class="bi bi-list fs-1 text-white"></i>
                </button>
            </div>

            <!-- Desktop Navigation Menu (Large screens) -->
            <div class="collapse navbar-collapse d-none d-lg-flex" id="navbarNav">
                <!-- Clean Role-Based Navigation Items -->
                <ul class="navbar-nav me-auto ms-2 gap-1">

                    {{-- 1. Dashboard --}}
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('dashboard') ? 'active-link' : '' }}" href="{{ route('dashboard') }}">
                            <i class="bi bi-speedometer2 me-1"></i> Dashboard
                        </a>
                    </li>

                    {{-- 1a. Admin Console Links (Exact 4-Item Layout) --}}
                    @if(Auth::user()->role === 'admin')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.users') ? 'active-link' : '' }}" href="{{ route('admin.users') }}">
                            <i class="bi bi-people me-1"></i> Users
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.thematic-areas') ? 'active-link' : '' }}" href="{{ route('admin.thematic-areas') }}">
                            <i class="bi bi-diagram-3 me-1"></i> Thematics
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle {{ request()->routeIs('admin.colleges', 'admin.departments') ? 'active-link' : '' }}" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-bank me-1"></i> Academic Units
                        </a>
                        <ul class="dropdown-menu shadow-sm">
                            <li><a class="dropdown-item small" href="{{ route('admin.colleges') }}"><i class="bi bi-bank me-2 text-dark"></i>Colleges</a></li>
                            <li><a class="dropdown-item small" href="{{ route('admin.departments') }}"><i class="bi bi-building me-2 text-dark"></i>Departments</a></li>
                        </ul>
                    </li>
                    @endif

                    {{-- 2. Research Projects (simple link) — not for TM, Reviewer, or Admin --}}
                    @if(Auth::user()->hasPermission('view_projects') && !in_array(Auth::user()->role, ['tm', 'reviewer', 'admin']))
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('projects.*') ? 'active-link' : '' }}" href="{{ route('projects.index') }}">
                            <i class="bi bi-folder2-open me-1"></i> Projects
                        </a>
                    </li>
                    @endif

                    {{-- 2a. My Projects (TM) --}}
                    @if(Auth::user()->role === 'tm')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('projects.*') ? 'active-link' : '' }}" href="{{ route('projects.index') }}">
                            <i class="bi bi-folder2-open me-1"></i> My Projects
                        </a>
                    </li>
                    @endif

                    {{-- 2b. My Evaluations (Reviewer) --}}
                    @if(Auth::user()->role === 'reviewer')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('evaluations.*') ? 'active-link' : '' }}" href="{{ route('evaluations.index') }}">
                            <i class="bi bi-clipboard-check me-1"></i> My Evaluations
                        </a>
                    </li>
                    @endif

                    {{-- 2c. Progress Reports (PI and TM) --}}
                    @if(Auth::user()->hasPermission('submit_progress_report'))
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('progress.*') ? 'active-link' : '' }}" href="{{ route('progress.index') }}">
                            <i class="bi bi-graph-up me-1"></i> Progress Reports
                        </a>
                    </li>
                    @endif

                    {{-- 2d. Extensions & Amendments (PI Requestor) --}}
                    @if(Auth::user()->hasAnyPermission(['request_extension', 'request_amendment']) && !Auth::user()->hasAnyPermission(['approve_extensions', 'approve_amendments']))
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('extensions.*') ? 'active-link' : '' }}" href="{{ route('extensions.index') }}">
                            <i class="bi bi-clock-history me-1"></i> Extensions
                        </a>
                    </li>
                    @endif

                    {{-- 3. Department Head Screening --}}
                    @if(Auth::user()->hasPermission('screen_proposals'))
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('dh.screening') ? 'active-link' : '' }}" href="{{ route('dh.screening') }}">
                            <i class="bi bi-ui-checks me-1"></i> DH Screening
                        </a>
                    </li>
                    @endif

                    {{-- 4. Coordinator Hub (simple link) --}}
                    @if(Auth::user()->hasPermission('assign_reviewer'))
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('coordinator.*') ? 'active-link' : '' }}" href="{{ route('coordinator.hub') }}">
                            <i class="bi bi-briefcase me-1"></i> Coordinator Hub
                        </a>
                    </li>
                    @endif

                    {{-- 5. Dean Approvals --}}
                    @if(Auth::user()->role === 'dean')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('dean.approvals') ? 'active-link' : '' }}" href="{{ route('dean.approvals') }}">
                            <i class="bi bi-bank me-1"></i> Dean Approvals (&lt;500k)
                        </a>
                    </li>
                    @endif

                    {{-- 6. IRERC Ethics Panel --}}
                    @if(Auth::user()->hasPermission('ethics_review'))
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('irerc.panel') ? 'active-link' : '' }}" href="{{ route('irerc.panel') }}">
                            <i class="bi bi-shield-exclamation me-1"></i> IRERC Ethics Panel
                        </a>
                    </li>
                    @endif

                    {{-- 7. RCSC Portal (simple link) --}}
                    @if(Auth::user()->hasPermission('view_rcsc_portal'))
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('rcsc.*') ? 'active-link' : '' }}" href="{{ route('rcsc.portal') }}">
                            <i class="bi bi-shield-lock me-1"></i> RCSC Portal
                        </a>
                    </li>
                    @endif



                    {{-- 8. Extensions --}}
                    @if(Auth::user()->hasAnyPermission(['approve_extensions', 'approve_amendments']))
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('extensions.*') ? 'active-link' : '' }}" href="{{ route('extensions.index') }}">
                            <i class="bi bi-clock-history me-1"></i> Extensions
                        </a>
                    </li>
                    @endif

                    {{-- 9. Finance Disbursement --}}
                    @if(Auth::user()->hasPermission('process_disbursement'))
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('finance.*') ? 'active-link' : '' }}" href="{{ route('finance.disbursement') }}">
                            <i class="bi bi-cash-stack me-1"></i> Finance Disbursement
                        </a>
                    </li>
                    @endif

                </ul>

                <!-- User Profile & Action Controls -->
                <div class="d-flex align-items-center text-white ms-auto gap-3">
                    {{-- Notifications Bell Dropdown (SCR-17) --}}
                    {{-- Notifications loaded via LoadNotifications middleware --}}

                    <div class="dropdown me-3">
                        <button class="btn btn-link p-0 border-0 position-relative d-flex align-items-center text-decoration-none" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Notifications">
                            <i class="bi bi-bell-fill fs-5 text-warning"></i>
                            @if($notifCount > 0)
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size:0.65rem; margin-top: -2px;">
                                    {{ $notifCount > 99 ? '99+' : $notifCount }}
                                </span>
                            @endif
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end p-2 shadow-lg" style="width: 320px; max-height: 380px; overflow-y: auto;">
                            <li class="dropdown-header fw-bold d-flex justify-content-between align-items-center border-bottom pb-2 mb-2">
                                <span><i class="bi bi-bell me-1 text-warning"></i> Notifications Center</span>
                                <span class="badge bg-dark rounded-pill">{{ $notifCount }}</span>
                            </li>
                            @forelse($notifItems as $item)
                                <li class="mb-2">
                                    <a class="dropdown-item rounded p-2 text-wrap bg-light border-start border-3 border-warning" href="#">
                                        <div class="d-flex align-items-start gap-2">
                                            <i class="bi {{ $item['icon'] }} fs-6 mt-1"></i>
                                            <div>
                                                <div class="fw-bold small text-dark">{{ $item['title'] }}</div>
                                                <div class="small text-muted" style="font-size: 0.78rem;">{{ $item['desc'] }}</div>
                                                <div class="text-muted" style="font-size: 0.68rem;"><i class="bi bi-clock me-1"></i>{{ $item['time'] }}</div>
                                            </div>
                                        </div>
                                    </a>
                                </li>
                            @empty
                                <li class="text-center py-3 text-muted small">
                                    <i class="bi bi-check-all fs-4 d-block text-success"></i>
                                    No pending notification alerts.
                                </li>
                            @endforelse
                        </ul>
                    </div>

                    {{-- Academic Call Cycle Badge --}}
                    <div class="d-none d-xl-flex align-items-center rounded-pill px-3 py-2 me-3" style="font-size:0.85rem; fw-semibold; cursor:pointer; transition: transform 0.2s; background: linear-gradient(135deg, #0d1b2a 0%, #1b2838 100%); border: 1px solid #1ecbff33;" data-bs-toggle="modal" data-bs-target="#calendarModal" title="View Academic Calendar">
                        <i class="bi bi-calendar2-check me-2 text-info"></i> <span class="text-white fw-semibold">AY 2026/2027</span>
                    </div>

                    {{-- User Profile & Demo Role Switcher Dropdown --}}
                    <div class="dropdown border-start border-secondary ps-3">
                        <a href="#" class="text-white text-decoration-none d-flex align-items-center gap-2 dropdown-toggle-custom" id="userProfileMenuBtn" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="text-end">
                                <div class="fw-bold small lh-1 text-white" style="white-space: nowrap;">{{ preg_replace('/\s*\([^)]*\)/', '', Auth::user()->name) }}</div>
                                <span class="badge badge-role mt-1" style="font-size: 0.65rem; padding: 2px 6px; cursor: pointer;">{{ strtoupper(Auth::user()->role) }} ▾</span>
                            </div>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end p-2 shadow-lg profile-dropdown-menu" aria-labelledby="userProfileMenuBtn" style="min-width: 290px; max-height: 480px; overflow-y: auto; z-index: 1050;">
                            {{-- Account Mini Card --}}
                            <li class="p-2 mb-2 rounded bg-light border">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle bg-dark text-white d-flex align-items-center justify-content-center fw-bold" style="width: 36px; height: 36px; font-size: 0.85rem;">
                                        {{ collect(explode(' ', preg_replace('/\s*\([^)]*\)/', '', Auth::user()->name)))->map(fn($p) => substr($p, 0, 1))->take(2)->join('') ?: 'GMU' }}
                                    </div>
                                    <div class="flex-grow-1 text-truncate">
                                        <div class="fw-bold text-dark small text-truncate">{{ preg_replace('/\s*\([^)]*\)/', '', Auth::user()->name) }}</div>
                                        <div class="text-muted" style="font-size: 0.72rem;">{{ Auth::user()->email }}</div>
                                    </div>
                                </div>
                                <div class="mt-2 pt-2 border-top d-flex justify-content-between align-items-center">
                                    <span class="badge bg-dark font-monospace" style="font-size: 0.65rem;">{{ Auth::user()->staff_id ?? 'GMU-STAFF' }}</span>
                                    <a href="{{ route('profile') }}" class="btn btn-sm d-flex align-items-center gap-1 fw-semibold text-white" style="font-size: 0.75rem; background: var(--gmu-secondary); border: 1px solid rgba(255,255,255,0.2); padding: 3px 10px; border-radius: 6px;">
                                        <i class="bi bi-person-circle"></i> View Profile
                                    </a>
                                </div>
                            </li>

                            <li class="dropdown-header fw-bold text-dark px-1 py-1 d-flex justify-content-between align-items-center">
                                <span><i class="bi bi-arrow-repeat me-1 text-primary"></i> Switch Demo Role</span>
                                <span class="badge bg-warning-subtle text-warning border border-warning-subtle" style="font-size: 0.6rem;">DEMO</span>
                            </li>

                            @php
                                $demoRoles = [
                                    'pi'          => ['name' => 'Principal Investigator', 'icon' => 'bi-person'],
                                    'tm'          => ['name' => 'Team Member',           'icon' => 'bi-people'],
                                    'dh'          => ['name' => 'Department Head',       'icon' => 'bi-person-workspace'],
                                    'coordinator' => ['name' => 'Coordinator',           'icon' => 'bi-person-badge'],
                                    'reviewer'    => ['name' => 'Peer Reviewer',         'icon' => 'bi-eye'],
                                    'dean'        => ['name' => 'College Dean',          'icon' => 'bi-bank'],
                                    'irerc'       => ['name' => 'IRERC Ethics',          'icon' => 'bi-shield-exclamation'],
                                    'vparttcs'    => ['name' => 'Vice President',        'icon' => 'bi-person-badge'],
                                    'rcsc'        => ['name' => 'RCSC Chair',            'icon' => 'bi-shield-lock'],
                                    'finance'     => ['name' => 'Finance Office',        'icon' => 'bi-cash'],
                                    'admin'       => ['name' => 'System Admin',          'icon' => 'bi-gear'],
                                ];
                            @endphp

                            @foreach($demoRoles as $rKey => $rMeta)
                                @php $isActiveRole = (Auth::user()->role === $rKey); @endphp
                                <li>
                                    <a class="dropdown-item small d-flex align-items-center justify-content-between py-1 px-2 my-1 rounded {{ $isActiveRole ? 'bg-success text-white fw-bold' : '' }}" href="{{ route('switch-role', $rKey) }}">
                                        <span><i class="bi {{ $rMeta['icon'] }} me-2 {{ $isActiveRole ? 'text-white' : 'text-muted' }}"></i>{{ $rMeta['name'] }}</span>
                                        @if($isActiveRole)
                                            <i class="bi bi-check-circle-fill text-white"></i>
                                        @endif
                                    </a>
                                </li>
                            @endforeach

                            <li><hr class="dropdown-divider my-2"></li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST" class="d-inline m-0">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger small fw-bold confirm-btn"
                                            data-confirm-title="Sign Out"
                                            data-confirm-message="Are you sure you want to sign out of your account?"
                                            data-confirm-icon="bi-box-arrow-right"
                                            data-confirm-color="text-danger"
                                            data-confirm-btn-text="Yes, Sign Out"
                                            data-confirm-btn-class="btn-danger">
                                        <i class="bi bi-box-arrow-right me-2"></i> Sign Out
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            @endauth
        </div>
    </nav>

    @auth
    <!-- Mobile Offcanvas Right Sidebar -->
    <div class="offcanvas offcanvas-end text-white d-lg-none" tabindex="-1" id="navbarOffcanvas" aria-labelledby="navbarOffcanvasLabel" style="background-color: var(--gmu-primary-dark); max-width: 320px; width: 78vw;">
        <div class="offcanvas-header border-bottom border-secondary pb-2 pt-3 px-3">
            <div class="d-flex align-items-center">
                <i class="bi bi-journal-bookmark-fill fs-3 text-warning me-2"></i>
                <div>
                    <div class="fw-bold text-white small lh-1">GAMBELLA UNIVERSITY</div>
                    <div class="text-warning" style="font-size: 0.68rem;">RPMS Mobile Portal</div>
                </div>
            </div>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        
        <div class="offcanvas-body p-3 d-flex flex-column" style="overflow-y: auto;">
            {{-- User info card — dark theme matching sidebar --}}
            <div class="p-2 mb-3 rounded-3 border" style="background: rgba(255,255,255,0.08); border-color: rgba(255,255,255,0.15) !important;">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-circle bg-dark text-white d-flex align-items-center justify-content-center fw-bold flex-shrink-0" style="width: 42px; height: 42px; font-size: 0.9rem;">
                        {{ collect(explode(' ', preg_replace('/\s*\([^)]*\)/', '', Auth::user()->name)))->map(fn($p) => substr($p, 0, 1))->take(2)->join('') ?: 'GMU' }}
                    </div>
                    <div class="flex-grow-1 text-truncate">
                        <div class="fw-bold text-white small text-truncate">{{ preg_replace('/\s*\([^)]*\)/', '', Auth::user()->name) }}</div>
                        <div class="text-white-50 text-truncate" style="font-size: 0.72rem;">{{ Auth::user()->email }}</div>
                    </div>
                </div>
                <div class="mt-2 pt-2 d-flex justify-content-between align-items-center" style="border-top: 1px solid rgba(255,255,255,0.15);">
                    <span class="badge bg-dark font-monospace" style="font-size: 0.65rem;">{{ Auth::user()->staff_id ?? 'GMU-STAFF' }}</span>
                    <a href="{{ route('profile') }}" class="btn btn-sm d-flex align-items-center gap-1 fw-semibold text-white" style="font-size: 0.75rem; background: var(--gmu-secondary); border: 1px solid rgba(255,255,255,0.2); padding: 3px 10px; border-radius: 6px;">
                        <i class="bi bi-person-circle"></i> View Profile
                    </a>
                </div>
            </div>

            {{-- Mobile Academic Call Badge --}}
            <div class="mb-3 py-2 px-3 rounded-pill d-flex align-items-center justify-content-center shadow-sm mx-2" style="background: linear-gradient(135deg, #0d1b2a 0%, #1b2838 100%); border: 1px solid #1ecbff33; font-size: 0.8rem; font-weight: 600; cursor:pointer;" data-bs-toggle="modal" data-bs-target="#calendarModal" data-bs-dismiss="offcanvas">
                <i class="bi bi-calendar2-check me-2 text-info"></i> <span class="text-white">AY 2026/2027</span>
            </div>

            {{-- Main Navigation Links --}}
            <ul class="nav nav-pills flex-column gap-1 mb-3">
                <li class="nav-item">
                    <a class="nav-link text-white py-2 px-2 {{ request()->routeIs('dashboard') ? 'active-link' : '' }}" href="{{ route('dashboard') }}" style="font-size: 0.85rem;">
                        <i class="bi bi-speedometer2 me-2 text-white-50"></i> Dashboard
                    </a>
                </li>
                @if(Auth::user()->hasPermission('view_projects'))
                <li class="nav-item">
                    <a class="nav-link text-white py-2 px-2 {{ request()->routeIs('projects.*') ? 'active-link' : '' }}" href="{{ route('projects.index') }}" style="font-size: 0.85rem;">
                        <i class="bi bi-folder2-open me-2 text-white-50"></i> Projects
                    </a>
                </li>
                @endif
                @if(Auth::user()->role === 'reviewer')
                <li class="nav-item">
                    <a class="nav-link text-white py-2 px-2 {{ request()->routeIs('evaluations.*') ? 'active-link' : '' }}" href="{{ route('evaluations.index') }}" style="font-size: 0.85rem;">
                        <i class="bi bi-clipboard-check me-2 text-white-50"></i> My Evaluations
                    </a>
                </li>
                @endif
                @if(Auth::user()->hasPermission('submit_progress_report'))
                <li class="nav-item">
                    <a class="nav-link text-white py-2 px-2 {{ request()->routeIs('progress.*') ? 'active-link' : '' }}" href="{{ route('progress.index') }}" style="font-size: 0.85rem;">
                        <i class="bi bi-graph-up me-2 text-white-50"></i> Progress Reports
                    </a>
                </li>
                @endif
                @if(Auth::user()->hasAnyPermission(['request_extension', 'request_amendment']) && !Auth::user()->hasAnyPermission(['approve_extensions', 'approve_amendments']))
                <li class="nav-item">
                    <a class="nav-link text-white py-2 px-2 {{ request()->routeIs('extensions.*') ? 'active-link' : '' }}" href="{{ route('extensions.index') }}" style="font-size: 0.85rem;">
                        <i class="bi bi-clock-history me-2 text-white-50"></i> Extensions
                    </a>
                </li>
                @endif
                @if(Auth::user()->hasPermission('screen_proposals'))
                <li class="nav-item">
                    <a class="nav-link text-white py-2 px-2 {{ request()->routeIs('dh.screening') ? 'active-link' : '' }}" href="{{ route('dh.screening') }}" style="font-size: 0.85rem;">
                        <i class="bi bi-ui-checks me-2 text-white-50"></i> DH Screening
                    </a>
                </li>
                @endif
                @if(Auth::user()->hasPermission('assign_reviewer'))
                <li class="nav-item">
                    <a class="nav-link text-white py-2 px-2 {{ request()->routeIs('coordinator.*') ? 'active-link' : '' }}" href="{{ route('coordinator.hub') }}" style="font-size: 0.85rem;">
                        <i class="bi bi-briefcase me-2 text-white-50"></i> Coordinator Hub
                    </a>
                </li>
                @endif

                @if(Auth::user()->role === 'dean')
                <li class="nav-item">
                    <a class="nav-link text-white py-2 px-2 {{ request()->routeIs('dean.approvals') ? 'active-link' : '' }}" href="{{ route('dean.approvals') }}" style="font-size: 0.85rem;">
                        <i class="bi bi-bank me-2 text-white-50"></i> Dean Approvals
                    </a>
                </li>
                @endif
                @if(Auth::user()->hasPermission('ethics_review'))
                <li class="nav-item">
                    <a class="nav-link text-white py-2 px-2 {{ request()->routeIs('irerc.panel') ? 'active-link' : '' }}" href="{{ route('irerc.panel') }}" style="font-size: 0.85rem;">
                        <i class="bi bi-shield-exclamation me-2 text-white-50"></i> IRERC Panel
                    </a>
                </li>
                @endif
                @if(Auth::user()->hasPermission('view_rcsc_portal'))
                <li class="nav-item">
                    <a class="nav-link text-white py-2 px-2 {{ request()->routeIs('rcsc.*') ? 'active-link' : '' }}" href="{{ route('rcsc.portal') }}" style="font-size: 0.85rem;">
                        <i class="bi bi-shield-lock me-2 text-white-50"></i> RCSC Portal
                    </a>
                </li>
                @endif
                @if(Auth::user()->hasPermission('view_analytics') && Auth::user()->role !== 'admin')
                <li class="nav-item">
                    <a class="nav-link text-white py-2 px-2 {{ request()->routeIs('analytics') ? 'active-link' : '' }}" href="{{ route('analytics') }}" style="font-size: 0.85rem;">
                        <i class="bi bi-bar-chart-line me-2 text-white-50"></i> Analytics
                    </a>
                </li>
                @endif
                @if(Auth::user()->hasPermission('process_disbursement'))
                <li class="nav-item">
                    <a class="nav-link text-white py-2 px-2 {{ request()->routeIs('finance.*') ? 'active-link' : '' }}" href="{{ route('finance.disbursement') }}" style="font-size: 0.85rem;">
                        <i class="bi bi-cash-stack me-2 text-white-50"></i> Finance
                    </a>
                </li>
                @endif
                @if(Auth::user()->hasAnyPermission(['approve_extensions', 'approve_amendments']))
                <li class="nav-item">
                    <a class="nav-link text-white py-2 px-2 {{ request()->routeIs('extensions.*') ? 'active-link' : '' }}" href="{{ route('extensions.index') }}" style="font-size: 0.85rem;">
                        <i class="bi bi-clock-history me-2 text-white-50"></i> Extensions
                    </a>
                </li>
                @endif
                @if(Auth::user()->hasPermission('manage_users'))
                <li class="nav-item">
                    <a class="nav-link text-white py-2 px-2 {{ request()->routeIs('admin.users') ? 'active-link' : '' }}" href="{{ route('admin.users') }}" style="font-size: 0.85rem;">
                        <i class="bi bi-people me-2 text-white-50"></i> Users
                    </a>
                </li>
                @endif
                @if(Auth::user()->role === 'admin')
                <li class="nav-item">
                    <a class="nav-link text-white py-2 px-2 {{ request()->routeIs('admin.thematic-areas') ? 'active-link' : '' }}" href="{{ route('admin.thematic-areas') }}" style="font-size: 0.85rem;">
                        <i class="bi bi-diagram-3 me-2 text-white-50"></i> Thematics
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white py-2 px-2 {{ request()->routeIs('admin.colleges') ? 'active-link' : '' }}" href="{{ route('admin.colleges') }}" style="font-size: 0.85rem;">
                        <i class="bi bi-bank me-2 text-white-50"></i> Colleges
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white py-2 px-2 {{ request()->routeIs('admin.departments') ? 'active-link' : '' }}" href="{{ route('admin.departments') }}" style="font-size: 0.85rem;">
                        <i class="bi bi-building me-2 text-white-50"></i> Departments
                    </a>
                </li>
                @endif
            </ul>

            {{-- Demo Role Switcher (Compact 2-Column Grid) --}}
            <div class="mb-3">
                <a class="w-100 fw-semibold py-2 d-flex justify-content-between align-items-center text-decoration-none rounded px-2" data-bs-toggle="collapse" href="#demoRoleCollapse" role="button" aria-expanded="false" style="font-size: 0.8rem; background: rgba(255,255,255,0.07); color: rgba(255,255,255,0.85); border: 1px solid rgba(255,255,255,0.12);">
                    <span><i class="bi bi-arrow-repeat me-2 text-white-50"></i>Switch Demo Role</span>
                    <i class="bi bi-chevron-down text-white-50" style="font-size: 0.75rem;"></i>
                </a>
                <div class="collapse mt-2" id="demoRoleCollapse">
                    @php
                        $mobileRoles = [
                            'pi'          => ['name' => 'PI (Lead)',          'icon' => 'bi-person'],
                            'tm'          => ['name' => 'Team Member',        'icon' => 'bi-people'],
                            'dh'          => ['name' => 'Dept. Head',         'icon' => 'bi-person-workspace'],
                            'coordinator' => ['name' => 'Coordinator',        'icon' => 'bi-person-badge'],
                            'reviewer'    => ['name' => 'Peer Reviewer',      'icon' => 'bi-eye'],
                            'dean'        => ['name' => 'College Dean',       'icon' => 'bi-bank'],
                            'irerc'       => ['name' => 'IRERC Ethics',       'icon' => 'bi-shield-exclamation'],
                            'vparttcs'    => ['name' => 'Vice President',     'icon' => 'bi-person-badge'],
                            'rcsc'        => ['name' => 'RCSC Chair',         'icon' => 'bi-shield-lock'],
                            'finance'     => ['name' => 'Finance Office',     'icon' => 'bi-cash'],
                            'admin'       => ['name' => 'System Admin',       'icon' => 'bi-gear'],
                        ];
                    @endphp
                    <div class="row g-1">
                        @foreach($mobileRoles as $rKey => $rMeta)
                            @php $isActiveRole = (Auth::user()->role === $rKey); @endphp
                            <div class="col-6">
                                <a href="{{ route('switch-role', $rKey) }}"
                                   class="d-block w-100 text-truncate text-start py-1 px-2 rounded text-decoration-none {{ $isActiveRole ? 'fw-bold' : '' }}"
                                   style="font-size: 0.72rem; {{ $isActiveRole ? 'background: #1a7a4a; color: #fff; border: 1px solid #28a465;' : 'background: rgba(255,255,255,0.06); color: rgba(255,255,255,0.78); border: 1px solid rgba(255,255,255,0.1);' }}" title="{{ $rMeta['name'] }}">
                                    <i class="bi {{ $rMeta['icon'] }} me-1 {{ $isActiveRole ? 'text-white' : 'text-white-50' }}"></i>
                                    <span>{{ $rMeta['name'] }}</span>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <hr class="border-secondary my-2 opacity-25">

            {{-- Sign out --}}
            <div class="mt-auto pt-2 flex-shrink-0">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger w-100 fw-bold py-2 confirm-btn" style="font-size: 0.85rem;"
                            data-confirm-title="Sign Out"
                            data-confirm-message="Are you sure you want to sign out of your account?"
                            data-confirm-icon="bi-box-arrow-right"
                            data-confirm-color="text-danger"
                            data-confirm-btn-text="Yes, Sign Out"
                            data-confirm-btn-class="btn-danger">
                        <i class="bi bi-box-arrow-right me-2"></i> Sign Out
                    </button>
                </form>
            </div>
        </div>
    </div>
    @endauth

    <!-- Main Content Container -->
    <main class="container my-4 flex-grow-1">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($errors->any() && !request()->routeIs('login', 'password.*', 'register'))
            <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                <div class="fw-bold mb-1"><i class="bi bi-exclamation-triangle-fill me-2"></i>Please correct the following errors:</div>
                <ul class="mb-0 ps-3">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Institutional Footer -->
    <footer class="bg-white border-top py-3 text-center text-muted small mt-auto">
        <div class="container">
            &copy; {{ date('Y') }} Gambella University &mdash; Office of the Vice President for ARTTCS. SDD v3.0 Compliant.
        </div>
    </footer>

    <!-- Bootstrap 5.3 JS Bundle (local) -->
    <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script>
        // Lock scroll only for modals and offcanvas — dropdowns do NOT lock scroll
        (function() {
            var scrollPos = 0;

            function lockScroll() {
                scrollPos = window.pageYOffset || document.documentElement.scrollTop;
                document.documentElement.style.overflow = 'hidden';
                document.body.style.overflow = 'hidden';
                document.body.style.position = 'fixed';
                document.body.style.top = -scrollPos + 'px';
                document.body.style.left = '0';
                document.body.style.right = '0';
                document.body.style.width = '100%';
            }

            function unlockScroll() {
                document.documentElement.style.overflow = '';
                document.body.style.overflow = '';
                document.body.style.position = '';
                document.body.style.top = '';
                document.body.style.left = '';
                document.body.style.right = '';
                document.body.style.width = '';
                window.scrollTo(0, scrollPos);
            }

            // Offcanvas sidebar
            var sidebar = document.getElementById('navbarOffcanvas');
            if (sidebar) {
                sidebar.addEventListener('show.bs.offcanvas', lockScroll);
                sidebar.addEventListener('hidden.bs.offcanvas', unlockScroll);
            }

            // ALL modals
            document.querySelectorAll('.modal').forEach(function(modal) {
                modal.addEventListener('show.bs.modal', lockScroll);
                modal.addEventListener('hidden.bs.modal', unlockScroll);
            });
        })();
    </script>
    <script>
        document.querySelectorAll('form').forEach(function (form) {
            form.addEventListener('submit', function (e) {
                var btn = form.querySelector('button[type="submit"]');
                if (btn && !btn.disabled && !btn.classList.contains('confirm-btn')) {
                    btn.disabled = true;
                    var originalText = btn.innerHTML;
                    btn.dataset.originalHtml = originalText;
                    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status"></span>Processing...';
                    setTimeout(function () {
                        if (btn.disabled) {
                            btn.disabled = false;
                            btn.innerHTML = originalText;
                        }
                    }, 8000);
                }
            });
        });

        // Custom Confirmation Modal
        document.addEventListener('DOMContentLoaded', function() {
            var confirmModal = document.getElementById('confirmModal');
            var confirmBtn = document.getElementById('confirmActionBtn');
            var confirmForm = null;

            document.querySelectorAll('button.confirm-btn').forEach(function(btn) {
                btn.addEventListener('click', function(e) {
                    var form = btn.closest('form');
                    if (form && !form.checkValidity()) {
                        form.reportValidity();
                        return;
                    }
                    e.preventDefault();
                    if (!form) return;
                    confirmForm = form;

                    var title = btn.dataset.confirmTitle || 'Confirm Action';
                    var message = btn.dataset.confirmMessage || 'Are you sure you want to proceed?';
                    var icon = btn.dataset.confirmIcon || 'bi-question-circle';
                    var iconColor = btn.dataset.confirmColor || 'text-warning';
                    var btnText = btn.dataset.confirmBtnText || 'Yes, Confirm';
                    var btnClass = btn.dataset.confirmBtnClass || 'btn-warning';

                    document.getElementById('confirmModalLabel').innerHTML = '<i class="bi ' + icon + ' me-2"></i>' + title;
                    document.getElementById('confirmModalBody').innerHTML =
                        '<div class="mb-3"><i class="bi ' + icon + ' ' + iconColor + '" style="font-size: 3rem;"></i></div>' +
                        '<h5 class="fw-bold text-dark">' + title + '</h5>' +
                        '<p class="text-muted mb-0">' + message + '</p>';
                    confirmBtn.className = 'btn px-4 fw-bold ' + btnClass;
                    confirmBtn.innerHTML = '<i class="bi bi-check-lg me-1"></i>' + btnText;

                    var modal = new bootstrap.Modal(confirmModal);
                    modal.show();
                });
            });

            confirmBtn.addEventListener('click', function() {
                if (confirmForm) {
                    confirmBtn.disabled = true;
                    confirmBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Processing...';
                    confirmForm.submit();
                }
            });

            confirmModal.addEventListener('hidden.bs.modal', function() {
                confirmForm = null;
                confirmBtn.disabled = false;
            });

            // Position profile dropdown below the trigger button
            var profileDropdown = document.getElementById('userProfileMenuBtn');
            if (profileDropdown) {
                profileDropdown.addEventListener('show.bs.dropdown', function () {
                    var rect = profileDropdown.getBoundingClientRect();
                    var menu = profileDropdown.nextElementSibling;
                    if (menu) {
                        menu.style.top = rect.bottom + 4 + 'px';
                    }
                });
            }

            // Hamburger → X toggle on mobile offcanvas open/close
            var offcanvasEl = document.getElementById('navbarOffcanvas');
            var mobileNavIcon = document.getElementById('mobileNavIcon');
            if (offcanvasEl && mobileNavIcon) {
                offcanvasEl.addEventListener('show.bs.offcanvas', function () {
                    mobileNavIcon.classList.remove('bi-list');
                    mobileNavIcon.classList.add('bi-x-lg');
                });
                offcanvasEl.addEventListener('hide.bs.offcanvas', function () {
                    mobileNavIcon.classList.remove('bi-x-lg');
                    mobileNavIcon.classList.add('bi-list');
                });
            }
        });
    </script>

    {{-- Reusable Confirmation Modal --}}
    <div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-dark text-white border-0 py-2">
                    <h6 class="modal-title fw-bold" id="confirmModalLabel">
                        <i class="bi bi-question-circle me-2"></i>Confirm
                    </h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center py-3" id="confirmModalBody">
                    <div class="mb-2">
                        <i class="bi bi-question-circle text-warning" style="font-size: 2rem;"></i>
                    </div>
                    <h6 class="fw-bold text-dark mb-1">Are you sure?</h6>
                    <p class="text-muted small mb-0">This action cannot be undone.</p>
                </div>
                <div class="modal-footer border-0 justify-content-center gap-2 pb-3 pt-0">
                    <button type="button" class="btn btn-outline-secondary btn-sm px-3" data-bs-dismiss="modal">
                        Cancel
                    </button>
                    <button type="button" class="btn btn-warning btn-sm px-3 fw-bold" id="confirmActionBtn">
                        Yes, Confirm
                    </button>
                </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Mini Calendar Modal --}}
    <div class="modal fade" id="calendarModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm" style="max-width:320px;">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-dark text-white border-0 py-2">
                    <h6 class="modal-title mb-0 fw-bold"><i class="bi bi-calendar2-check me-2"></i>Research Calendar</h6>
                    <button type="button" class="btn-close btn-close-white btn-sm" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <button class="btn btn-sm btn-light py-0 px-2 text-muted" id="calPrevBtn"><i class="bi bi-chevron-left"></i></button>
                        <span class="fw-bold text-dark small" id="calendarMonthLabel" style="font-size:0.85rem;"></span>
                        <button class="btn btn-sm btn-light py-0 px-2 text-muted" id="calNextBtn"><i class="bi bi-chevron-right"></i></button>
                    </div>
                    <div class="text-center mb-2" style="height:12px;">
                        <span id="calTodayBtn" class="text-dark fw-bold" style="font-size:0.65rem; cursor:pointer; display:none; text-transform:uppercase; letter-spacing:0.5px;"><i class="bi bi-arrow-return-left me-1"></i>Back to Today</span>
                    </div>
                    <!-- Days of week -->
                    <div class="d-flex justify-content-between text-muted fw-bold mb-2 text-center" style="font-size:0.7rem;">
                        <div style="width:14%;">Su</div><div style="width:14%;">Mo</div><div style="width:14%;">Tu</div><div style="width:14%;">We</div><div style="width:14%;">Th</div><div style="width:14%;">Fr</div><div style="width:14%;">Sa</div>
                    </div>
                    <!-- Grid -->
                    <div id="miniCalendarGrid" class="d-flex flex-wrap text-center" style="font-size:0.8rem;"></div>
                    
                    <hr class="my-3 border-secondary opacity-25">
                    
                    <div class="text-center py-1">
                        <div class="fw-bold text-dark mb-1" style="font-size:0.85rem;">Role-based research calendar</div>
                        <div class="text-dark fw-bold" style="font-size:1rem; letter-spacing:0.5px;">COMING SOON</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const calGrid = document.getElementById('miniCalendarGrid');
        const monthLabel = document.getElementById('calendarMonthLabel');
        const prevBtn = document.getElementById('calPrevBtn');
        const nextBtn = document.getElementById('calNextBtn');
        const todayBtn = document.getElementById('calTodayBtn');
        if(!calGrid || !monthLabel) return;

        // Calendar scroll is handled by global overlay scroll lock above
        
        let currentDate = new Date(); // Date used for navigation
        const actualToday = new Date(); // Always strictly today
        const monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
        
        function renderCalendar() {
            const year = currentDate.getFullYear();
            const month = currentDate.getMonth();
            monthLabel.textContent = monthNames[month] + " " + year;
            
            // Show today button if we are navigated away from current month
            if (todayBtn) {
                if (month !== actualToday.getMonth() || year !== actualToday.getFullYear()) {
                    todayBtn.style.display = 'inline-block';
                } else {
                    todayBtn.style.display = 'none';
                }
            }
            
            const firstDay = new Date(year, month, 1).getDay();
            const daysInMonth = new Date(year, month + 1, 0).getDate();
            
            let html = '';
            for(let i=0; i<firstDay; i++) {
                html += '<div style="width:14%; padding: 6px 0;"></div>';
            }
            
            for(let i=1; i<=daysInMonth; i++) {
                let extraStyle = 'cursor:pointer; border-radius:6px; padding: 6px 0; margin-bottom: 2px; transition: background 0.2s;';
                let cls = 'fw-semibold text-dark hover-cal-bg';
                
                // Highlight if it's the actual current day in real life
                if(i === actualToday.getDate() && month === actualToday.getMonth() && year === actualToday.getFullYear()) {
                    extraStyle += 'background: #212529; color:#fff !important; box-shadow: 0 2px 4px rgba(0,0,0,0.3);';
                    cls = 'fw-bold text-white';
                }
                
                html += `<div style="width:14%; ${extraStyle}" class="${cls}" title="${monthNames[month]} ${i}, ${year}">${i}</div>`;
            }
            calGrid.innerHTML = html;
        }

        prevBtn.addEventListener('click', function() {
            currentDate.setMonth(currentDate.getMonth() - 1);
            renderCalendar();
        });

        nextBtn.addEventListener('click', function() {
            currentDate.setMonth(currentDate.getMonth() + 1);
            renderCalendar();
        });
        
        if (todayBtn) {
            todayBtn.addEventListener('click', function() {
                currentDate = new Date(); // Reset to today
                renderCalendar();
            });
        }

        // Initial render
        renderCalendar();
    });
    </script>
    <style>
    .hover-cal-bg:hover { background-color: #f1f5f9 !important; color: #000 !important; }
    
    /* Bulletproof modal scroll lock */
    body:has(.modal.show),
    html:has(.modal.show),
    body:has(.offcanvas.show),
    html:has(.offcanvas.show) {
        overflow: hidden !important;
    }

    /* Profile dropdown positioning */
    .profile-dropdown-menu.show {
        position: fixed !important;
        right: 1rem !important;
        left: auto !important;
        top: auto !important;
    }

    /* Navbar sticky */
    .navbar-gmu {
        position: sticky !important;
        top: 0 !important;
        z-index: 1050 !important;
    }
    @media (max-width: 991.98px) {
        .navbar-gmu {
            position: fixed !important;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1060 !important;
            width: 100%;
        }
        body { padding-top: 70px !important; }
    }
    </style>

    <script>
    // Custom Select Dropdowns - lazy init, only processes new selects
    (function() {
        function initCustomSelects(root) {
            var container = root || document;
            container.querySelectorAll('select.form-select:not(.custom-select-done)').forEach(function(sel) {
                sel.classList.add('custom-select-done');
                var isSmall = sel.classList.contains('form-select-sm');
                var wrapper = document.createElement('div');
                wrapper.className = 'custom-select-wrapper' + (isSmall ? ' custom-select-sm' : '');
                sel.parentNode.insertBefore(wrapper, sel);
                wrapper.appendChild(sel);

                var trigger = document.createElement('div');
                trigger.className = 'custom-select-trigger';
                var optionsDiv = document.createElement('div');
                optionsDiv.className = 'custom-select-options';

                var placeholderText = '-- Select --';
                var firstOption = sel.querySelector('option[value=""]');
                if (firstOption) placeholderText = firstOption.textContent;

                var selectedText = document.createElement('span');
                selectedText.className = sel.value ? '' : 'placeholder';
                selectedText.textContent = sel.value ? sel.options[sel.selectedIndex].text : placeholderText;
                var arrow = document.createElement('span');
                arrow.className = 'arrow';
                arrow.innerHTML = '&#9662;';
                trigger.appendChild(selectedText);
                trigger.appendChild(arrow);
                wrapper.appendChild(trigger);
                wrapper.appendChild(optionsDiv);

                function renderOptions() {
                    optionsDiv.innerHTML = '';
                    Array.from(sel.options).forEach(function(opt, i) {
                        var div = document.createElement('div');
                        div.className = 'custom-select-option' + (opt.value === sel.value ? ' selected' : '');
                        div.textContent = opt.text;
                        var check = document.createElement('span');
                        check.className = 'check-icon';
                        check.innerHTML = '&#10003;';
                        div.appendChild(check);
                        div.addEventListener('click', function(e) {
                            e.stopPropagation();
                            sel.value = opt.value;
                            selectedText.textContent = opt.text;
                            selectedText.className = opt.value ? '' : 'placeholder';
                            optionsDiv.querySelectorAll('.custom-select-option').forEach(function(o) { o.classList.remove('selected'); });
                            div.classList.add('selected');
                            optionsDiv.classList.remove('show');
                            trigger.classList.remove('open');
                            sel.dispatchEvent(new Event('change'));
                        });
                        optionsDiv.appendChild(div);
                    });
                }
                renderOptions();

                trigger.addEventListener('click', function(e) {
                    e.stopPropagation();
                    document.querySelectorAll('.custom-select-options.show').forEach(function(o) { o.classList.remove('show'); });
                    document.querySelectorAll('.custom-select-trigger.open').forEach(function(t) { t.classList.remove('open'); });
                    var rect = trigger.getBoundingClientRect();
                    optionsDiv.style.width = rect.width + 'px';
                    optionsDiv.style.left = rect.left + 'px';
                    var dropHeight = Math.min(200, optionsDiv.scrollHeight || 200);
                    if (rect.bottom + dropHeight > window.innerHeight) {
                        optionsDiv.style.top = (rect.top - dropHeight - 4) + 'px';
                    } else {
                        optionsDiv.style.top = (rect.bottom + 4) + 'px';
                    }
                    optionsDiv.classList.toggle('show');
                    trigger.classList.toggle('open');
                });
            });
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', function() { initCustomSelects(document); });
        } else {
            initCustomSelects(document);
        }

        // Only init selects inside a modal WHEN that specific modal opens (not all modals)
        document.querySelectorAll('.modal').forEach(function(modal) {
            modal.addEventListener('shown.bs.modal', function() {
                initCustomSelects(modal);
            });
        });

        document.addEventListener('click', function() {
            document.querySelectorAll('.custom-select-options.show').forEach(function(o) { o.classList.remove('show'); });
            document.querySelectorAll('.custom-select-trigger.open').forEach(function(t) { t.classList.remove('open'); });
        });
    })();
    </script>

    <script>
    // Page Transition Loader
    (function() {
        var loader = document.getElementById('page-loader');
        if (!loader) return;

        // Show loader on all internal link clicks
        document.addEventListener('click', function(e) {
            var link = e.target.closest('a[href]');
            if (!link) return;
            var href = link.getAttribute('href');
            if (!href || href === '#' || href.startsWith('javascript:') || href.startsWith('mailto:') || href.startsWith('tel:')) return;
            if (link.target === '_blank') return;
            if (link.hasAttribute('data-bs-toggle') || link.hasAttribute('data-bs-dismiss')) return;
            if (link.closest('.dropdown-menu') || link.closest('.modal')) return;
            // Only for internal links
            if (href.startsWith('/') || href.startsWith(window.location.origin)) {
                loader.classList.add('active');
            }
        });

        // Show loader on form submits (confirm buttons)
        document.addEventListener('submit', function(e) {
            var btn = e.target.querySelector('button[type="submit"], .confirm-btn');
            if (btn && !btn.classList.contains('btn-outline-secondary')) {
                loader.classList.add('active');
            }
        });

        // Hide loader when page fully loads
        window.addEventListener('load', function() {
            loader.classList.remove('active');
        });
    })();
    </script>

    @stack('scripts')
</body>
</html>
