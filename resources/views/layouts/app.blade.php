<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RPMS - Research Project Management System</title>
    <meta name="user-role" content="{{ auth()->check() ? auth()->user()->role : '' }}">
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <!-- Bootstrap 5.3 CSS (CDN) -->
    <link href="{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="{{ asset('vendor/bootstrap/css/bootstrap-icons.css') }}" rel="stylesheet">
    <!-- RPMS Design System -->
    <link href="{{ asset('css/rpms-theme.css') }}" rel="stylesheet">
    <style>
        :root {
            --UNI-primary: #0f3e2e;
            --UNI-primary-dark: #0a2b20;
            --UNI-secondary: #1e5641;
            --UNI-accent: #2e8b57;
            --UNI-gold: #d4af37;
            --UNI-gold-dark: #b89324;
            --UNI-gold-light: #fef9e7;
            --UNI-bg: #f6f8fa;
            --UNI-card-border: #e2e8f0;
            --UNI-text: #1e293b;
            --UNI-muted: #64748b;
            --UNI-focus: rgba(46, 139, 87, 0.28);
        }
        html {
            overflow-x: hidden;
            scroll-behavior: smooth;
        }
        body {
            background-color: #f4f7f5;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            color: var(--UNI-text);
            letter-spacing: -0.01em;
            line-height: 1.55;
        }
        .skip-link {
            position: fixed;
            top: 0.75rem;
            left: 0.75rem;
            z-index: 2000;
            padding: 0.65rem 1rem;
            transform: translateY(-160%);
            border-radius: 0.5rem;
            background: #ffffff;
            color: var(--UNI-primary);
            box-shadow: 0 4px 16px rgba(15, 23, 42, 0.18);
            font-weight: 700;
        }
        .skip-link:focus {
            transform: translateY(0);
        }
        main.container {
            width: 100%;
            max-width: 1320px;
            padding-left: clamp(1rem, 2vw, 1.75rem);
            padding-right: clamp(1rem, 2vw, 1.75rem);
            padding-bottom: 1.5rem;
        }
        h1, h2, h3, h4, h5, h6 {
            color: #172b24;
            letter-spacing: -0.025em;
        }
        .text-muted {
            color: var(--UNI-muted) !important;
        }
        /* Navbar Styling */
        .navbar-UNI {
            background: linear-gradient(135deg, var(--UNI-primary) 0%, var(--UNI-primary-dark) 100%);
            box-shadow: 0 4px 20px -2px rgba(15, 62, 46, 0.35);
            border-bottom: 2px solid var(--UNI-gold);
        }
        .guest-header {
            background: linear-gradient(135deg, #0f3e2e 0%, #103b32 45%, #0c302b 100%);
            border-bottom: 2px solid rgba(212, 175, 55, 0.7);
            box-shadow: 0 12px 30px rgba(15, 62, 46, 0.18);
        }
        .guest-navbar {
            padding: 0.75rem 0;
        }
        .guest-brand-mark {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 42px;
            height: 42px;
            border-radius: 12px;
            background: rgba(212, 175, 55, 0.18);
            border: 1px solid rgba(212, 175, 55, 0.42);
            color: #f6d677;
            font-size: 1.3rem;
            flex-shrink: 0;
        }
        .guest-nav {
            gap: 1.15rem;
        }
        .guest-nav a,
        .nav-link-guest {
            color: rgba(255,255,255,0.85);
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 600;
            padding: 0.35rem 0.65rem;
            border-radius: 6px;
            transition: all 0.2s ease;
        }
        .guest-nav a:hover,
        .nav-link-guest:hover {
            color: #fff;
            background: rgba(255, 255, 255, 0.1);
        }
        .guest-nav .active-guest-link {
            color: #ffffff !important;
            background: rgba(255, 255, 255, 0.16) !important;
            box-shadow: inset 0 -2px 0 var(--UNI-gold, #d4af37);
        }
        .guest-nav .guest-login-link {
            color: var(--UNI-primary-dark) !important;
            background-color: #fff;
            border-color: #fff;
        }
        .guest-nav .guest-login-link:hover {
            color: var(--UNI-primary) !important;
            background-color: #f1f5f9;
            border-color: #f1f5f9;
        }
        @media (max-width: 767.98px) {
            .guest-navbar {
                padding: 0.5rem 0;
            }
            .guest-brand-mark {
                width: 36px;
                height: 36px;
                font-size: 1.1rem;
                border-radius: 9px;
            }
            .guest-nav {
                border-top: 1px solid rgba(255, 255, 255, 0.15);
                margin-top: 0.5rem;
                padding-top: 0.75rem;
            }
            .guest-nav .nav-link-guest {
                font-size: 0.95rem;
                padding: 0.5rem 0.75rem;
            }
            .guest-nav .guest-login-link {
                margin-top: 0.4rem;
                padding: 0.5rem 1rem;
            }
        }
        .site-footer {
            background: linear-gradient(135deg, #0d2e26 0%, #102f2c 100%);
            border-top: 1px solid rgba(212, 175, 55, 0.35);
            color: rgba(255,255,255,0.9);
        }
        .site-footer-brand {
            display: inline-flex;
            width: 42px;
            height: 42px;
            border-radius: 12px;
            align-items: center;
            justify-content: center;
            background: rgba(212, 175, 55, 0.16);
            color: #f6d677;
            font-size: 1.25rem;
        }
        .footer-title {
            color: #fef3c7;
            font-size: 0.8rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            margin-bottom: 0.7rem;
        }
        .footer-links {
            display: grid;
            gap: 0.45rem;
        }
        .footer-links a,
        .footer-links span {
            color: rgba(255,255,255,0.7);
            text-decoration: none;
            font-size: 0.92rem;
        }
        .footer-links a:hover {
            color: #fff;
        }
        .footer-bottom {
            font-size: 0.8rem;
        }
        /* Fixed navbar on desktop */

        .brand-text {
            color: #ffffff;
            font-weight: 800;
            letter-spacing: 0.8px;
            font-size: 0.95rem;
        }
        .brand-sub {
            color: var(--UNI-gold);
            font-size: 0.72rem;
            font-weight: 500;
            letter-spacing: 0.2px;
        }
        .badge-call-cycle {
            background: rgba(212, 175, 55, 0.15);
            color: var(--UNI-gold);
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
        .navbar-UNI .nav-link {
            color: rgba(255, 255, 255, 0.85) !important;
            font-weight: 500;
            font-size: 0.88rem;
            padding: 0.45rem 0.75rem !important;
            border-radius: 6px;
            transition: all 0.2s ease-in-out;
            white-space: nowrap;
        }
        .navbar-UNI .nav-link:hover {
            color: #ffffff !important;
            background: rgba(255, 255, 255, 0.08);
        }
        .navbar-UNI .nav-link.active-link {
            color: var(--UNI-gold) !important;
            background: rgba(212, 175, 55, 0.15);
            font-weight: 700 !important;
        }
        #navbarOffcanvas .nav-link.active-link {
            color: var(--UNI-gold) !important;
            background: rgba(212, 175, 55, 0.2);
            border-left: 3px solid var(--UNI-gold);
            font-weight: 700 !important;
        }
        /* Card Styling */
        .card-custom {
            border: 1px solid var(--UNI-card-border);
            border-radius: 14px;
            background: #ffffff;
            box-shadow: 0 2px 8px rgba(15, 62, 46, 0.035), 0 1px 2px rgba(15, 23, 42, 0.03);
            transition: box-shadow 0.2s ease, border-color 0.2s ease;
        }
        .card-custom:hover {
            border-color: #d5e2da;
            box-shadow: 0 8px 24px rgba(15, 62, 46, 0.075);
        }
        .card-custom-header {
            background: #ffffff;
            border-bottom: 1px solid #edf2f7;
            padding: 1rem 1.25rem;
            border-top-left-radius: 14px;
            border-top-right-radius: 14px;
        }
        .page-heading {
            margin-bottom: 1.5rem;
        }
        .page-heading h1,
        .page-heading h2,
        .page-heading h3 {
            margin-bottom: 0.2rem;
            font-weight: 750;
        }
        .page-heading p,
        .page-heading .text-muted {
            margin-bottom: 0;
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
            color: var(--UNI-gold) !important;
            border: 1px solid rgba(212, 175, 55, 0.4) !important;
        }
        /* Clean Dark / White Buttons across all pages */
        .btn-success, .btn-primary {
            background-color: var(--UNI-primary) !important;
            border-color: var(--UNI-primary) !important;
            color: #ffffff !important;
        }
        .btn-dark {
            background-color: #0f172a !important;
            border-color: #0f172a !important;
            color: #ffffff !important;
        }
        .btn-success:hover, .btn-primary:hover {
            background-color: var(--UNI-primary-dark) !important;
            border-color: var(--UNI-primary-dark) !important;
            color: #ffffff !important;
        }
        .btn-dark:hover {
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
        .btn-outline-success, .btn-outline-primary {
            border-color: #a9c8b5 !important;
            color: var(--UNI-primary) !important;
        }
        .btn-outline-success:hover, .btn-outline-primary:hover, .btn-outline-dark:hover, .btn-outline-secondary:hover {
            background-color: #f1f5f9 !important;
            border-color: #94a3b8 !important;
            color: #0f172a !important;
        }
        .btn-outline-success:hover, .btn-outline-primary:hover {
            background-color: #edf5ef !important;
            border-color: var(--UNI-primary) !important;
            color: var(--UNI-primary-dark) !important;
        }
        .btn {
            border-radius: 0.55rem;
            font-weight: 600;
            transition: background-color 0.16s ease, border-color 0.16s ease, box-shadow 0.16s ease;
        }
        .btn:focus-visible,
        .form-control:focus-visible,
        .form-select:focus-visible,
        .form-check-input:focus-visible,
        .upload-zone:focus-visible,
        a:focus-visible,
        button:focus-visible {
            outline: 3px solid var(--UNI-focus);
            outline-offset: 2px;
        }
        .form-control,
        .form-select {
            min-height: 42px;
            border-color: #cbd5e1;
            border-radius: 0.55rem;
            color: #1e293b;
        }
        .form-control:focus,
        .form-select:focus {
            border-color: var(--UNI-accent);
            box-shadow: 0 0 0 0.2rem var(--UNI-focus);
        }

        /* Direct, Instant Checkbox Rendering across All Browsers */
        .form-check-input {
            cursor: pointer;
            width: 1.25em;
            height: 1.25em;
            margin-top: 0.15em;
            border: 1.5px solid #94a3b8;
            border-radius: 0.35rem;
            background-color: #ffffff;
            transition: background-color 0.1s ease-in-out, border-color 0.1s ease-in-out, box-shadow 0.1s ease-in-out;
        }
        .form-check-input:hover {
            border-color: var(--UNI-accent);
        }
        .form-check-input:focus {
            border-color: var(--UNI-accent);
            box-shadow: 0 0 0 0.2rem var(--UNI-focus);
            outline: none;
        }
        .form-check-input:checked,
        .form-check-input:checked[type=checkbox] {
            background-color: var(--UNI-primary) !important;
            border-color: var(--UNI-primary) !important;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20'%3e%3cpath fill='none' stroke='%23ffffff' stroke-linecap='round' stroke-linejoin='round' stroke-width='3' d='m5 10 3.5 3.5 7-7'/%3e%3c/svg%3e") !important;
            background-repeat: no-repeat !important;
            background-position: center !important;
            background-size: 75% !important;
        }
        .form-check-label {
            cursor: pointer;
            user-select: none;
            padding-left: 0.35rem;
        }
        .form-control-sm,
        .form-select-sm {
            min-height: 36px;
        }
        .form-label {
            color: #334155;
        }
        .table {
            --bs-table-hover-bg: rgba(15, 62, 46, 0.035);
            color: #334155;
        }
        .table > thead th {
            color: #475569;
            background-color: #f7f9f8;
            white-space: nowrap;
        }
        .table > tbody tr:last-child td {
            border-bottom: 0;
        }
        .table-responsive {
            border-radius: 0.6rem;
        }
        .alert {
            border-radius: 0.75rem;
        }
        .pagination {
            gap: 0.2rem;
        }
        .page-link {
            color: var(--UNI-primary);
            border-radius: 0.4rem;
        }
        .page-item.active .page-link {
            background-color: var(--UNI-primary);
            border-color: var(--UNI-primary);
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
        border-color: var(--UNI-primary, #0f3e2e) !important;
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
        color: var(--UNI-primary, #0f3e2e);
    }
    .custom-select-option.selected {
        background: var(--UNI-primary, #0f3e2e);
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
        .dropdown-item {
            font-size: 0.86rem;
            border-radius: 6px;
            padding: 0.45rem 0.85rem;
        }
        .dropdown-item:hover {
            background-color: #f1f5f9;
            color: var(--UNI-primary);
        }

        /* Mobile Responsive Media Queries */
        @media (max-width: 991.98px) {
            main.container {
                padding-left: 1rem;
                padding-right: 1rem;
            }
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
                border-radius: 12px;
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
            .navbar-UNI {
                position: fixed !important;
                top: 0;
                left: 0;
                right: 0;
                z-index: 1060 !important;
                width: 100%;
            }
            /* Add padding below navbar for fixed position */
            body.app-screen {
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
            .navbar-UNI .d-flex .text-muted.small {
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
        @media (prefers-reduced-motion: reduce) {
            html {
                scroll-behavior: auto;
            }
            *,
            *::before,
            *::after {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                scroll-behavior: auto !important;
                transition-duration: 0.01ms !important;
            }
        }

        :root {
            --UNI-primary: #155e4b;
            --UNI-primary-dark: #10483a;
            --UNI-secondary: #21765e;
            --UNI-accent: #2f9875;
            --UNI-gold: #d8b45a;
            --UNI-bg: #f4f7f6;
            --UNI-card-border: #e3eae7;
            --UNI-text: #172b25;
            --UNI-muted: #718078;
        }
        body {
            background:
                radial-gradient(ellipse at 0 0, rgba(220, 237, 228, 0.42), transparent 36rem),
                #f5f7f6;
            color: var(--UNI-text);
        }
        .navbar-UNI {
            background: rgba(16, 54, 45, 0.97);
            border-bottom: 1px solid rgba(216, 180, 90, 0.55);
            box-shadow: 0 8px 24px rgba(18, 45, 38, 0.14);
            backdrop-filter: blur(14px);
        }
        .navbar-UNI > .container {
            max-width: 1320px;
            padding-left: clamp(1rem, 2vw, 1.75rem);
            padding-right: clamp(1rem, 2vw, 1.75rem);
        }
        .navbar-UNI .navbar-nav {
            flex-wrap: nowrap;
        }
        .navbar-UNI .nav-link {
            font-size: 0.82rem;
            padding: 0.48rem 0.65rem !important;
            border-radius: 0.65rem;
        }
        .navbar-UNI .nav-link.active-link {
            color: #fff !important;
            background: rgba(255, 255, 255, 0.14);
            box-shadow: inset 0 -2px 0 var(--UNI-gold);
        }
        .brand-text {
            letter-spacing: 0.06em;
        }
        main.container {
            max-width: 1320px;
            padding-bottom: 2rem;
        }
        .card-custom {
            border-color: #e4ebe8;
            border-radius: 1rem;
            box-shadow: 0 5px 20px rgba(25, 54, 45, 0.045), 0 1px 3px rgba(25, 54, 45, 0.035);
        }
        .card-custom:hover {
            border-color: #d6e4dd;
            box-shadow: 0 12px 30px rgba(25, 54, 45, 0.08);
        }
        .card-custom-header {
            padding: 1.1rem 1.35rem;
        }
        .page-heading {
            margin-bottom: 1.75rem;
        }
        .page-heading h1,
        .page-heading h2,
        .page-heading h3 {
            color: #16372d;
            letter-spacing: -0.035em;
        }
        .btn {
            border-radius: 0.7rem;
            padding-top: 0.58rem;
            padding-bottom: 0.58rem;
        }
        .btn-sm {
            padding-top: 0.38rem;
            padding-bottom: 0.38rem;
        }
        .form-control,
        .form-select,
        input[type="text"],
        input[type="search"],
        input[type="email"],
        input[type="password"],
        input[type="number"],
        input[type="date"],
        textarea,
        select {
            background-color: #ffffff !important;
            color: #1e293b !important;
            border: 1px solid #cbd5e1 !important;
            border-radius: 0.65rem;
        }
        .form-control::placeholder,
        input::placeholder,
        textarea::placeholder {
            color: #94a3b8 !important;
            opacity: 1 !important;
        }
        .form-control,
        .form-select {
            min-height: 42px;
        }
        .form-control:focus,
        .form-select:focus,
        input:focus,
        textarea:focus,
        select:focus {
            background-color: #ffffff !important;
            color: #0f172a !important;
            border-color: var(--UNI-accent) !important;
            box-shadow: 0 0 0 0.22rem rgba(47, 152, 117, 0.18) !important;
            outline: none !important;
        }
        .input-group-text {
            background-color: #ffffff !important;
            color: #64748b !important;
            border: 1px solid #cbd5e1 !important;
            border-radius: 0.65rem;
        }
        .input-group > .input-group-text {
            border-top-right-radius: 0 !important;
            border-bottom-right-radius: 0 !important;
        }
        .input-group > .form-control {
            border-top-left-radius: 0 !important;
            border-bottom-left-radius: 0 !important;
        }
        .input-group > .input-group-text.border-end-0 {
            border-right: 0 !important;
        }
        .input-group > .form-control.border-start-0 {
            border-left: 0 !important;
        }
        select.form-select option,
        select option {
            background-color: #ffffff !important;
            color: #1e293b !important;
        }
        .table {
            --bs-table-hover-bg: #f5f9f7;
        }
        .table > thead th {
            color: #61736b;
            background: #f5f8f6;
            border-bottom-color: #e4ebe7;
            letter-spacing: 0.075em;
        }
        .table > tbody td {
            border-bottom-color: #edf1ef;
        }
        .stat-bubble {
            width: 48px;
            height: 48px;
            border: 0;
            border-radius: 14px;
            background: #edf5f1;
            color: var(--UNI-primary);
        }
        .alert {
            border: 1px solid rgba(25, 54, 45, 0.08);
            box-shadow: 0 3px 12px rgba(25, 54, 45, 0.035);
        }
        .dropdown-menu {
            border-color: #e3eae7;
            border-radius: 0.9rem;
            box-shadow: 0 16px 38px rgba(19, 43, 36, 0.15);
        }
        .auth-screen {
            background: #f3f6f4;
        }
        .auth-screen .navbar-UNI,
        .auth-screen footer {
            display: none !important;
        }
        main.auth-main {
            max-width: none;
            margin: 0 !important;
            padding: 1.5rem 1rem !important;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: calc(100vh - 68px);
        }
        @media (max-width: 768px) {
            main.auth-main {
                padding: 1rem 0.5rem !important;
                min-height: auto;
                align-items: flex-start;
            }
        }
        .guest-screen main.container {
            max-width: none;
            margin: 0 !important;
            padding: 0 !important;
        }
        .dashboard-heading {
            padding: 1.6rem 1.8rem;
            border: 1px solid #e1ebe5;
            border-radius: 1.2rem;
            background: linear-gradient(115deg, #fff 0%, #f1f7f3 100%);
            box-shadow: 0 8px 26px rgba(25, 54, 45, 0.045);
        }
        @media (max-width: 991.98px) {
            .navbar-UNI .navbar-nav {
                flex-wrap: wrap;
            }
            .dashboard-heading {
                padding: 1.25rem;
            }
        }
        @media (max-width: 575.98px) {
            .page-heading {
                margin-bottom: 1.2rem;
            }
            .dashboard-heading {
                border-radius: 1rem;
            }
        }
    </style>
    @yield('styles')
</head>
<body class="d-flex flex-column min-vh-100 @auth app-screen @endauth @guest @if(request()->routeIs('login')) auth-screen justify-content-center @else guest-screen @endif @endguest">

    <a class="skip-link" href="#main-content">Skip to main content</a>

    @guest
    <header class="guest-header sticky-top">
        <nav class="navbar navbar-expand-md guest-navbar">
            <div class="container">
                <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('home') }}">
                    <span class="guest-brand-mark"><i class="bi bi-journal-bookmark-fill"></i></span>
                    <div>
                        <div class="brand-text fs-6 mb-0">RPMS</div>
                        <div class="brand-sub">Research Project Management System</div>
                    </div>
                </a>

                <!-- Mobile Hamburger Toggler for Guest Navbar -->
                <button class="navbar-toggler border-0 p-1 text-white shadow-none d-md-none" type="button" data-bs-toggle="collapse" data-bs-target="#guestNavbarNav" aria-controls="guestNavbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <i class="bi bi-list fs-1 text-white"></i>
                </button>

                <!-- Collapsible Navigation Menu -->
                <div class="collapse navbar-collapse" id="guestNavbarNav">
                    <div class="guest-nav d-flex flex-column flex-md-row align-items-stretch align-items-md-center gap-2 gap-md-3 ms-auto mt-2 mt-md-0 pt-2 pt-md-0">
                        <a class="nav-link-guest {{ request()->routeIs('home') ? 'active-guest-link' : '' }}" href="{{ route('home') }}">Home</a>
                        <a class="nav-link-guest {{ request()->routeIs('lifecycle') ? 'active-guest-link' : '' }}" href="{{ route('lifecycle') }}">Lifecycle</a>
                        <a class="nav-link-guest {{ request()->routeIs('support') ? 'active-guest-link' : '' }}" href="{{ route('support') }}">Support</a>
                        @auth
                            <a class="btn btn-light btn-sm rounded-pill px-3 guest-login-link text-center fw-bold mt-1 mt-md-0" href="{{ route('dashboard') }}"><i class="bi bi-speedometer2 me-1"></i>Dashboard</a>
                        @else
                            <a class="btn btn-light btn-sm rounded-pill px-3 guest-login-link text-center fw-bold mt-1 mt-md-0" href="{{ route('login') }}"><i class="bi bi-box-arrow-in-right me-1"></i>Sign In</a>
                        @endauth
                    </div>
                </div>
            </div>
        </nav>
    </header>
    @else
    <!-- Navigation Header -->
    <nav class="navbar navbar-expand-lg navbar-UNI py-2 sticky-lg-top @yield('navbar-class')" style="z-index: 1050;">
        <div class="container @yield('navbar-container-class')">
            <!-- Brand Identity -->
            <a class="navbar-brand d-flex align-items-center me-4" href="{{ route('dashboard') }}">
                <i class="bi bi-journal-bookmark-fill fs-2 text-warning me-2"></i>
                <div>
                    <div class="brand-text fs-6">RPMS</div>
                    <div class="brand-sub">Research Project Management System</div>
                </div>
            </a>

            <!-- Mobile Right Controls (Notifications & Hamburger) -->
            <div class="d-flex align-items-center gap-2 d-lg-none ms-auto">
                {{-- Mobile Notifications Bell --}}
                <div class="dropdown">
                    <button class="btn btn-link p-1 border-0 position-relative text-white" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Notifications">
                        <i class="bi bi-bell-fill fs-4 text-warning"></i>
                        @if(($notifCount ?? 0) > 0)
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size:0.6rem; margin-top: -2px;">
                                {{ ($notifCount ?? 0) > 99 ? '99+' : ($notifCount ?? 0) }}
                            </span>
                        @endif
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end p-2 shadow-lg" style="width: 290px; max-height: 360px; overflow-y: auto;">
                        <li class="dropdown-header fw-bold d-flex justify-content-between align-items-center border-bottom pb-2 mb-2">
                            <span><i class="bi bi-bell me-1 text-warning"></i> Notifications</span>
                            <span class="d-flex align-items-center gap-1">
                                <span class="badge bg-dark rounded-pill">{{ $notifCount ?? 0 }}</span>
                                @if(($notifCount ?? 0) > 0)
                                <form action="{{ route('notifications.read-all') }}" method="POST" class="d-inline m-0">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-link p-0 text-decoration-none" style="font-size:0.7rem;" title="Mark all as read">Mark read</button>
                                </form>
                                @endif
                            </span>
                        </li>
                        @forelse($notifItems ?? [] as $item)
                            <li class="mb-2">
                                <a class="dropdown-item rounded p-2 text-wrap bg-light border-start border-3 border-warning" href="#">
                                    <div class="d-flex align-items-start gap-2">
                                        <i class="bi {{ $item['icon'] ?? 'bi-bell' }} fs-6 mt-1 text-warning"></i>
                                        <div>
                                            <div class="fw-bold small text-dark">{{ $item['title'] ?? '' }}</div>
                                            <div class="small text-muted" style="font-size: 0.75rem;">{{ $item['desc'] ?? '' }}</div>
                                            <div class="text-muted" style="font-size: 0.65rem;"><i class="bi bi-clock me-1"></i>{{ $item['time'] ?? 'Now' }}</div>
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

                    {{-- 12. Admin: Backups & Migration --}}
                    @if(Auth::user()->role === 'admin')
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle {{ request()->routeIs('admin.backups', 'admin.data-migration') ? 'active-link' : '' }}" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-hdd-stack me-1"></i> System
                        </a>
                        <ul class="dropdown-menu shadow-sm">
                            <li><a class="dropdown-item small" href="{{ route('admin.backups') }}"><i class="bi bi-hdd-stack me-2 text-dark"></i>Backup &amp; Recovery</a></li>
                            <li><a class="dropdown-item small" href="{{ route('admin.data-migration') }}"><i class="bi bi-box-arrow-in-down me-2 text-dark"></i>Data Migration</a></li>
                            <li><a class="dropdown-item small" href="{{ route('reports.index') }}"><i class="bi bi-file-earmark-pdf me-2 text-dark"></i>Institutional Reports</a></li>
                        </ul>
                    </li>
                    @endif

                </ul>

                <!-- User Profile & Action Controls -->
                <div class="d-flex align-items-center text-white ms-auto gap-2 gap-xl-3">
                    {{-- Command Palette Trigger --}}
                    <button id="rpms-search-trigger" type="button" class="btn btn-link p-0 border-0 d-none d-md-flex align-items-center text-decoration-none text-white-50 gap-1" title="Search (Ctrl+K)" style="font-size:0.8rem;">
                        <i class="bi bi-search fs-5"></i>
                        <kbd style="font-size:0.65rem; background:rgba(255,255,255,0.1); border:1px solid rgba(255,255,255,0.2); padding:1px 5px; border-radius:4px; color:rgba(255,255,255,0.6);">Ctrl K</kbd>
                    </button>

                    {{-- Notifications Bell Dropdown (SCR-17) --}}
                    {{-- Notifications loaded via LoadNotifications middleware --}}
                    <div class="dropdown">
                        <button class="btn btn-link p-0 border-0 position-relative d-flex align-items-center text-decoration-none" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Notifications">
                            <i class="bi bi-bell-fill fs-5 text-warning"></i>
                            @if(($notifCount ?? 0) > 0)
                                <span id="rpms-notif-badge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size:0.65rem; margin-top: -2px;">
                                    {{ ($notifCount ?? 0) > 99 ? '99+' : ($notifCount ?? 0) }}
                                </span>
                            @else
                                <span id="rpms-notif-badge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size:0.65rem; margin-top: -2px; display:none;">0</span>
                            @endif
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end p-2 shadow-lg" style="width: 320px; max-height: 380px; overflow-y: auto;">
                            <li class="dropdown-header fw-bold d-flex justify-content-between align-items-center border-bottom pb-2 mb-2">
                                <span><i class="bi bi-bell me-1 text-warning"></i> Notifications Center</span>
                                <span class="d-flex align-items-center gap-1">
                                    <span class="badge bg-dark rounded-pill">{{ $notifCount ?? 0 }}</span>
                                    @if(($notifCount ?? 0) > 0)
                                    <form action="{{ route('notifications.read-all') }}" method="POST" class="d-inline m-0">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-link p-0 text-decoration-none" style="font-size:0.7rem;" title="Mark all as read">Mark all read</button>
                                    </form>
                                    @endif
                                </span>
                            </li>
                            @forelse($notifItems ?? [] as $item)
                                <li class="mb-2">
                                    <a class="dropdown-item rounded p-2 text-wrap bg-light border-start border-3 border-warning" href="#">
                                        <div class="d-flex align-items-start gap-2">
                                            <i class="bi {{ $item['icon'] ?? 'bi-bell' }} fs-6 mt-1 text-warning"></i>
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
                    <div class="d-none d-xl-flex align-items-center fw-semibold rounded-pill px-3 py-1 text-white" style="font-size:0.82rem; cursor:pointer; transition: transform 0.2s; background: rgba(255,255,255,0.12); border: 1px solid rgba(255,255,255,0.22);" data-bs-toggle="modal" data-bs-target="#calendarModal" title="View Academic Calendar">
                        <i class="bi bi-calendar2-check me-2 text-warning"></i> <span class="text-white">AY 2026/2027</span>
                    </div>

                    {{-- User Profile Dropdown --}}
                    <div class="dropdown border-start border-secondary ps-3">
                        <a href="#" class="text-white text-decoration-none d-flex align-items-center gap-2 dropdown-toggle-custom" id="userProfileMenuBtn" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="text-end">
                                <div class="fw-bold small lh-1 text-white" style="white-space: nowrap;">{{ preg_replace('/\s*\([^)]*\)/', '', Auth::user()->name) }}</div>
                                <span class="badge badge-role mt-1" style="font-size: 0.65rem; padding: 2px 6px; cursor: pointer;">{{ strtoupper(Auth::user()->role) }} <i class="bi bi-chevron-down ms-1" style="font-size:0.6rem;"></i></span>
                            </div>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end p-2 shadow-lg profile-dropdown-menu" aria-labelledby="userProfileMenuBtn" style="min-width: 290px; max-height: 480px; overflow-y: auto; z-index: 1050;">
                            {{-- Account Mini Card --}}
                            <li class="p-2 mb-2 rounded bg-light border">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle bg-dark text-white d-flex align-items-center justify-content-center fw-bold" style="width: 36px; height: 36px; font-size: 0.85rem;">
                                        {{ collect(explode(' ', preg_replace('/\s*\([^)]*\)/', '', Auth::user()->name)))->map(fn($p) => substr($p, 0, 1))->take(2)->join('') ?: 'UNI' }}
                                    </div>
                                    <div class="flex-grow-1 text-truncate">
                                        <div class="fw-bold text-dark small text-truncate">{{ preg_replace('/\s*\([^)]*\)/', '', Auth::user()->name) }}</div>
                                        <div class="text-muted" style="font-size: 0.72rem;">{{ Auth::user()->email }}</div>
                                    </div>
                                </div>
                                <div class="mt-2 pt-2 border-top d-flex justify-content-between align-items-center">
                                    <span class="badge bg-dark font-monospace" style="font-size: 0.65rem;">{{ Auth::user()->staff_id ?? 'UNI-STAFF' }}</span>
                                    <a href="{{ route('profile') }}" class="btn btn-sm d-flex align-items-center gap-1 fw-semibold text-white" style="font-size: 0.75rem; background: var(--UNI-secondary); border: 1px solid rgba(255,255,255,0.2); padding: 3px 10px; border-radius: 6px;">
                                        <i class="bi bi-person-circle"></i> View Profile
                                    </a>
                                </div>
                            </li>



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
        </div>
    </nav>

    @auth
    <!-- Mobile Offcanvas Right Sidebar -->
    <div class="offcanvas offcanvas-end text-white d-lg-none" tabindex="-1" id="navbarOffcanvas" aria-labelledby="navbarOffcanvasLabel" style="background-color: var(--UNI-primary-dark); max-width: 320px; width: 78vw;">
        <div class="offcanvas-header border-bottom border-secondary pb-2 pt-3 px-3">
            <div class="d-flex align-items-center">
                <i class="bi bi-journal-bookmark-fill fs-3 text-warning me-2"></i>
                <div>
                    <div class="fw-bold text-white small lh-1">RPMS</div>
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
                        {{ collect(explode(' ', preg_replace('/\s*\([^)]*\)/', '', Auth::user()->name)))->map(fn($p) => substr($p, 0, 1))->take(2)->join('') ?: 'UNI' }}
                    </div>
                    <div class="flex-grow-1 text-truncate">
                        <div class="fw-bold text-white small text-truncate">{{ preg_replace('/\s*\([^)]*\)/', '', Auth::user()->name) }}</div>
                        <div class="text-white-50 text-truncate" style="font-size: 0.72rem;">{{ Auth::user()->email }}</div>
                    </div>
                </div>
                <div class="mt-2 pt-2 d-flex justify-content-between align-items-center" style="border-top: 1px solid rgba(255,255,255,0.15);">
                    <span class="badge bg-dark font-monospace" style="font-size: 0.65rem;">{{ Auth::user()->staff_id ?? 'UNI-STAFF' }}</span>
                    <a href="{{ route('profile') }}" class="btn btn-sm d-flex align-items-center gap-1 fw-semibold text-white" style="font-size: 0.75rem; background: var(--UNI-secondary); border: 1px solid rgba(255,255,255,0.2); padding: 3px 10px; border-radius: 6px;">
                        <i class="bi bi-person-circle"></i> View Profile
                    </a>
                </div>
            </div>

            {{-- Mobile Academic Call Badge --}}
            <div class="mb-3 py-2 px-3 rounded-pill d-flex align-items-center justify-content-center shadow-sm mx-2" style="background: rgba(255,255,255,0.12); border: 1px solid rgba(255,255,255,0.22); font-size: 0.8rem; font-weight: 600; cursor:pointer;" data-bs-toggle="modal" data-bs-target="#calendarModal" data-bs-dismiss="offcanvas">
                <i class="bi bi-calendar2-check me-2 text-warning"></i> <span class="text-white">AY 2026/2027</span>
            </div>

            {{-- Main Navigation Links --}}
            <ul class="nav nav-pills flex-column gap-1 mb-3">
                {{-- 1. Dashboard (All Roles) --}}
                <li class="nav-item">
                    <a class="nav-link text-white py-2 px-2 {{ request()->routeIs('dashboard') ? 'active-link' : '' }}" href="{{ route('dashboard') }}" style="font-size: 0.85rem;">
                        <i class="bi bi-speedometer2 me-2 text-white-50"></i> Dashboard
                    </a>
                </li>

                {{-- 2. Admin Dedicated Navigation --}}
                @if(Auth::user()->role === 'admin')
                <li class="nav-item">
                    <a class="nav-link text-white py-2 px-2 {{ request()->routeIs('admin.users') ? 'active-link' : '' }}" href="{{ route('admin.users') }}" style="font-size: 0.85rem;">
                        <i class="bi bi-people me-2 text-white-50"></i> Users
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white py-2 px-2 {{ request()->routeIs('admin.thematic-areas') ? 'active-link' : '' }}" href="{{ route('admin.thematic-areas') }}" style="font-size: 0.85rem;">
                        <i class="bi bi-diagram-3 me-2 text-white-50"></i> Thematic Areas
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
                <li class="nav-item">
                    <a class="nav-link text-white py-2 px-2 {{ request()->routeIs('admin.backups') ? 'active-link' : '' }}" href="{{ route('admin.backups') }}" style="font-size: 0.85rem;">
                        <i class="bi bi-hdd-stack me-2 text-white-50"></i> System Backups
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white py-2 px-2 {{ request()->routeIs('admin.data-migration') ? 'active-link' : '' }}" href="{{ route('admin.data-migration') }}" style="font-size: 0.85rem;">
                        <i class="bi bi-box-arrow-in-down me-2 text-white-50"></i> Data Migration
                    </a>
                </li>
                @else
                    {{-- 3. Projects (for roles that manage projects, excluding TM & Reviewer) --}}
                    @if(Auth::user()->hasPermission('view_projects') && !in_array(Auth::user()->role, ['tm', 'reviewer']))
                    <li class="nav-item">
                        <a class="nav-link text-white py-2 px-2 {{ request()->routeIs('projects.*') ? 'active-link' : '' }}" href="{{ route('projects.index') }}" style="font-size: 0.85rem;">
                            <i class="bi bi-folder2-open me-2 text-white-50"></i> Projects
                        </a>
                    </li>
                    @endif

                    {{-- 3a. My Projects (TM) --}}
                    @if(Auth::user()->role === 'tm')
                    <li class="nav-item">
                        <a class="nav-link text-white py-2 px-2 {{ request()->routeIs('projects.*') ? 'active-link' : '' }}" href="{{ route('projects.index') }}" style="font-size: 0.85rem;">
                            <i class="bi bi-folder2-open me-2 text-white-50"></i> My Projects
                        </a>
                    </li>
                    @endif

                    {{-- 3b. My Evaluations (Reviewer) --}}
                    @if(Auth::user()->role === 'reviewer')
                    <li class="nav-item">
                        <a class="nav-link text-white py-2 px-2 {{ request()->routeIs('evaluations.*') ? 'active-link' : '' }}" href="{{ route('evaluations.index') }}" style="font-size: 0.85rem;">
                            <i class="bi bi-clipboard-check me-2 text-white-50"></i> My Evaluations
                        </a>
                    </li>
                    @endif

                    {{-- 4. Progress Reports (PI & TM) --}}
                    @if(Auth::user()->hasPermission('submit_progress_report'))
                    <li class="nav-item">
                        <a class="nav-link text-white py-2 px-2 {{ request()->routeIs('progress.*') ? 'active-link' : '' }}" href="{{ route('progress.index') }}" style="font-size: 0.85rem;">
                            <i class="bi bi-graph-up me-2 text-white-50"></i> Progress Reports
                        </a>
                    </li>
                    @endif

                    {{-- 5. Extensions (PI Requester) --}}
                    @if(Auth::user()->hasAnyPermission(['request_extension', 'request_amendment']) && !Auth::user()->hasAnyPermission(['approve_extensions', 'approve_amendments']))
                    <li class="nav-item">
                        <a class="nav-link text-white py-2 px-2 {{ request()->routeIs('extensions.*') ? 'active-link' : '' }}" href="{{ route('extensions.index') }}" style="font-size: 0.85rem;">
                            <i class="bi bi-clock-history me-2 text-white-50"></i> Extensions
                        </a>
                    </li>
                    @endif

                    {{-- 6. DH Screening (DH) --}}
                    @if(Auth::user()->hasPermission('screen_proposals'))
                    <li class="nav-item">
                        <a class="nav-link text-white py-2 px-2 {{ request()->routeIs('dh.screening') ? 'active-link' : '' }}" href="{{ route('dh.screening') }}" style="font-size: 0.85rem;">
                            <i class="bi bi-ui-checks me-2 text-white-50"></i> DH Screening
                        </a>
                    </li>
                    @endif

                    {{-- 7. Coordinator Hub (Coordinator) --}}
                    @if(Auth::user()->hasPermission('assign_reviewer'))
                    <li class="nav-item">
                        <a class="nav-link text-white py-2 px-2 {{ request()->routeIs('coordinator.*') ? 'active-link' : '' }}" href="{{ route('coordinator.hub') }}" style="font-size: 0.85rem;">
                            <i class="bi bi-briefcase me-2 text-white-50"></i> Coordinator Hub
                        </a>
                    </li>
                    @endif

                    {{-- 8. Dean Approvals (Dean) --}}
                    @if(Auth::user()->role === 'dean')
                    <li class="nav-item">
                        <a class="nav-link text-white py-2 px-2 {{ request()->routeIs('dean.approvals') ? 'active-link' : '' }}" href="{{ route('dean.approvals') }}" style="font-size: 0.85rem;">
                            <i class="bi bi-bank me-2 text-white-50"></i> Dean Approvals (&lt;500k)
                        </a>
                    </li>
                    @endif

                    {{-- 9. IRERC Ethics Panel --}}
                    @if(Auth::user()->hasPermission('ethics_review'))
                    <li class="nav-item">
                        <a class="nav-link text-white py-2 px-2 {{ request()->routeIs('irerc.panel') ? 'active-link' : '' }}" href="{{ route('irerc.panel') }}" style="font-size: 0.85rem;">
                            <i class="bi bi-shield-exclamation me-2 text-white-50"></i> IRERC Ethics Panel
                        </a>
                    </li>
                    @endif

                    {{-- 10. RCSC Portal --}}
                    @if(Auth::user()->hasPermission('view_rcsc_portal'))
                    <li class="nav-item">
                        <a class="nav-link text-white py-2 px-2 {{ request()->routeIs('rcsc.*') ? 'active-link' : '' }}" href="{{ route('rcsc.portal') }}" style="font-size: 0.85rem;">
                            <i class="bi bi-shield-lock me-2 text-white-50"></i> RCSC Portal
                        </a>
                    </li>
                    @endif

                    {{-- 11. Extensions Review (Approvers: RCSC, VP) --}}
                    @if(Auth::user()->hasAnyPermission(['approve_extensions', 'approve_amendments']))
                    <li class="nav-item">
                        <a class="nav-link text-white py-2 px-2 {{ request()->routeIs('extensions.*') ? 'active-link' : '' }}" href="{{ route('extensions.index') }}" style="font-size: 0.85rem;">
                            <i class="bi bi-clock-history me-2 text-white-50"></i> Extensions Review
                        </a>
                    </li>
                    @endif

                    {{-- 12. Finance Disbursement --}}
                    @if(Auth::user()->hasPermission('process_disbursement'))
                    <li class="nav-item">
                        <a class="nav-link text-white py-2 px-2 {{ request()->routeIs('finance.*') ? 'active-link' : '' }}" href="{{ route('finance.disbursement') }}" style="font-size: 0.85rem;">
                            <i class="bi bi-cash-stack me-2 text-white-50"></i> Finance Disbursement
                        </a>
                    </li>
                    @endif
                @endif
            </ul>

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
    @endguest

    <!-- Main Content Container -->
    <main class="container my-4 flex-grow-1 @guest @if(request()->routeIs('login')) auth-main @endif @endguest" id="main-content">
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

        @if(isset($errors) && $errors->any() && !request()->routeIs('login', 'password.*', 'register'))
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

    @guest
    <!-- Public Site Footer -->
    <footer class="site-footer mt-auto py-3">
        <div class="container">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3 text-center text-md-start">
                <div class="d-flex align-items-center gap-2">
                    <span class="guest-brand-mark" style="width:32px;height:32px;font-size:1rem;"><i class="bi bi-journal-bookmark-fill"></i></span>
                    <span class="fw-bold text-white fs-6">RPMS</span>
                    <span class="text-white-50 small">&mdash; University Research Management System</span>
                </div>
                <div class="d-flex gap-4 small">
                    <a href="{{ route('home') }}" class="text-white text-decoration-none fw-semibold">Home</a>
                    <a href="{{ route('lifecycle') }}" class="text-white text-decoration-none fw-semibold">Lifecycle</a>
                    <a href="{{ route('support') }}" class="text-white text-decoration-none fw-semibold">Support</a>
                </div>
                <div class="text-white-50 small">
                    &copy; {{ date('Y') }} RPMS. All rights reserved.
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5.3 JS Bundle (local) -->
    @endguest

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
    
    /* Modal scroll lock */
    body:has(.modal.show),
    html:has(.modal.show),
    body:has(.offcanvas.show),
    html:has(.offcanvas.show) {
        overflow: hidden !important;
    }


    /* Navbar sticky */
    .navbar-UNI {
        position: sticky !important;
        top: 0 !important;
        z-index: 1050 !important;
    }
    @media (max-width: 991.98px) {
        .navbar-UNI {
            position: fixed !important;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1060 !important;
            width: 100%;
        }
        body.app-screen { padding-top: 70px !important; }
    }
    </style>

    @stack('scripts')

    <!-- RPMS Pro: Command Palette Overlay -->
    <div id="rpms-command-overlay" class="rpms-command-overlay">
        <div class="rpms-command-box">
            <div class="rpms-command-input-wrap">
                <i class="bi bi-search"></i>
                <input id="rpms-command-input" class="rpms-command-input" type="text" placeholder="Search pages, projects, actions…" autocomplete="off">
                <kbd class="rpms-command-kbd">ESC</kbd>
            </div>
            <div id="rpms-command-results" class="rpms-command-results"></div>
            <div class="rpms-command-footer">
                <span><kbd class="rpms-command-kbd"><i class="bi bi-arrow-up"></i> <i class="bi bi-arrow-down"></i></kbd> Navigate</span>
                <span><kbd class="rpms-command-kbd">Enter</kbd> Open</span>
                <span><kbd class="rpms-command-kbd">ESC</kbd> Close</span>
            </div>
        </div>
    </div>

    <!-- RPMS Pro JS -->
    <script src="{{ asset('js/rpms-pro.js') }}"></script>
</body>
</html>
