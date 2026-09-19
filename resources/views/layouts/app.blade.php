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
        body {
            background-color: var(--gmu-bg);
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            color: #1e293b;
            letter-spacing: -0.01em;
            overflow-x: hidden;
        }
        html {
            overflow-x: hidden;
        }
        /* Navbar Styling */
        .navbar-gmu {
            background: linear-gradient(135deg, var(--gmu-primary) 0%, var(--gmu-primary-dark) 100%);
            box-shadow: 0 4px 20px -2px rgba(15, 62, 46, 0.35);
            border-bottom: 2px solid var(--gmu-gold);
        }
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
            .navbar-brand i {
                font-size: 1.4rem !important;
            }
            .card-custom {
                border-radius: 8px;
                padding: 0.75rem !important;
            }
            .table-responsive {
                border-radius: 8px;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
                white-space: nowrap;
            }
            .table-responsive table {
                min-width: 600px;
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
                z-index: 1050 !important;
                width: 100%;
            }
            /* Add padding below navbar for fixed position */
            body {
                padding-top: 70px !important;
            }
            /* Smaller headings on mobile */
            h3.fw-bold {
                font-size: 1.1rem !important;
            }
            h5.fw-bold, h6.fw-bold {
                font-size: 0.95rem !important;
            }
            /* Smaller badges on mobile */
            .badge {
                font-size: 0.65rem !important;
                padding: 0.25em 0.5em !important;
            }
            /* Hide subtitle text on mobile */
            .navbar-gmu .d-flex .text-muted.small {
                display: none !important;
            }
            /* Compact KPI cards */
            .fs-1 {
                font-size: 1.5rem !important;
            }
            /* Stack buttons vertically on mobile */
            .d-flex.gap-2 {
                flex-wrap: wrap;
            }
            .d-flex.gap-2 .btn {
                flex: 0 0 auto;
                min-width: 0;
                font-size: 0.75rem !important;
                padding: 0.3rem 0.5rem !important;
            }
            /* Compact dashboard header */
            .d-flex.justify-content-between.align-items-center {
                flex-direction: column !important;
                align-items: flex-start !important;
                gap: 0.5rem !important;
            }
            .d-flex.justify-content-between.align-items-center .btn {
                width: 100% !important;
            }
            /* Compact KPI cards */
            .card-custom.p-4 {
                padding: 0.75rem !important;
            }
            /* Compact quick actions */
            .d-flex.flex-wrap.gap-2 .btn {
                font-size: 0.78rem !important;
                padding: 0.4rem 0.6rem !important;
            }
            /* Smaller table text */
            .table td, .table th {
                padding: 0.5rem !important;
                font-size: 0.8rem !important;
            }
            /* KPI cards 2x2 on mobile */
            .row.g-3 > div[class*="col-"] {
                flex: 0 0 50% !important;
                max-width: 50% !important;
            }
            /* Projects filter: stack search & filter on mobile */
            #filterForm .row.g-3 > div {
                flex: 0 0 100% !important;
                max-width: 100% !important;
            }
            /* Compact KPI card content */
            .fs-2 {
                font-size: 1rem !important;
            }
            .stat-bubble {
                width: 28px !important;
                height: 28px !important;
                border-radius: 6px !important;
            }
            .stat-bubble i {
                font-size: 0.85rem !important;
            }
            .card-custom.p-3 {
                padding: 0.5rem !important;
            }
            .text-muted.small.text-uppercase {
                font-size: 0.6rem !important;
            }
            .small.text-muted {
                font-size: 0.65rem !important;
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
        .timeline-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            flex: 1;
            position: relative;
            z-index: 1;
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

    <!-- Navigation Header -->
    <nav class="navbar navbar-expand-lg navbar-gmu py-2 @yield('navbar-class')">
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
                <button class="btn text-white p-1 border-0" type="button" data-bs-toggle="offcanvas" data-bs-target="#navbarOffcanvas" aria-controls="navbarOffcanvas" aria-label="Toggle navigation">
                    <i class="bi bi-list fs-1 text-warning"></i>
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

                    {{-- 1a. Departments & Colleges (Admin only) --}}
                    @if(Auth::user()->role === 'admin')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.departments') ? 'active-link' : '' }}" href="{{ route('admin.departments') }}">
                            <i class="bi bi-building me-1"></i> Departments
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.colleges') ? 'active-link' : '' }}" href="{{ route('admin.colleges') }}">
                            <i class="bi bi-bank me-1"></i> Colleges
                        </a>
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

                    {{-- 2c. Progress Reports (TM) --}}
                    @if(Auth::user()->role === 'tm')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('progress.*') ? 'active-link' : '' }}" href="{{ route('progress.index') }}">
                            <i class="bi bi-graph-up me-1"></i> Progress Reports
                        </a>
                    </li>
                    @endif

                    {{-- 3. My Projects (PI: consolidated dropdown) --}}
                    @if(Auth::user()->hasAnyPermission(['request_pi_transfer', 'request_termination']))
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle {{ request()->routeIs('pitransfer.*') || request()->routeIs('termination.*') ? 'active-link' : '' }}" href="#" data-bs-toggle="dropdown">
                            <i class="bi bi-kanban me-1"></i> My Projects
                        </a>
                        <ul class="dropdown-menu">
                            @if(Auth::user()->hasPermission('request_pi_transfer'))
                            <li><a class="dropdown-item" href="{{ route('pitransfer.index') }}"><i class="bi bi-arrow-left-right me-2 text-secondary"></i>PI Transfer</a></li>
                            @endif
                            @if(Auth::user()->hasPermission('request_termination'))
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="{{ route('termination.index') }}"><i class="bi bi-exclamation-triangle me-2"></i>Terminate Project</a></li>
                            @endif
                        </ul>
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

                    {{-- 8. Extensions & Amendments --}}
                    @if(Auth::user()->hasAnyPermission(['approve_extensions', 'approve_amendments']))
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('extensions.*') ? 'active-link' : '' }}" href="{{ route('extensions.index') }}">
                            <i class="bi bi-clock-history me-1"></i> Extensions & Amendments
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
                    @php
                        $notifCount = 0;
                        $notifItems = [];
                        $userRole = Auth::user()->role;

                        if ($userRole === 'reviewer') {
                            $pendingEvals = \App\Models\Evaluation::where('examiner_id', Auth::id())->where('decision', 'Pending')->get();
                            $notifCount = $pendingEvals->count();
                            foreach($pendingEvals as $e) {
                                $notifItems[] = [
                                    'title' => 'Pending Review Assigned',
                                    'desc'  => 'Project #' . $e->project_id . ' requires evaluation',
                                    'time'  => $e->created_at ? $e->created_at->diffForHumans() : 'Recently',
                                    'icon'  => 'bi-eye-fill text-primary'
                                ];
                            }
                        } elseif (in_array($userRole, ['coordinator', 'dh'])) {
                            $pendingProposals = \App\Models\Project::where('status', 'Submitted')->get();
                            $notifCount = $pendingProposals->count();
                            foreach($pendingProposals as $p) {
                                $notifItems[] = [
                                    'title' => 'New Proposal Submitted',
                                    'desc'  => '"' . substr($p->title, 0, 30) . '..." awaiting screening',
                                    'time'  => $p->created_at ? $p->created_at->diffForHumans() : 'Recently',
                                    'icon'  => 'bi-file-earmark-text text-warning'
                                ];
                            }
                        } elseif (in_array($userRole, ['dean', 'rcsc', 'vparttcs'])) {
                            $pendingBudgets = \App\Models\BudgetRequest::where('status', 'Pending')->get();
                            $notifCount = $pendingBudgets->count();
                            foreach($pendingBudgets as $b) {
                                $notifItems[] = [
                                    'title' => 'Budget Approval Needed',
                                    'desc'  => 'Project #' . $b->project_id . ' - ETB ' . number_format($b->requested_amount, 2),
                                    'time'  => $b->created_at ? $b->created_at->diffForHumans() : 'Recently',
                                    'icon'  => 'bi-currency-dollar text-success'
                                ];
                            }
                        } elseif ($userRole === 'pi') {
                            $myProjects = \App\Models\Project::where('pi_id', Auth::id())->whereNotIn('status', ['Completed', 'Terminated'])->get();
                            $notifCount = $myProjects->count();
                            foreach($myProjects as $p) {
                                $notifItems[] = [
                                    'title' => 'Project Status Update',
                                    'desc'  => '"' . substr($p->title, 0, 30) . '..." is currently ' . $p->status,
                                    'time'  => $p->updated_at ? $p->updated_at->diffForHumans() : 'Recently',
                                    'icon'  => 'bi-info-circle text-info'
                                ];
                            }
                        } elseif ($userRole === 'irerc') {
                            $pendingIRERC = \App\Models\IRERCClearance::where('status', 'Pending')->get();
                            $notifCount = $pendingIRERC->count();
                            foreach($pendingIRERC as $ir) {
                                $notifItems[] = [
                                    'title' => 'Ethics Review Pending',
                                    'desc'  => 'Project #' . $ir->project_id . ' requires IRERC clearance',
                                    'time'  => $ir->created_at ? $ir->created_at->diffForHumans() : 'Recently',
                                    'icon'  => 'bi-shield-exclamation text-warning'
                                ];
                            }
                        } elseif ($userRole === 'admin') {
                            $activeUsers = \App\Models\User::where('status', 'active')->count();
                            $notifCount = 1;
                            $notifItems[] = [
                                'title' => 'System Overview',
                                'desc'  => $activeUsers . ' active users in the system',
                                'time'  => 'Now',
                                'icon'  => 'bi-gear text-primary'
                            ];
                        } elseif ($userRole === 'tm') {
                            $myMemberProjects = \App\Models\ProjectMember::where('user_id', Auth::id())->get();
                            $notifCount = $myMemberProjects->count();
                            foreach($myMemberProjects as $mp) {
                                $p = \App\Models\Project::find($mp->project_id);
                                if ($p) {
                                    $notifItems[] = [
                                        'title' => 'Team Project Update',
                                        'desc'  => '"' . substr($p->title, 0, 30) . '..." - ' . $p->status,
                                        'time'  => $p->updated_at ? $p->updated_at->diffForHumans() : 'Recently',
                                        'icon'  => 'bi-people text-info'
                                    ];
                                }
                            }
                        }
                    @endphp

                    <div class="dropdown me-3">
                        <button class="btn btn-link p-0 border-0 position-relative d-flex align-items-center text-decoration-none" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Notifications (SCR-17)">
                            <i class="bi bi-bell-fill fs-5 text-warning"></i>
                            @if($notifCount > 0)
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size:0.65rem; margin-top: -2px;">
                                    {{ $notifCount > 99 ? '99+' : $notifCount }}
                                </span>
                            @endif
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end p-2 shadow-lg" style="width: 320px; max-height: 380px; overflow-y: auto;">
                            <li class="dropdown-header fw-bold d-flex justify-content-between align-items-center border-bottom pb-2 mb-2">
                                <span><i class="bi bi-bell me-1 text-warning"></i> Notifications Center (SCR-17)</span>
                                <span class="badge bg-primary rounded-pill">{{ $notifCount }}</span>
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
                    <div class="d-none d-xl-flex align-items-center text-warning small border border-warning rounded-pill px-3 py-1 me-3" style="font-size:0.75rem;">
                        <i class="bi bi-calendar2-check me-1"></i> AY 2026/2027 (Call #1 Active)
                    </div>

                    {{-- User Profile & Demo Role Switcher Dropdown --}}
                    <div class="dropdown border-start border-secondary ps-3">
                        <a href="#" class="text-white text-decoration-none d-flex align-items-center gap-2 dropdown-toggle-custom" id="userProfileMenuBtn" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="text-end">
                                <div class="fw-bold small lh-1 text-white" style="white-space: nowrap;">{{ preg_replace('/\s*\([^)]*\)/', '', Auth::user()->name) }}</div>
                                <span class="badge badge-role mt-1" style="font-size: 0.65rem; padding: 2px 6px; cursor: pointer;">{{ strtoupper(Auth::user()->role) }} ▾</span>
                            </div>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end p-2 shadow-lg profile-dropdown-menu" aria-labelledby="userProfileMenuBtn" style="min-width: 250px; z-index: 1050;">
                            <li class="dropdown-header fw-bold border-bottom pb-2 mb-1">
                                <i class="bi bi-person-badge me-1 text-success"></i> Account Profile
                            </li>
                            <li><span class="dropdown-item-text small text-muted"><strong>Staff ID:</strong> {{ Auth::user()->staff_id ?? 'GMU-STAFF' }}</span></li>
                            <li><span class="dropdown-item-text small text-muted"><strong>Email:</strong> {{ Auth::user()->email }}</span></li>
                            <li><a class="dropdown-item small" href="{{ route('profile') }}"><i class="bi bi-person-circle me-2"></i>View &amp; Edit Profile</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li class="dropdown-header fw-bold text-primary mb-1">
                                <i class="bi bi-arrow-repeat me-1"></i> Switch Demo Role View
                            </li>
                            <li><a class="dropdown-item small" href="{{ route('switch-role', 'pi') }}"><i class="bi bi-person me-2"></i>PI (Principal Investigator)</a></li>
                            <li><a class="dropdown-item small" href="{{ route('switch-role', 'tm') }}"><i class="bi bi-people me-2"></i>Team Member (Co-Researcher)</a></li>
                            <li><a class="dropdown-item small" href="{{ route('switch-role', 'dh') }}"><i class="bi bi-person-workspace me-2"></i>Department Head (DH)</a></li>
                            <li><a class="dropdown-item small" href="{{ route('switch-role', 'coordinator') }}"><i class="bi bi-person-badge me-2"></i>Research Coordinator</a></li>
                            <li><a class="dropdown-item small" href="{{ route('switch-role', 'reviewer') }}"><i class="bi bi-eye me-2"></i>Peer Reviewer</a></li>
                            <li><a class="dropdown-item small" href="{{ route('switch-role', 'dean') }}"><i class="bi bi-bank me-2"></i>College Dean</a></li>
                            <li><a class="dropdown-item small" href="{{ route('switch-role', 'irerc') }}"><i class="bi bi-shield-exclamation me-2"></i>IRERC Ethics Committee</a></li>
                            <li><a class="dropdown-item small" href="{{ route('switch-role', 'vparttcs') }}"><i class="bi bi-person-badge me-2"></i>Vice President (ARTTCS)</a></li>
                            <li><a class="dropdown-item small" href="{{ route('switch-role', 'rcsc') }}"><i class="bi bi-shield-lock me-2"></i>RCSC Chair</a></li>
                            <li><a class="dropdown-item small" href="{{ route('switch-role', 'finance') }}"><i class="bi bi-cash me-2"></i>Finance Office</a></li>
                            <li><a class="dropdown-item small" href="{{ route('switch-role', 'admin') }}"><i class="bi bi-gear me-2"></i>System Administrator</a></li>
                            <li><hr class="dropdown-divider"></li>
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
    <div class="offcanvas offcanvas-end text-white d-lg-none" tabindex="-1" id="navbarOffcanvas" aria-labelledby="navbarOffcanvasLabel" style="background-color: var(--gmu-primary-dark); width: 310px;">
        <div class="offcanvas-header border-bottom border-secondary pb-3">
            <div class="d-flex align-items-center">
                <i class="bi bi-journal-bookmark-fill fs-3 text-warning me-2"></i>
                <div>
                    <div class="fw-bold text-white small">GAMBELLA UNIVERSITY</div>
                    <div class="text-warning" style="font-size: 0.7rem;">RPMS Mobile Menu</div>
                </div>
            </div>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body p-3">
            {{-- User info card --}}
            <div class="p-2 bg-dark bg-opacity-50 rounded-3 mb-3 border border-secondary">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="fw-bold text-white small">{{ preg_replace('/\s*\([^)]*\)/', '', Auth::user()->name) }}</div>
                        <span class="badge badge-role mt-1" style="font-size: 0.6rem;">{{ strtoupper(Auth::user()->role) }}</span>
                    </div>
                    <a href="{{ route('profile') }}" class="text-warning text-decoration-none" style="font-size: 0.7rem;">
                        <i class="bi bi-pencil-square"></i>
                    </a>
                </div>
            </div>

            {{-- Main Navigation Links --}}
            <ul class="nav nav-pills flex-column gap-0 mb-3">
                <li class="nav-item">
                    <a class="nav-link text-white py-2 px-2 {{ request()->routeIs('dashboard') ? 'active-link' : '' }}" href="{{ route('dashboard') }}" style="font-size: 0.85rem;">
                        <i class="bi bi-speedometer2 me-2"></i> Dashboard
                    </a>
                </li>
                @if(Auth::user()->hasPermission('view_projects'))
                <li class="nav-item">
                    <a class="nav-link text-white py-2 px-2 {{ request()->routeIs('projects.*') ? 'active-link' : '' }}" href="{{ route('projects.index') }}" style="font-size: 0.85rem;">
                        <i class="bi bi-folder2-open me-2"></i> Projects
                    </a>
                </li>
                @endif
                @if(Auth::user()->role === 'reviewer')
                <li class="nav-item">
                    <a class="nav-link text-white py-2 px-2 {{ request()->routeIs('evaluations.*') ? 'active-link' : '' }}" href="{{ route('evaluations.index') }}" style="font-size: 0.85rem;">
                        <i class="bi bi-clipboard-check me-2"></i> My Evaluations
                    </a>
                </li>
                @endif
                @if(Auth::user()->hasAnyPermission(['submit_progress_report', 'request_extension', 'request_amendment', 'submit_procurement', 'request_pi_transfer']))
                <li class="nav-item">
                    <a class="nav-link text-white py-2 px-2 {{ request()->routeIs('progress.*') || request()->routeIs('extensions.*') || request()->routeIs('procurement.*') || request()->routeIs('pitransfer.*') ? 'active-link' : '' }}" href="{{ route('progress.index') }}" style="font-size: 0.85rem;">
                        <i class="bi bi-kanban me-2"></i> My Projects
                    </a>
                </li>
                @endif
                @if(Auth::user()->hasPermission('screen_proposals'))
                <li class="nav-item">
                    <a class="nav-link text-white py-2 px-2 {{ request()->routeIs('dh.screening') ? 'active-link' : '' }}" href="{{ route('dh.screening') }}" style="font-size: 0.85rem;">
                        <i class="bi bi-ui-checks me-2"></i> DH Screening
                    </a>
                </li>
                @endif
                @if(Auth::user()->hasPermission('assign_reviewer'))
                <li class="nav-item">
                    <a class="nav-link text-white py-2 px-2 {{ request()->routeIs('coordinator.*') ? 'active-link' : '' }}" href="{{ route('coordinator.hub') }}" style="font-size: 0.85rem;">
                        <i class="bi bi-briefcase me-2"></i> Coordinator Hub
                    </a>
                </li>
                @endif
                @if(Auth::user()->hasPermission('manage_certificates'))
                <li class="nav-item">
                    <a class="nav-link text-white py-2 px-2 {{ request()->routeIs('certificates') ? 'active-link' : '' }}" href="{{ route('certificates') }}" style="font-size: 0.85rem;">
                        <i class="bi bi-award me-2"></i> Certificates
                    </a>
                </li>
                @endif
                @if(Auth::user()->role === 'dean')
                <li class="nav-item">
                    <a class="nav-link text-white py-2 px-2 {{ request()->routeIs('dean.approvals') ? 'active-link' : '' }}" href="{{ route('dean.approvals') }}" style="font-size: 0.85rem;">
                        <i class="bi bi-bank me-2"></i> Dean Approvals
                    </a>
                </li>
                @endif
                @if(Auth::user()->hasPermission('ethics_review'))
                <li class="nav-item">
                    <a class="nav-link text-white py-2 px-2 {{ request()->routeIs('irerc.panel') ? 'active-link' : '' }}" href="{{ route('irerc.panel') }}" style="font-size: 0.85rem;">
                        <i class="bi bi-shield-exclamation me-2"></i> IRERC Panel
                    </a>
                </li>
                @endif
                @if(Auth::user()->hasPermission('view_rcsc_portal'))
                <li class="nav-item">
                    <a class="nav-link text-white py-2 px-2 {{ request()->routeIs('rcsc.*') ? 'active-link' : '' }}" href="{{ route('rcsc.portal') }}" style="font-size: 0.85rem;">
                        <i class="bi bi-shield-lock me-2"></i> RCSC Portal
                    </a>
                </li>
                @endif
                @if(Auth::user()->hasPermission('view_analytics'))
                <li class="nav-item">
                    <a class="nav-link text-white py-2 px-2 {{ request()->routeIs('analytics') ? 'active-link' : '' }}" href="{{ route('analytics') }}" style="font-size: 0.85rem;">
                        <i class="bi bi-bar-chart-line me-2"></i> Analytics
                    </a>
                </li>
                @endif
                @if(Auth::user()->hasPermission('process_disbursement'))
                <li class="nav-item">
                    <a class="nav-link text-white py-2 px-2 {{ request()->routeIs('finance.*') ? 'active-link' : '' }}" href="{{ route('finance.disbursement') }}" style="font-size: 0.85rem;">
                        <i class="bi bi-cash-stack me-2"></i> Finance
                    </a>
                </li>
                @endif
                @if(Auth::user()->hasAnyPermission(['approve_extensions', 'approve_amendments']))
                <li class="nav-item">
                    <a class="nav-link text-white py-2 px-2 {{ request()->routeIs('extensions.*') ? 'active-link' : '' }}" href="{{ route('extensions.index') }}" style="font-size: 0.85rem;">
                        <i class="bi bi-clock-history me-2"></i> Extensions
                    </a>
                </li>
                @endif
                @if(Auth::user()->hasPermission('manage_users'))
                <li class="nav-item">
                    <a class="nav-link text-white py-2 px-2 {{ request()->routeIs('admin.users') ? 'active-link' : '' }}" href="{{ route('admin.users') }}" style="font-size: 0.85rem;">
                        <i class="bi bi-people me-2"></i> Users
                    </a>
                </li>
                @endif
                @if(Auth::user()->hasPermission('view_audit_logs'))
                <li class="nav-item">
                    <a class="nav-link text-white py-2 px-2 {{ request()->routeIs('admin.audit-logs') ? 'active-link' : '' }}" href="{{ route('admin.audit-logs') }}" style="font-size: 0.85rem;">
                        <i class="bi bi-shield-check me-2"></i> Audit Logs
                    </a>
                </li>
                @endif
                @if(Auth::user()->hasPermission('manage_thematic_areas'))
                <li class="nav-item">
                    <a class="nav-link text-white py-2 px-2 {{ request()->routeIs('admin.thematic-areas') ? 'active-link' : '' }}" href="{{ route('admin.thematic-areas') }}" style="font-size: 0.85rem;">
                        <i class="bi bi-tags me-2"></i> Thematic Areas
                    </a>
                </li>
                @endif
                @if(Auth::user()->hasPermission('hrms_sync'))
                <li class="nav-item">
                    <a class="nav-link text-white py-2 px-2 {{ request()->routeIs('admin.hrms-sync') ? 'active-link' : '' }}" href="{{ route('admin.hrms-sync') }}" style="font-size: 0.85rem;">
                        <i class="bi bi-cloud-arrow-up me-2"></i> HRMS Sync
                    </a>
                </li>
                @endif
            </ul>

            {{-- Demo Role Switcher --}}
            <div class="text-uppercase text-muted fw-bold mb-1 ps-1" style="font-size: 0.6rem; letter-spacing: 0.5px;">Switch Role (Demo)</div>
            <div class="d-flex flex-wrap gap-1 mb-3">
                <a href="{{ route('switch-role', 'pi') }}" class="btn btn-sm btn-outline-light py-0" style="font-size: 0.65rem;">PI</a>
                <a href="{{ route('switch-role', 'tm') }}" class="btn btn-sm btn-outline-light py-0" style="font-size: 0.65rem;">TM</a>
                <a href="{{ route('switch-role', 'dh') }}" class="btn btn-sm btn-outline-light py-0" style="font-size: 0.65rem;">DH</a>
                <a href="{{ route('switch-role', 'coordinator') }}" class="btn btn-sm btn-outline-light py-0" style="font-size: 0.65rem;">Coord</a>
                <a href="{{ route('switch-role', 'reviewer') }}" class="btn btn-sm btn-outline-light py-0" style="font-size: 0.65rem;">Review</a>
                <a href="{{ route('switch-role', 'dean') }}" class="btn btn-sm btn-outline-light py-0" style="font-size: 0.65rem;">Dean</a>
                <a href="{{ route('switch-role', 'irerc') }}" class="btn btn-sm btn-outline-light py-0" style="font-size: 0.65rem;">IRERC</a>
                <a href="{{ route('switch-role', 'vparttcs') }}" class="btn btn-sm btn-outline-light py-0" style="font-size: 0.65rem;">VP</a>
                <a href="{{ route('switch-role', 'rcsc') }}" class="btn btn-sm btn-outline-light py-0" style="font-size: 0.65rem;">RCSC</a>
                <a href="{{ route('switch-role', 'finance') }}" class="btn btn-sm btn-outline-light py-0" style="font-size: 0.65rem;">Fin</a>
                <a href="{{ route('switch-role', 'admin') }}" class="btn btn-sm btn-outline-light py-0" style="font-size: 0.65rem;">Admin</a>
            </div>

            <hr class="border-secondary my-2">

            {{-- Sign out --}}
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-outline-light w-100 fw-bold py-2 confirm-btn" style="font-size: 0.85rem;"
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
                    e.preventDefault();
                    var form = btn.closest('form');
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
        });
    </script>

    {{-- Reusable Confirmation Modal --}}
    <div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-dark text-white border-0">
                    <h5 class="modal-title fw-bold" id="confirmModalLabel">
                        <i class="bi bi-question-circle me-2"></i>Confirm Action
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center py-4" id="confirmModalBody">
                    <div class="mb-3">
                        <i class="bi bi-question-circle text-warning" style="font-size: 3rem;"></i>
                    </div>
                    <h5 class="fw-bold text-dark">Are you sure?</h5>
                    <p class="text-muted mb-0">This action cannot be undone.</p>
                </div>
                <div class="modal-footer border-0 justify-content-center gap-2 pb-4">
                    <button type="button" class="btn btn-outline-secondary px-3" data-bs-dismiss="modal">
                        <i class="bi bi-x-lg me-1"></i>Cancel
                    </button>
                    <button type="button" class="btn btn-warning px-4 fw-bold" id="confirmActionBtn">
                        <i class="bi bi-check-lg me-1"></i>Yes, Confirm
                    </button>
                </div>
            </div>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
