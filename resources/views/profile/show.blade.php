@extends('layouts.app')

@section('styles')
<style>
    .avatar-upload-wrapper {
        position: relative;
        width: 120px;
        height: 120px;
        cursor: pointer;
    }
    .avatar-upload-wrapper:hover .avatar-overlay {
        opacity: 1;
    }
    .avatar-circle {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        object-fit: cover;
        border: 4px solid #e2e8f0;
        background: #f1f5f9;
    }
    .avatar-overlay {
        position: absolute;
        inset: 0;
        border-radius: 50%;
        background: rgba(15, 23, 42, 0.55);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.25s ease;
        color: #fff;
    }
    .avatar-overlay i { font-size: 1.6rem; }
    .avatar-overlay span { font-size: 0.65rem; font-weight: 600; margin-top: 2px; }

    .breadcrumb-sm { font-size: 0.82rem; }
    .breadcrumb-sm .breadcrumb-item + .breadcrumb-item::before { font-size: 0.72rem; }

    .role-badge {
        font-size: 0.7rem;
        padding: 3px 10px;
        border-radius: 6px;
        font-weight: 700;
        letter-spacing: 0.4px;
        text-transform: uppercase;
    }
    .role-pi       { background: #dbeafe; color: #1e40af; }
    .role-tm       { background: #e0e7ff; color: #3730a3; }
    .role-dh       { background: #fef3c7; color: #92400e; }
    .role-coordinator { background: #d1fae5; color: #065f46; }
    .role-reviewer { background: #ede9fe; color: #5b21b6; }
    .role-dean     { background: #fce7f3; color: #9d174d; }
    .role-irerc    { background: #fee2e2; color: #991b1b; }
    .role-vparttcs { background: #fef9c3; color: #854d0e; }
    .role-rcsc     { background: #ccfbf1; color: #0f766e; }
    .role-finance  { background: #ecfdf5; color: #065f46; }
    .role-admin    { background: #1e293b; color: #f8fafc; }

    .info-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 6px 14px;
        font-size: 0.8rem;
        color: #475569;
    }
    .info-chip i { font-size: 0.95rem; }

    .strength-bar {
        height: 6px;
        border-radius: 3px;
        background: #e2e8f0;
        overflow: hidden;
        transition: all 0.3s;
    }
    .strength-bar .fill {
        height: 100%;
        border-radius: 3px;
        transition: width 0.4s ease, background 0.4s ease;
    }

    .activity-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        flex-shrink: 0;
    }
    .activity-dot.action { background: #3b82f6; }
    .activity-dot.login  { background: #22c55e; }
    .activity-dot.default { background: #94a3b8; }

    .profile-hero {
        background: linear-gradient(135deg, var(--gmu-primary) 0%, var(--gmu-secondary) 100%);
        border-radius: 14px;
        padding: 2rem 2rem 1.5rem;
        color: #fff;
        margin-bottom: 1.5rem;
    }
</style>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">

        {{-- Breadcrumbs --}}
        <nav aria-label="breadcrumb" class="mb-3 breadcrumb-sm">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none"><i class="bi bi-house-door me-1"></i>Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">My Profile</li>
            </ol>
        </nav>

        {{-- Profile Hero Card --}}
        <div class="profile-hero">
            <div class="d-flex align-items-center gap-4 flex-wrap">
                {{-- Avatar Upload --}}
                <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" id="avatarForm" class="m-0">
                    @csrf
                    @method('PUT')
                    <label for="avatarInput" class="avatar-upload-wrapper mb-0" title="Change avatar">
                        @if($user->avatar_path)
                            <img src="{{ Storage::url($user->avatar_path) }}" alt="Avatar" class="avatar-circle">
                        @else
                            <div class="avatar-circle d-flex align-items-center justify-content-center">
                                <i class="bi bi-person-fill fs-1 text-secondary"></i>
                            </div>
                        @endif
                        <div class="avatar-overlay">
                            <i class="bi bi-camera-fill"></i>
                            <span>Change</span>
                        </div>
                    </label>
                    <input type="file" name="avatar" id="avatarInput" accept="image/*" class="d-none">
                </form>

                <div>
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <h3 class="fw-bold mb-0 text-white">{{ $user->name }}</h3>
                        <span class="role-badge role-{{ $user->role }}">{{ strtoupper($user->role) }}</span>
                    </div>
                    <div class="d-flex align-items-center gap-3 flex-wrap mt-2" style="opacity: 0.85;">
                        <span class="info-chip border-0 bg-white bg-opacity-10 text-white">
                            <i class="bi bi-envelope"></i> {{ $user->email }}
                        </span>
                        <span class="info-chip border-0 bg-white bg-opacity-10 text-white">
                            <i class="bi bi-person-badge"></i> {{ $user->staff_id ?? 'N/A' }}
                        </span>
                    </div>
                    <div class="d-flex align-items-center gap-3 flex-wrap mt-2" style="opacity: 0.75;">
                        @if($user->last_login_at)
                            <span class="info-chip border-0 bg-white bg-opacity-10 text-white" style="font-size:0.73rem;">
                                <i class="bi bi-clock-history"></i> Last Login: {{ $user->last_login_at->diffForHumans() }}
                            </span>
                        @endif
                        <span class="info-chip border-0 bg-white bg-opacity-10 text-white" style="font-size:0.73rem;">
                            <i class="bi bi-calendar-check"></i> Member Since: {{ $user->created_at->format('M d, Y') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Profile Info Card --}}
        <div class="card card-custom mb-4">
            <div class="card-header bg-white border-bottom fw-bold">
                <i class="bi bi-person-badge me-2 text-success"></i> Account Details
            </div>
            <div class="card-body">
                <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" id="profileForm">
                    @csrf
                    @method('PUT')
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">Full Name</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name', $user->name) }}" required>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">Email Address</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                   value="{{ old('email', $user->email) }}" required>
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-muted">Staff ID</label>
                            <input type="text" class="form-control" value="{{ $user->staff_id ?? 'N/A' }}" disabled>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-muted">Role</label>
                            <input type="text" class="form-control" value="{{ strtoupper($user->role) }}" disabled>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-muted">Status</label>
                            <input type="text" class="form-control"
                                   value="{{ ucfirst($user->status ?? 'Active') }}" disabled>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">Department</label>
                            <input type="text" class="form-control"
                                   value="{{ $user->department ? $user->department->name : 'N/A' }}" disabled>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">College</label>
                            <input type="text" class="form-control"
                                   value="{{ $user->department && $user->department->college ? $user->department->college->name : 'N/A' }}" disabled>
                        </div>
                    </div>
                    <div class="mt-3">
                        <button type="submit" class="btn btn-success fw-bold">
                            <i class="bi bi-check-lg me-1"></i> Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Change Password Card --}}
        <div class="card card-custom mb-4">
            <div class="card-header bg-white border-bottom fw-bold">
                <i class="bi bi-lock me-2 text-warning"></i> Change Password
            </div>
            <div class="card-body">
                <form action="{{ route('profile.change-password') }}" method="POST" id="passwordForm">
                    @csrf
                    @method('PUT')
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-bold small text-muted">Current Password</label>
                            <input type="password" name="current_password"
                                   class="form-control @error('current_password') is-invalid @enderror" required>
                            @error('current_password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">New Password</label>
                            <input type="password" name="new_password" id="newPasswordInput"
                                   class="form-control @error('new_password') is-invalid @enderror" required>
                            @error('new_password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            {{-- Password Strength Indicator --}}
                            <div class="mt-2">
                                <div class="strength-bar">
                                    <div class="fill" id="strengthFill" style="width:0%; background:#e2e8f0;"></div>
                                </div>
                                <small class="text-muted mt-1 d-block" id="strengthLabel" style="font-size:0.72rem;"></small>
                            </div>
                            <div class="form-text">Min 8 chars, upper, lower, number, and symbol required.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">Confirm New Password</label>
                            <input type="password" name="new_password_confirmation" class="form-control" required>
                        </div>
                    </div>
                    <div class="mt-3">
                        <button type="submit" class="btn btn-warning fw-bold text-dark">
                            <i class="bi bi-shield-lock me-1"></i> Update Password
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Activity History Card --}}
        <div class="card card-custom mb-4">
            <div class="card-header bg-white border-bottom fw-bold">
                <i class="bi bi-clock-history me-2 text-info"></i> Recent Activity
            </div>
            <div class="card-body p-0">
                @if($recentLogs->isEmpty())
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                        <span class="small">No recent activity recorded.</span>
                    </div>
                @else
                    <div class="list-group list-group-flush">
                        @foreach($recentLogs as $log)
                            <div class="list-group-item d-flex align-items-start gap-3 py-3">
                                <div class="activity-dot mt-1 {{ str_contains(strtolower($log->action), 'login') ? 'login' : (str_contains(strtolower($log->action), 'create') || str_contains(strtolower($log->action), 'update') || str_contains(strtolower($log->action), 'delete') ? 'action' : 'default') }}"></div>
                                <div class="flex-grow-1">
                                    <div class="fw-semibold text-dark small">{{ ucfirst($log->action) }}</div>
                                    <div class="text-muted" style="font-size:0.76rem;">
                                        @if($log->entity_type)
                                            <span class="badge bg-light text-dark border me-1" style="font-size:0.68rem;">{{ class_basename($log->entity_type) }}</span>
                                        @endif
                                        @if($log->details)
                                            {{ is_string($log->details) ? Str::limit($log->details, 80) : json_encode($log->details) }}
                                        @endif
                                    </div>
                                </div>
                                <div class="text-muted text-nowrap" style="font-size:0.7rem;">
                                    <i class="bi bi-clock me-1"></i>{{ $log->created_at ? $log->created_at->diffForHumans() : 'N/A' }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        {{-- Back to Dashboard --}}
        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back to Dashboard
        </a>

    </div>
</div>
@endsection

@section('scripts')
<script>
(function() {
    // Auto-upload avatar on file select
    var avatarInput = document.getElementById('avatarInput');
    if (avatarInput) {
        avatarInput.addEventListener('change', function() {
            if (this.files && this.files.length > 0) {
                var form = document.getElementById('avatarForm');
                form.submit();
            }
        });
    }

    // Password strength indicator
    var pwInput = document.getElementById('newPasswordInput');
    var fill = document.getElementById('strengthFill');
    var label = document.getElementById('strengthLabel');
    if (pwInput && fill && label) {
        pwInput.addEventListener('input', function() {
            var v = this.value;
            var score = 0;
            if (v.length >= 8) score++;
            if (v.length >= 12) score++;
            if (/[a-z]/.test(v) && /[A-Z]/.test(v)) score++;
            if (/\d/.test(v)) score++;
            if (/[^a-zA-Z0-9]/.test(v)) score++;

            var pct, color, text;
            if (v.length === 0) { pct = 0; color = '#e2e8f0'; text = ''; }
            else if (score <= 1) { pct = 20; color = '#ef4444'; text = 'Very Weak'; }
            else if (score === 2) { pct = 40; color = '#f97316'; text = 'Weak'; }
            else if (score === 3) { pct = 60; color = '#eab308'; text = 'Fair'; }
            else if (score === 4) { pct = 80; color = '#22c55e'; text = 'Strong'; }
            else { pct = 100; color = '#16a34a'; text = 'Very Strong'; }

            fill.style.width = pct + '%';
            fill.style.background = color;
            label.textContent = text;
            label.style.color = color;
        });
    }

    // Success toast notification
    @if(session('success'))
    (function() {
        var toast = document.createElement('div');
        toast.className = 'position-fixed top-0 end-0 m-3 p-3 pe-4 alert alert-success alert-dismissible fade show shadow-lg';
        toast.style.cssText = 'z-index:9999; min-width:300px; border-radius:10px; border-left:4px solid #16a34a;';
        toast.innerHTML = '<i class="bi bi-check-circle-fill me-2"></i><strong>{{ session("success") }}</strong>' +
            '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
        document.body.appendChild(toast);
        setTimeout(function() {
            if (toast.parentNode) {
                toast.classList.remove('show');
                setTimeout(function() { toast.remove(); }, 300);
            }
        }, 4000);
    })();
    @endif
})();
</script>
@endsection
