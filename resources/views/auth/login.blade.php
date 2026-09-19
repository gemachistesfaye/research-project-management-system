@extends('layouts.app')

@section('navbar-class', 'justify-content-center')
@section('navbar-container-class', ' justify-content-center')

@section('content')
<div class="row justify-content-center my-4">
    <div class="col-md-5">
        <div class="card card-custom p-4">
            <div class="text-center mb-4">
                <i class="bi bi-shield-lock-fill fs-1 text-success"></i>
                <h4 class="fw-bold mt-2">Sign In to GMU-RPMS</h4>
                <p class="text-muted small">Academic Research & Governance Portal</p>
            </div>

            <form method="POST" action="{{ route('login') }}" autocomplete="off">
                @csrf

                @if($errors->any())
                <div class="alert alert-danger py-2 mb-3">
                    <i class="bi bi-exclamation-triangle-fill me-1"></i>
                    {{ $errors->first('email') }}
                </div>
                @endif

                <div class="mb-3">
                    <label class="form-label fw-bold">Email Address</label>
                    <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror"
                           required autofocus placeholder="user@gmu.edu.et"
                           value="{{ old('email') }}"
                           autocomplete="username">
                </div>

                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <label class="form-label fw-bold mb-0">Password</label>
                        <span class="small text-muted">
                            <i class="bi bi-person-lock me-1"></i>Forgot password? Contact the <strong>System Administrator</strong>.
                        </span>
                    </div>
                    <div class="input-group mt-1">
                        <input type="password" id="password" name="password" class="form-control"
                               required placeholder="••••••••"
                               autocomplete="current-password">
                        <button class="btn btn-outline-secondary border" type="button" id="togglePassword" title="Show/Hide password">
                            <i class="bi bi-eye" id="passwordIcon"></i>
                        </button>
                    </div>
                </div>

                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="remember" name="remember" {{ old('remember') ? 'checked' : '' }}>
                    <label class="form-check-label" for="remember">Remember Me</label>
                </div>

                <button type="submit" id="loginBtn" class="btn btn-success w-100 py-2 fw-bold">
                    <span id="loginText">Sign In</span>
                    <span id="loginSpinner" class="d-none">
                        <span class="spinner-border spinner-border-sm me-1" role="status"></span>Signing in...
                    </span>
                </button>
            </form>

            <div class="text-center mt-3">
                <form action="{{ route('login') }}" method="POST">
                    @csrf
                    <input type="hidden" name="email" value="admin@gmu.edu.et">
                    <input type="hidden" name="password" value="GMU@Demo1">
                    <button type="submit" class="btn btn-outline-dark btn-sm fw-bold">
                        <i class="bi bi-gear me-1"></i> Login as Admin
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('togglePassword').addEventListener('click', function () {
        const pwd = document.getElementById('password');
        const icon = document.getElementById('passwordIcon');
        if (pwd.type === 'password') {
            pwd.type = 'text';
            icon.classList.replace('bi-eye', 'bi-eye-slash');
        } else {
            pwd.type = 'password';
            icon.classList.replace('bi-eye-slash', 'bi-eye');
        }
    });

    document.querySelector('form').addEventListener('submit', function () {
        document.getElementById('loginText').classList.add('d-none');
        document.getElementById('loginSpinner').classList.remove('d-none');
        document.getElementById('loginBtn').disabled = true;
    });
</script>
@endsection
