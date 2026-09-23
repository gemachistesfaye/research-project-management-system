@extends('layouts.app')

@section('content')
<div class="row justify-content-center my-4">
    <div class="col-md-5">
        <div class="card card-custom p-4">
            <div class="text-center mb-4">
                <i class="bi bi-key-fill fs-1 text-warning"></i>
                <h4 class="fw-bold mt-2">Reset Your Password</h4>
                <p class="text-muted small">Both your <strong>Staff ID</strong> and <strong>Email</strong> must match your account.</p>
            </div>

            {{-- Security note --}}
            <div class="alert alert-info small py-2 mb-3">
                <i class="bi bi-shield-lock me-1"></i>
                <strong>Two-factor identity check:</strong> You must provide your Staff ID <em>and</em> registered email before a new password can be set.
            </div>

            <form method="POST" action="{{ route('login') }}">
                @csrf

                {{-- Staff ID --}}
                <div class="mb-3">
                    <label class="form-label fw-bold">Staff ID</label>
                    <input type="text"
                           name="staff_id"
                           class="form-control @error('staff_id') is-invalid @enderror"
                           value="{{ old('staff_id') }}"
                           required
                           placeholder="e.g. UNI/STAFF/001"
                           autofocus>
                    @error('staff_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Email --}}
                <div class="mb-3">
                    <label class="form-label fw-bold">Registered Email Address</label>
                    <input type="email"
                           name="email"
                           class="form-control @error('email') is-invalid @enderror"
                           value="{{ old('email') }}"
                           required
                           placeholder="user@university.edu">
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <hr>

                {{-- New password --}}
                <div class="mb-3">
                    <label class="form-label fw-bold">New Password</label>
                    <input type="password"
                           name="new_password"
                           class="form-control @error('new_password') is-invalid @enderror"
                           required
                           placeholder="Minimum 8 characters">
                    @error('new_password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    {{-- Password requirements list --}}
                    <ul class="mt-2 mb-0 small text-muted ps-3">
                        <li>At least <strong>8 characters</strong></li>
                        <li>At least one <strong>uppercase</strong> letter (A–Z)</li>
                        <li>At least one <strong>lowercase</strong> letter (a–z)</li>
                        <li>At least one <strong>number</strong> (0–9)</li>
                        <li>At least one <strong>symbol</strong> (@, $, !, %, *, ?, &amp;, # …)</li>
                    </ul>
                </div>

                {{-- Confirm password --}}
                <div class="mb-4">
                    <label class="form-label fw-bold">Confirm New Password</label>
                    <input type="password"
                           name="new_password_confirmation"
                           class="form-control"
                           required
                           placeholder="Re-enter new password">
                </div>

                <button type="submit" class="btn btn-warning w-100 py-2 fw-bold text-dark">
                    <i class="bi bi-shield-check me-1"></i> Verify Identity &amp; Reset Password
                </button>
            </form>

            <div class="text-center mt-3">
                <a href="{{ route('login') }}" class="text-decoration-none small text-muted">
                    <i class="bi bi-arrow-left me-1"></i>Back to Sign In
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
