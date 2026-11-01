@extends('layouts.app')

@section('content')
<section class="login-frame" aria-labelledby="login-title">
    <div class="login-intro">
        <a href="{{ route('home') }}" class="login-brand text-decoration-none text-white" title="Return to Home">
            <span class="login-brand-mark"><i class="bi bi-journal-bookmark-fill"></i></span>
            <span>
                <strong>RPMS</strong>
                <small>Research Project Management System</small>
            </span>
        </a>

        <div class="login-intro-copy">
            <span class="login-eyebrow"><i class="bi bi-star me-2"></i>Research, made manageable</span>
            <h1>Ideas into impact.<br><span>Every step, connected.</span></h1>
            <p>One secure workspace for research proposals, reviews, funding, and project progress.</p>

            <div class="login-benefits">
                <div><i class="bi bi-check2-circle"></i><span>Follow projects from proposal to completion</span></div>
                <div><i class="bi bi-shield-check"></i><span>Keep reviews and approvals in one place</span></div>
                <div><i class="bi bi-graph-up"></i><span>See progress and funding at a glance</span></div>
            </div>
        </div>

        <div class="login-intro-footer">
            <i class="bi bi-lock-fill me-2"></i>Secure access for the research community
        </div>
    </div>

    <div class="login-panel">
        <div class="login-panel-inner">
            <span class="login-form-kicker">Welcome back</span>
            <h2 id="login-title">Sign in to your account</h2>
            <p class="login-form-description">Enter your university account details to continue.</p>

            @if($errors->any())
            <div class="alert alert-danger login-error" role="alert">
                <i class="bi bi-exclamation-triangle-fill"></i>
                <span>{{ $errors->first('email') ?: $errors->first() }}</span>
            </div>
            @endif

            @if(session('success'))
            <div class="alert alert-success" role="status">{{ session('success') }}</div>
            @endif

            <form method="POST" action="{{ route('login', [], false) }}" autocomplete="on" id="loginForm">
                @csrf

                <div class="mb-2">
                    <label class="form-label" for="email">Email address</label>
                    <div class="login-input-wrap">
                        <i class="bi bi-envelope"></i>
                        <input type="email" id="email" name="email"
                               class="form-control @error('email') is-invalid @enderror"
                               required autofocus placeholder="name@university.edu"
                               value="{{ old('email') }}" autocomplete="username">
                    </div>
                </div>

                <div class="mb-2">
                    <div class="d-flex justify-content-between align-items-center">
                        <label class="form-label mb-0" for="password">Password</label>
                        <span class="login-help">Need help? Contact administrator.</span>
                    </div>
                    <div class="login-input-wrap login-password-wrap mt-1">
                        <i class="bi bi-lock"></i>
                        <input type="password" id="password" name="password" class="form-control"
                               required placeholder="Enter your password" autocomplete="current-password">
                        <button class="password-toggle" type="button" id="togglePassword"
                                title="Show or hide password" aria-label="Show password">
                            <i class="bi bi-eye" id="passwordIcon"></i>
                        </button>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-2">
                    <label class="form-check login-remember mb-0" for="remember">
                        <input type="checkbox" class="form-check-input" id="remember" name="remember" {{ old('remember') ? 'checked' : '' }}>
                        <span class="form-check-label" style="font-size:0.78rem;">Remember me</span>
                    </label>
                    <span class="login-secure" style="font-size:0.72rem;"><i class="bi bi-shield-lock me-1"></i>Secure sign in</span>
                </div>

                <button type="submit" id="loginBtn" class="btn btn-success w-100 login-submit">
                    <span id="loginText">Sign in</span>
                    <span id="loginSpinner" class="d-none">
                        <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Signing in...
                    </span>
                    <i class="bi bi-arrow-right ms-auto" id="loginArrow"></i>
                </button>
            </form>

            @if(config('rpms.show_demo_accounts') || app()->environment('local'))
            <div class="login-demo mt-2 pt-2 border-top">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <span class="text-muted fw-bold" style="font-size: 0.68rem; text-transform: uppercase; letter-spacing: 0.04em;">
                        <i class="bi bi-lightning text-warning me-1"></i>Workflow test accounts:
                    </span>
                    <span class="text-muted" style="font-size: 0.65rem;">Pass: <code class="fw-bold px-1 rounded bg-light border text-dark">UNI@Demo1</code></span>
                </div>
                <div class="demo-buttons-grid">
                    <button type="button" class="demo-btn" data-email="pi@rpms.local" title="Principal Investigator (pi@rpms.local)">PI</button>
                    <button type="button" class="demo-btn" data-email="tm@rpms.local" title="Team Member (tm@rpms.local)">TM</button>
                    <button type="button" class="demo-btn" data-email="dh@rpms.local" title="Department Head (dh@rpms.local)">DH</button>
                    <button type="button" class="demo-btn" data-email="coordinator@rpms.local" title="Research Coordinator (coordinator@rpms.local)">Coord</button>
                    <button type="button" class="demo-btn" data-email="reviewer@rpms.local" title="Blind Reviewer 1 (reviewer@rpms.local)">Rev 1</button>
                    <button type="button" class="demo-btn" data-email="reviewer2@rpms.local" title="Reviewer 2 (reviewer2@rpms.local)">Rev 2</button>
                    <button type="button" class="demo-btn" data-email="dean@rpms.local" title="College Dean (dean@rpms.local)">Dean</button>
                    <button type="button" class="demo-btn" data-email="irerc@rpms.local" title="IRERC Ethics (irerc@rpms.local)">IRERC</button>
                    <button type="button" class="demo-btn" data-email="vp@rpms.local" title="Vice President (vp@rpms.local)">VP</button>
                    <button type="button" class="demo-btn" data-email="rcsc@rpms.local" title="RCSC Chair (rcsc@rpms.local)">RCSC</button>
                    <button type="button" class="demo-btn" data-email="finance@rpms.local" title="Finance Office (finance@rpms.local)">Finance</button>
                    <button type="button" class="demo-btn" data-email="admin@rpms.local" title="System Administrator (admin@rpms.local)">Admin</button>
                </div>
            </div>
            @endif

            <p class="login-legal">By signing in, you agree to use this system in accordance with university policies.</p>
        </div>
    </div>
</section>

<script>
    const passwordToggle = document.getElementById('togglePassword');
    passwordToggle.addEventListener('click', function () {
        const password = document.getElementById('password');
        const icon = document.getElementById('passwordIcon');
        const showing = password.type === 'password';

        password.type = showing ? 'text' : 'password';
        icon.classList.toggle('bi-eye', !showing);
        icon.classList.toggle('bi-eye-slash', showing);
        passwordToggle.setAttribute('aria-label', showing ? 'Hide password' : 'Show password');
    });

    document.getElementById('loginForm').addEventListener('submit', function () {
        document.getElementById('loginText').classList.add('d-none');
        document.getElementById('loginArrow').classList.add('d-none');
        document.getElementById('loginSpinner').classList.remove('d-none');
        document.getElementById('loginBtn').disabled = true;
    });

    // Reset button state when navigating back in browser
    window.addEventListener('pageshow', function () {
        const btn = document.getElementById('loginBtn');
        const text = document.getElementById('loginText');
        const arrow = document.getElementById('loginArrow');
        const spinner = document.getElementById('loginSpinner');
        if (btn) btn.disabled = false;
        if (text) text.classList.remove('d-none');
        if (arrow) arrow.classList.remove('d-none');
        if (spinner) spinner.classList.add('d-none');
    });

    // 1-Click Auto-Fill Demo Credentials via Small Buttons
    document.querySelectorAll('.demo-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            document.querySelectorAll('.demo-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');

            const email = this.getAttribute('data-email');
            const emailInput = document.getElementById('email');
            const passwordInput = document.getElementById('password');
            emailInput.value = email;
            passwordInput.value = 'UNI@Demo1';

            // Dispatch input and change events for form synchronization
            emailInput.dispatchEvent(new Event('input', { bubbles: true }));
            emailInput.dispatchEvent(new Event('change', { bubbles: true }));
            passwordInput.dispatchEvent(new Event('input', { bubbles: true }));
            passwordInput.dispatchEvent(new Event('change', { bubbles: true }));

            // Focus sign-in button instead of password to prevent browser credential popups and asymmetric highlight rings
            const loginBtn = document.getElementById('loginBtn');
            if (loginBtn) {
                loginBtn.focus();
            }
        });
    });
</script>
@endsection

@section('styles')
<style>
    .login-frame {
        display: grid;
        grid-template-columns: minmax(0, 1.05fr) minmax(390px, 0.95fr);
        width: min(980px, 100%);
        min-height: 520px;
        margin: auto;
        overflow: hidden;
        border: 1px solid #e2e9e5;
        border-radius: 1.25rem;
        background: #fff;
        box-shadow: 0 16px 50px rgba(20, 52, 42, 0.12);
    }
    .login-intro {
        position: relative;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        min-height: 100%;
        overflow: hidden;
        padding: clamp(1.5rem, 3.5vw, 2.5rem);
        color: #fff;
        background:
            radial-gradient(circle at 84% 14%, rgba(113, 201, 156, 0.22), transparent 15rem),
            linear-gradient(145deg, #174e3d 0%, #103b32 58%, #0c302b 100%);
    }
    .login-intro::after {
        position: absolute;
        right: -8rem;
        bottom: -11rem;
        width: 24rem;
        height: 24rem;
        border: 1px solid rgba(255, 255, 255, 0.12);
        border-radius: 50%;
        box-shadow: 0 0 0 30px rgba(255, 255, 255, 0.025), 0 0 0 65px rgba(255, 255, 255, 0.02);
        content: "";
        pointer-events: none;
    }
    .login-brand {
        z-index: 1;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    .login-brand-mark {
        display: grid;
        width: 2.5rem;
        height: 2.5rem;
        place-items: center;
        border: 1px solid rgba(255, 255, 255, 0.22);
        border-radius: 0.75rem;
        color: #f2d58e;
        background: rgba(255, 255, 255, 0.1);
        font-size: 1.15rem;
    }
    .login-brand strong,
    .login-brand small {
        display: block;
    }
    .login-brand strong {
        font-size: 1rem;
        letter-spacing: 0.08em;
    }
    .login-brand small {
        margin-top: 0.1rem;
        color: rgba(255, 255, 255, 0.68);
        font-size: 0.68rem;
    }
    .login-intro-copy {
        z-index: 1;
        max-width: 32rem;
        padding: 0.75rem 0;
    }
    .login-eyebrow,
    .login-form-kicker {
        display: inline-flex;
        align-items: center;
        color: #c8e5d4;
        font-size: 0.7rem;
        font-weight: 700;
        letter-spacing: 0.1em;
        text-transform: uppercase;
    }
    .login-intro h1 {
        margin: 0.65rem 0 0.4rem;
        color: #fff;
        font-size: clamp(1.65rem, 2.8vw, 2.2rem);
        font-weight: 750;
        letter-spacing: -0.04em;
        line-height: 1.15;
    }
    .login-intro h1 span {
        color: #c8e5d4;
    }
    .login-intro-copy > p {
        max-width: 26rem;
        color: rgba(255, 255, 255, 0.72);
        font-size: 0.86rem;
        line-height: 1.5;
        margin-bottom: 0;
    }
    .login-benefits {
        display: grid;
        gap: 0.5rem;
        margin-top: 1rem;
    }
    .login-benefits > div {
        display: flex;
        align-items: center;
        gap: 0.65rem;
        color: rgba(255, 255, 255, 0.88);
        font-size: 0.82rem;
    }
    .login-benefits i {
        color: #c8e5d4;
        font-size: 1rem;
    }
    .login-intro-footer {
        z-index: 1;
        color: rgba(255, 255, 255, 0.63);
        font-size: 0.74rem;
    }
    .login-panel {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: clamp(1.25rem, 2.5vw, 1.85rem) clamp(1.5rem, 3.5vw, 2.5rem);
        background: #fff;
    }
    .login-panel-inner {
        width: 100%;
        max-width: 410px;
    }
    .login-form-kicker {
        color: #398365;
        letter-spacing: 0.09em;
    }
    .login-panel h2 {
        margin: 0.25rem 0 0.15rem;
        color: #19372e;
        font-size: 1.45rem;
        font-weight: 750;
        letter-spacing: -0.03em;
    }
    .login-form-description {
        margin-bottom: 0.75rem;
        color: #76837d;
        font-size: 0.82rem;
    }
    .login-panel .form-label {
        margin-bottom: 0.2rem;
        color: #344c42;
        font-size: 0.78rem;
        font-weight: 700;
    }
    .login-input-wrap {
        position: relative;
    }
    .login-input-wrap > i {
        position: absolute;
        z-index: 2;
        top: 50%;
        left: 0.85rem;
        color: #8a9b92;
        transform: translateY(-50%);
        font-size: 0.85rem;
        pointer-events: none;
    }
    .login-input-wrap .form-control {
        min-height: 40px;
        padding-left: 2.3rem;
        border-color: #dfe7e3;
        border-radius: 0.65rem;
        background: #fbfcfb;
        font-size: 0.85rem;
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out, background-color 0.15s ease-in-out;
    }
    .login-input-wrap .form-control:focus {
        background: #fff;
        border-color: #17624b;
        box-shadow: 0 0 0 0.2rem rgba(23, 98, 75, 0.15);
    }
    .login-input-wrap input:-webkit-autofill,
    .login-input-wrap input:-webkit-autofill:hover,
    .login-input-wrap input:-webkit-autofill:focus,
    .login-input-wrap input:-webkit-autofill:active {
        -webkit-box-shadow: 0 0 0 40px #fbfcfb inset !important;
        -webkit-text-fill-color: #19372e !important;
        transition: background-color 5000s ease-in-out 0s;
    }
    .login-help {
        max-width: 12rem;
        color: #829088;
        font-size: 0.68rem;
        text-align: right;
    }
    .login-password-wrap .form-control {
        padding-right: 2.7rem;
    }
    .password-toggle {
        position: absolute;
        z-index: 3;
        top: 50%;
        right: 0.7rem;
        padding: 0.25rem;
        border: 0;
        color: #74847c;
        background: transparent;
        transform: translateY(-50%);
        font-size: 0.85rem;
    }
    .login-remember {
        display: flex;
        align-items: center;
        gap: 0.45rem;
        color: #52635a;
        font-size: 0.78rem;
    }
    .login-remember .form-check-input {
        float: none;
        margin: 0;
        width: 0.9rem;
        height: 0.9rem;
    }
    .login-secure {
        color: #75857c;
        font-size: 0.72rem;
    }
    .login-submit {
        display: flex;
        min-height: 40px;
        align-items: center;
        padding: 0.45rem 1rem;
        border: 0 !important;
        border-radius: 0.65rem;
        background: #17624b !important;
        font-size: 0.88rem;
        box-shadow: 0 6px 14px rgba(23, 98, 75, 0.18);
    }
    .login-submit:hover {
        background: #104b3a !important;
        box-shadow: 0 8px 18px rgba(23, 98, 75, 0.24);
    }
    .login-submit:disabled {
        opacity: 0.75;
    }
    .login-error {
        display: flex;
        align-items: flex-start;
        gap: 0.5rem;
        font-size: 0.78rem;
        padding: 0.5rem 0.75rem;
        margin-bottom: 0.65rem;
    }
    .login-demo {
        margin-top: 0.65rem;
    }
    .demo-buttons-grid {
        display: grid;
        grid-template-columns: repeat(6, 1fr);
        gap: 3px;
    }
    .demo-btn {
        padding: 4px 2px;
        min-height: 28px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.7rem;
        font-weight: 600;
        color: #265545;
        background: #f1f6f3;
        border: 1px solid #d4e3dc;
        border-radius: 5px;
        cursor: pointer;
        text-align: center;
        white-space: nowrap;
        transition: all 0.12s ease-in-out;
    }
    .demo-btn:hover {
        background: #17624b;
        color: #ffffff;
        border-color: #17624b;
        transform: translateY(-1px);
    }
    .demo-btn.active {
        background: #17624b;
        color: #ffffff;
        border-color: #17624b;
        box-shadow: 0 0 0 2px rgba(23, 98, 75, 0.25);
    }
    .login-legal {
        margin: 0.65rem 0 0;
        color: #98a49e;
        font-size: 0.65rem;
        line-height: 1.4;
        text-align: center;
    }
    @media (max-width: 880px) {
        .login-frame {
            grid-template-columns: 1fr;
            width: min(490px, 100%);
            min-height: auto;
            margin: 0.5rem auto;
            border-radius: 1.15rem;
        }
        .login-intro {
            min-height: auto;
            padding: 1.25rem 1.25rem;
        }
        .login-intro-copy {
            padding: 0.5rem 0 0.25rem;
        }
        .login-intro h1 {
            margin: 0.4rem 0 0.3rem;
            font-size: 1.45rem;
        }
        .login-benefits {
            display: grid;
            gap: 0.45rem;
            margin-top: 0.65rem;
        }
        .login-benefits > div {
            font-size: 0.8rem;
        }
        .login-intro-footer {
            display: none;
        }
        .login-panel {
            padding: 1.25rem 1.25rem;
        }
    }
    @media (max-width: 575.98px) {
        .login-frame {
            width: 100%;
            margin: 0 auto;
            border-radius: 1rem;
            box-shadow: 0 4px 20px rgba(20, 52, 42, 0.08);
        }
        .login-intro {
            padding: 0.85rem 1rem;
            background: linear-gradient(145deg, #174e3d 0%, #103b32 100%);
        }
        .login-brand-mark {
            width: 2.1rem;
            height: 2.1rem;
            font-size: 0.95rem;
        }
        .login-intro-copy {
            display: none;
        }
        .login-panel {
            padding: 1.15rem 1rem 1.25rem;
        }
        .login-panel h2 {
            font-size: 1.3rem;
        }
        .login-form-description {
            font-size: 0.78rem;
            margin-bottom: 0.65rem;
        }
        .demo-buttons-grid {
            grid-template-columns: repeat(4, 1fr);
            gap: 4px;
        }
    }
    @media (max-width: 360px) {
        .demo-buttons-grid {
            grid-template-columns: repeat(3, 1fr);
            gap: 3px;
        }
        .login-help {
            max-width: 8.5rem;
            font-size: 0.62rem;
        }
    }
</style>
@endsection
