<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Page Not Found | RPMS</title>
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
            background: #fff7ed;
            border: 2px solid #fed7aa;
        }
        .error-icon i { font-size: 2.5rem; color: #ea580c; }
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
                        <i class="bi bi-question-circle-fill"></i>
                    </div>

                    <h1 class="fw-bold mb-2" style="color: #0f172a; font-size: 1.75rem;">Page Not Found</h1>
                    <p class="text-muted mb-1" style="font-size: 3rem; font-weight: 800; color: #cbd5e1;">404</p>

                    <hr class="my-4">

                    <p class="text-muted mb-3" style="font-size: 0.95rem;">
                        Sorry, we couldn't find the page you're looking for.<br>
                        It may have been moved, renamed, or doesn't exist yet.
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
                        <a href="{{ url()->previous() }}" class="btn btn-outline-secondary px-3 py-2 fw-bold">
                            <i class="bi bi-arrow-left me-1"></i> Go Back
                        </a>
                    </div>

                    {{-- Helpful Links --}}
                    <div class="mt-3 p-3 bg-light rounded-3 text-start">
                        <small class="text-muted fw-bold d-block mb-2"><i class="bi bi-link-45deg me-1"></i> Helpful Links</small>
                        <div class="row g-2">
                            <div class="col-6">
                                <a href="{{ route('dashboard') }}" class="text-decoration-none small"><i class="bi bi-speedometer2 me-1"></i> Dashboard</a>
                            </div>
                            @auth
                            <div class="col-6">
                                <a href="{{ route('projects.index') }}" class="text-decoration-none small"><i class="bi bi-folder me-1"></i> My Projects</a>
                            </div>
                            @endauth
                            <div class="col-6">
                                <a href="mailto:admin@institution.org" class="text-decoration-none small"><i class="bi bi-envelope me-1"></i> Contact Support</a>
                            </div>
                        </div>
                    </div>
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
