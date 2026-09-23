<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Access Denied | RPMS</title>
    <link href="{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/bootstrap/css/bootstrap-icons.css') }}" rel="stylesheet">
    <style>
        :root {
            --UNI-primary: #0f3e2e;
            --UNI-primary-dark: #0a2b20;
            --UNI-gold: #d4af37;
            --UNI-bg: #f6f8fa;
        }
        body {
            background-color: var(--UNI-bg);
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            color: #1e293b;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .navbar-UNI {
            background: linear-gradient(135deg, var(--UNI-primary) 0%, var(--UNI-primary-dark) 100%);
            box-shadow: 0 4px 20px -2px rgba(15, 62, 46, 0.35);
            border-bottom: 2px solid var(--UNI-gold);
        }
        .brand-text { color: #ffffff; font-weight: 800; letter-spacing: 0.8px; font-size: 0.95rem; }
        .brand-sub { color: var(--UNI-gold); font-size: 0.72rem; font-weight: 500; }
        .error-card {
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            background: #ffffff;
            box-shadow: 0 2px 12px rgba(15, 62, 46, 0.04);
        }
        .error-icon {
            width: 80px; height: 80px;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            background: #fef2f2;
            border: 2px solid #fecaca;
        }
        .error-icon i { font-size: 2.5rem; color: #dc2626; }
        .btn-UNI {
            background-color: #0f172a; border-color: #0f172a; color: #ffffff;
        }
        .btn-UNI:hover {
            background-color: #334155; border-color: #334155; color: #ffffff;
        }
    </style>
</head>
<body class="d-flex flex-column min-vh-100">

    <nav class="navbar navbar-expand-lg navbar-UNI py-2">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="{{ route('dashboard') }}">
                <i class="bi bi-journal-bookmark-fill fs-2 text-warning me-2"></i>
                <div>
                    <div class="brand-text fs-6">Institution</div>
                    <div class="brand-sub">Research Project Management System</div>
                </div>
            </a>
        </div>
    </nav>

    <main class="container my-4 flex-grow-1 d-flex align-items-center justify-content-center">
        <div class="row justify-content-center w-100">
            <div class="col-md-6 col-lg-5">
                <div class="error-card p-5 text-center">
                    <div class="error-icon mx-auto mb-4">
                        <i class="bi bi-shield-lock-fill"></i>
                    </div>

                    <h1 class="fw-bold mb-2" style="color: #0f172a; font-size: 1.75rem;">Oops! Access Denied</h1>
                    <p class="text-muted mb-1" style="font-size: 3rem; font-weight: 800; color: #cbd5e1;">403</p>

                    <hr class="my-4">

                    <p class="text-muted mb-3" style="font-size: 0.95rem;">
                        It looks like you don't have permission to view this page.<br>
                        This might be a restricted area, or the link you followed may not be available to your account.
                    </p>

                    {{-- Search Box --}}
                    <form action="{{ route('dashboard') }}" method="GET" class="mb-4">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                            <input type="text" name="search" class="form-control" placeholder="Search projects, reports...">
                            <button type="submit" class="btn btn-UNI">Search</button>
                        </div>
                    </form>

                    <div class="d-flex gap-2 justify-content-center flex-wrap mb-3">
                        <a href="{{ route('dashboard') }}" class="btn btn-UNI px-3 py-2 fw-bold">
                            <i class="bi bi-house-door me-1"></i> Dashboard
                        </a>
                        @auth
                        <a href="{{ route('projects.index') }}" class="btn btn-outline-secondary px-3 py-2 fw-bold">
                            <i class="bi bi-folder me-1"></i> Projects
                        </a>
                        @endauth
                        <form action="{{ route('logout') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-outline-secondary px-3 py-2 fw-bold">
                                <i class="bi bi-box-arrow-right me-1"></i> Sign Out
                            </button>
                        </form>
                    </div>

                    {{-- Contact Info --}}
                    <div class="mt-3 p-3 bg-light rounded-3">
                        <small class="text-muted">
                            <i class="bi bi-envelope me-1"></i> Need help? Contact us at
                            <a href="mailto:admin@institution.org" class="fw-bold text-decoration-none">admin@institution.org</a>
                        </small>
                    </div>

                    @auth
                    <div class="mt-4 p-3 bg-light rounded-3">
                        <small class="text-muted">
                            <strong>Your role:</strong> {{ strtoupper(Auth::user()->role) }}<br>
                            <strong>Staff ID:</strong> {{ Auth::user()->staff_id ?? 'N/A' }}
                        </small>
                    </div>
                    @endauth
                </div>
            </div>
        </div>
    </main>

    <footer class="bg-white border-top py-3 text-center text-muted small mt-auto">
        <div class="container">
            &copy; {{ date('Y') }} Institution &mdash; Office of the Vice President for ARTTCS. SDD v3.0 Compliant.
        </div>
    </footer>

    <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
</body>
</html>
