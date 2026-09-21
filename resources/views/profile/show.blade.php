@extends('layouts.app')

@section('styles')
<style>
    .avatar-upload-wrapper {
        position: relative;
        width: 120px;
        height: 120px;
        width: 110px;
        height: 110px;
        cursor: pointer;
        flex-shrink: 0;
    }
    .avatar-upload-wrapper:hover .avatar-overlay {
        opacity: 1;
    }
    .avatar-circle {
        width: 120px;
        height: 120px;
        width: 110px;
        height: 110px;
        border-radius: 50%;
        object-fit: cover;
        border: 4px solid #e2e8f0;
        border: 4px solid rgba(255, 255, 255, 0.4);
        background: #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .avatar-initials {
        width: 110px;
        height: 110px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.2);
        border: 3px solid rgba(255, 255, 255, 0.5);
        color: #ffffff;
        font-size: 2.2rem;
        font-weight: 800;
        display: flex;
        align-items: center;
        justify-content: center;
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    .avatar-overlay {
        position: absolute;
        inset: 0;
        border-radius: 50%;
        background: rgba(15, 23, 42, 0.55);
        background: rgba(15, 23, 42, 0.65);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.25s ease;
        color: #fff;
    }
    .avatar-overlay i { font-size: 1.6rem; }
    .avatar-overlay i { font-size: 1.5rem; }
    .avatar-overlay span { font-size: 0.65rem; font-weight: 600; margin-top: 2px; }

    .breadcrumb-sm { font-size: 0.82rem; }
    .breadcrumb-sm .breadcrumb-item + .breadcrumb-item::before { font-size: 0.72rem; }

    .role-badge {
        font-size: 0.7rem;
        padding: 3px 10px;
        font-size: 0.72rem;
        padding: 4px 10px;
        border-radius: 6px;
        font-weight: 700;
        letter-spacing: 0.4px;
        text-transform: uppercase;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }
    .role-pi       { background: #dbeafe; color: #1e40af; }
    .role-tm       { background: #e0e7ff; color: #3730a3; }
    .role-dh       { background: #fef3c7; color: #92400e; }
    .role-pi          { background: #dbeafe; color: #1e40af; }
    .role-tm          { background: #e0e7ff; color: #3730a3; }
    .role-dh          { background: #fef3c7; color: #92400e; }
    .role-coordinator { background: #d1fae5; color: #065f46; }
    .role-reviewer { background: #ede9fe; color: #5b21b6; }
    .role-dean     { background: #fce7f3; color: #9d174d; }
    .role-irerc    { background: #fee2e2; color: #991b1b; }
    .role-vparttcs { background: #fef9c3; color: #854d0e; }
    .role-rcsc     { background: #ccfbf1; color: #0f766e; }
    .role-finance  { background: #ecfdf5; color: #065f46; }
    .role-admin    { background: #1e293b; color: #f8fafc; }
    .role-reviewer    { background: #ede9fe; color: #5b21b6; }
    .role-dean        { background: #fce7f3; color: #9d174d; }
    .role-irerc       { background: #fee2e2; color: #991b1b; }
    .role-vparttcs    { background: #fef9c3; color: #854d0e; }
    .role-rcsc        { background: #ccfbf1; color: #0f766e; }
    .role-finance     { background: #ecfdf5; color: #065f46; }
    .role-admin       { background: #1e293b; color: #f8fafc; }

    .info-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        background: rgba(255, 255, 255, 0.12);
        border: 1px solid rgba(255, 255, 255, 0.25);
        border-radius: 8px;
        padding: 5px 12px;
        font-size: 0.78rem;
        color: #ffffff;
    }
    .info-chip i { font-size: 0.9rem; }

    .stat-pill {
        background: rgba(0, 0, 0, 0.2);
        border: 1px solid rgba(255, 255, 255, 0.15);
        border-radius: 8px;
        padding: 6px 14px;
        font-size: 0.8rem;
        text-align: center;
        min-width: 90px;
    }

    .nav-pills-custom .nav-link {
        color: #475569;
        font-weight: 600;
        font-size: 0.9rem;
        padding: 0.65rem 1.25rem;
        border-radius: 8px;
        transition: all 0.2s ease;
    }
    .info-chip i { font-size: 0.95rem; }
    .nav-pills-custom .nav-link:hover {
        background: #f1f5f9;
        color: #0f3e2e;
    }
    .nav-pills-custom .nav-link.active {
        background: #0f3e2e;
        color: #ffffff;
        box-shadow: 0 4px 10px rgba(15, 62, 46, 0.25);
    }

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
        width: 10px;
        height: 10px;
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
        padding: 1.75rem 1.75rem 1.5rem;
        color: #fff;
        margin-bottom: 1.5rem;
        box-shadow: 0 4px 16px rgba(15, 62, 46, 0.15);
    }
</style>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
    <div class="col-lg-9">

        {{-- Breadcrumbs --}}
        <nav aria-label="breadcrumb" class="mb-3 breadcrumb-sm">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none"><i class="bi bi-house-door me-1"></i>Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">My Profile</li>
            </ol>
        </nav>
        <div class="d-flex justify-content-between align-items-center mb-3">
            <nav aria-label="breadcrumb" class="breadcrumb-sm m-0">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none"><i class="bi bi-house-door me-1"></i>Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Account Profile</li>
                </ol>
            </nav>
            <a href="{{ route('dashboard') }}" class="btn btn-sm btn-outline-dark fw-bold">
                <i class="bi bi-arrow-left me-1"></i> Back to Dashboard
            </a>
        </div>

        {{-- Profile Hero Card --}}
        <div class="profile-hero">
            <div class="d-flex align-items-center gap-4 flex-wrap">
                {{-- Avatar Upload --}}
                <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" id="avatarForm" class="m-0">
                    @csrf
                    @method('PUT')
                    <label for="avatarInput" class="avatar-upload-wrapper mb-0" title="Change avatar">
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-4">
                
                {{-- Left: Avatar + Identity --}}
                <div class="d-flex align-items-center gap-3 gap-md-4">
                    {{-- Avatar Upload Form --}}
                    <div class="d-flex flex-column align-items-center">
                        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" id="avatarForm" class="m-0">
                            @csrf
                            @method('PUT')
                            <label for="avatarInput" class="avatar-upload-wrapper mb-0" title="Click to upload photo">
                                @if($user->avatar_path)
                                    <img src="{{ Storage::url($user->avatar_path) }}" alt="Avatar" class="avatar-circle">
                                @else
                                    @php
                                        $initials = collect(explode(' ', preg_replace('/\s*\([^)]*\)/', '', $user->name)))->map(fn($part) => substr($part, 0, 1))->take(2)->join('');
                                    @endphp
                                    <div class="avatar-initials">
                                        {{ $initials ?: 'GMU' }}
                                    </div>
                                @endif
                                <div class="avatar-overlay">
                                    <i class="bi bi-camera-fill"></i>
                                    <span>Upload</span>
                                </div>
                            </label>
                            <input type="file" name="avatar" id="avatarInput" accept="image/*" class="d-none">
                        </form>
                        
                        @if($user->avatar_path)
                            <img src="{{ Storage::url($user->avatar_path) }}" alt="Avatar" class="avatar-circle">
                        @else
                            <div class="avatar-circle d-flex align-items-center justify-content-center">
                                <i class="bi bi-person-fill fs-1 text-secondary"></i>
                            </div>
                        <form action="{{ route('profile.delete-avatar') }}" method="POST" class="mt-2 m-0">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-link text-white text-opacity-75 p-0 text-decoration-none" style="font-size: 0.7rem;" title="Remove Photo">
                                <i class="bi bi-trash me-1"></i> Remove
                            </button>
                        </form>
                        @endif
                        <div class="avatar-overlay">
                            <i class="bi bi-camera-fill"></i>
                            <span>Change</span>
                    </div>

                    {{-- Name, Role, Metadata --}}
                    <div>
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <h3 class="fw-bold mb-0 text-white">{{ preg_replace('/\s*\([^)]*\)/', '', $user->name) }}</h3>
                            <span class="role-badge role-{{ $user->role }}">{{ strtoupper($user->role) }}</span>
                        </div>
                    </label>
                    <input type="file" name="avatar" id="avatarInput" accept="image/*" class="d-none">
                </form>
                        
                        <div class="d-flex align-items-center gap-2 flex-wrap mt-2">
                            <span class="info-chip">
                                <i class="bi bi-envelope"></i> {{ $user->email }}
                            </span>
                            <span class="info-chip">
                                <i class="bi bi-person-badge"></i> {{ $user->staff_id ?? 'N/A' }}
                            </span>
                        </div>

                <div>
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <h3 class="fw-bold mb-0 text-white">{{ $user->name }}</h3>
                        <span class="role-badge role-{{ $user->role }}">{{ strtoupper($user->role) }}</span>
                        <div class="d-flex align-items-center gap-3 flex-wrap mt-2 text-white text-opacity-75" style="font-size: 0.74rem;">
                            @if($user->department)
                                <span><i class="bi bi-building me-1"></i>{{ $user->department->name }}</span>
                            @endif
                            @if($user->last_login_at)
                                <span><i class="bi bi-clock-history me-1"></i>Active {{ $user->last_login_at->diffForHumans() }}</span>
                            @endif
                            <span><i class="bi bi-calendar-check me-1"></i>Member since {{ $user->created_at->format('M Y') }}</span>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-3 flex-wrap mt-2" style="opacity: 0.85;">
                        <span class="info-chip border-0 bg-white bg-opacity-10 text-white">
                            <i class="bi bi-envelope"></i> {{ $user->email }}
                        </span>
                        <span class="info-chip border-0 bg-white bg-opacity-10 text-white">
                            <i class="bi bi-person-badge"></i> {{ $user->staff_id ?? 'N/A' }}
                        </span>
                </div>

                {{-- Right: Mini Statistics Pills --}}
                @if(!empty($stats))
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    @foreach($stats as $st)
                    <div class="stat-pill flex-grow-1 flex-md-grow-0">
                        <div class="text-white text-opacity-75 small text-uppercase" style="font-size: 0.65rem;">
                            <i class="bi {{ $st['icon'] }} me-1"></i>{{ $st['label'] }}
                        </div>
                        <div class="fs-5 fw-bold text-white mt-1">{{ $st['val'] }}</div>
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
                    @endforeach
                </div>
                @endif

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
        {{-- Tabbed Navigation for Clean Zero-Scroll Layout --}}
        <ul class="nav nav-pills nav-pills-custom gap-2 mb-4 bg-white p-2 rounded-3 shadow-sm" id="profileTabs" role="tablist">
            <li class="nav-item flex-grow-1 flex-md-grow-0" role="presentation">
                <button class="nav-link active w-100 text-center" id="personal-tab" data-bs-toggle="pill" data-bs-target="#personalPane" type="button" role="tab">
                    <i class="bi bi-person-lines-fill me-1"></i> Personal &amp; Institutional Info
                </button>
            </li>
            <li class="nav-item flex-grow-1 flex-md-grow-0" role="presentation">
                <button class="nav-link w-100 text-center" id="security-tab" data-bs-toggle="pill" data-bs-target="#securityPane" type="button" role="tab">
                    <i class="bi bi-shield-lock me-1"></i> Security &amp; Password
                </button>
            </li>
            <li class="nav-item flex-grow-1 flex-md-grow-0" role="presentation">
                <button class="nav-link w-100 text-center" id="activity-tab" data-bs-toggle="pill" data-bs-target="#activityPane" type="button" role="tab">
                    <i class="bi bi-clock-history me-1"></i> Activity Audit Log
                </button>
            </li>
        </ul>

        {{-- Tab Panes --}}
        <div class="tab-content" id="profileTabsContent">

            {{-- Tab 1: Personal Info --}}
            <div class="tab-pane fade show active" id="personalPane" role="tabpanel">
                <div class="card card-custom p-3 p-md-4">
                    <h5 class="fw-bold mb-3 text-dark"><i class="bi bi-person-badge me-2 text-success"></i>Account Details</h5>
                    
                    <form action="{{ route('profile.update') }}" method="POST" id="profileForm">
                        @csrf
                        @method('PUT')
                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-bold small text-muted">Full Name</label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                       value="{{ old('name', $user->name) }}" required>
                                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-bold small text-muted">Official Email Address</label>
                                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                       value="{{ old('email', $user->email) }}" required>
                                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-6 col-md-4">
                                <label class="form-label fw-bold small text-muted">Staff ID</label>
                                <input type="text" class="form-control font-monospace bg-light" value="{{ $user->staff_id ?? 'N/A' }}" disabled>
                            </div>
                            <div class="col-6 col-md-4">
                                <label class="form-label fw-bold small text-muted">System Role</label>
                                <input type="text" class="form-control bg-light fw-bold" value="{{ strtoupper($user->role) }}" disabled>
                            </div>
                            <div class="col-12 col-md-4">
                                <label class="form-label fw-bold small text-muted">Account Status</label>
                                <input type="text" class="form-control bg-light"
                                       value="{{ ucfirst($user->status ?? 'Active') }}" disabled>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-bold small text-muted">Assigned Department</label>
                                <input type="text" class="form-control bg-light"
                                       value="{{ $user->department ? $user->department->name : 'Central Administration' }}" disabled>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-bold small text-muted">Assigned College</label>
                                <input type="text" class="form-control bg-light"
                                       value="{{ $user->department && $user->department->college ? $user->department->college->name : 'N/A' }}" disabled>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">Email Address</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                   value="{{ old('email', $user->email) }}" required>
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror

                        <div class="d-flex justify-content-between align-items-center mt-4 pt-2 border-top">
                            <small class="text-muted"><i class="bi bi-info-circle me-1"></i>Institutional details are synchronized with GMU HRMS.</small>
                            <button type="submit" class="btn btn-dark fw-bold px-4">
                                <i class="bi bi-check-lg me-1"></i> Save Changes
                            </button>
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
                        <button type="submit" class="btn btn-dark fw-bold">
                            <i class="bi bi-check-lg me-1"></i> Save Changes
                        </button>
                    </div>
                </form>
                    </form>
                </div>
            </div>
        </div>

        {{-- Change Password Card --}}
        <div class="card card-custom mb-4">
            <div class="card-header bg-white border-bottom fw-bold">
                <i class="bi bi-lock me-2 text-dark"></i> Change Password
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
            {{-- Tab 2: Security & Password --}}
            <div class="tab-pane fade" id="securityPane" role="tabpanel">
                <div class="card card-custom p-3 p-md-4">
                    <h5 class="fw-bold mb-3 text-dark"><i class="bi bi-lock me-2 text-dark"></i>Change Account Password</h5>
                    
                    <form action="{{ route('profile.change-password') }}" method="POST" id="passwordForm">
                        @csrf
                        @method('PUT')
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-bold small text-muted">Current Password</label>
                                <div class="input-group">
                                    <input type="password" name="current_password" id="current_password"
                                           class="form-control @error('current_password') is-invalid @enderror" placeholder="Enter current password" required>
                                    <button class="btn btn-outline-secondary toggle-password" type="button" data-target="current_password">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    @error('current_password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <small class="text-muted mt-1 d-block" id="strengthLabel" style="font-size:0.72rem;"></small>
                            </div>
                            <div class="form-text">Min 8 chars, upper, lower, number, and symbol required.</div>
                            
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-bold small text-muted">New Password</label>
                                <div class="input-group">
                                    <input type="password" name="new_password" id="newPasswordInput"
                                           class="form-control @error('new_password') is-invalid @enderror" placeholder="Enter new password" required>
                                    <button class="btn btn-outline-secondary toggle-password" type="button" data-target="newPasswordInput">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    @error('new_password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                {{-- Password Strength Indicator --}}
                                <div class="mt-2">
                                    <div class="strength-bar">
                                        <div class="fill" id="strengthFill" style="width:0%; background:#e2e8f0;"></div>
                                    </div>
                                    <small class="text-muted mt-1 d-block" id="strengthLabel" style="font-size:0.72rem;"></small>
                                </div>
                                <div class="form-text small" style="font-size: 0.75rem;">Requires min 8 chars, uppercase, lowercase, numbers, &amp; symbol.</div>
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label fw-bold small text-muted">Confirm New Password</label>
                                <div class="input-group">
                                    <input type="password" name="new_password_confirmation" id="new_password_confirmation" class="form-control" placeholder="Re-type new password" required>
                                    <button class="btn btn-outline-secondary toggle-password" type="button" data-target="new_password_confirmation">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">Confirm New Password</label>
                            <input type="password" name="new_password_confirmation" class="form-control" required>

                        <div class="d-flex justify-content-end mt-4 pt-2 border-top">
                            <button type="submit" class="btn btn-dark fw-bold px-4">
                                <i class="bi bi-shield-lock me-1"></i> Update Password
                            </button>
                        </div>
                    </div>
                    <div class="mt-3">
                        <button type="submit" class="btn btn-dark fw-bold">
                            <i class="bi bi-shield-lock me-1"></i> Update Password
                        </button>
                    </div>
                </form>
                    </form>
                </div>
            </div>
        </div>

        {{-- Activity History Card --}}
        <div class="card card-custom mb-4">
            <div class="card-header bg-white border-bottom fw-bold">
                <i class="bi bi-clock-history me-2 text-dark"></i> Recent Activity
            </div>
            <div class="card-body p-0">
                @if($recentLogs->isEmpty())
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                        <span class="small">No recent activity recorded.</span>
            {{-- Tab 3: Activity & Audit Logs --}}
            <div class="tab-pane fade" id="activityPane" role="tabpanel">
                <div class="card card-custom p-0 overflow-hidden">
                    <div class="p-3 bg-white border-bottom d-flex justify-content-between align-items-center">
                        <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-clock-history me-2 text-dark"></i>Personal Event Activity Log</h6>
                        <span class="badge bg-secondary">Immutable Trail</span>
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

                    @if($recentLogs->isEmpty())
                        <div class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                            <span class="small">No recent activity recorded.</span>
                        </div>
                    @else
                        <div class="list-group list-group-flush">
                            @foreach($recentLogs as $log)
                                <div class="list-group-item d-flex align-items-start gap-3 py-3">
                                    <div class="activity-dot mt-1 {{ str_contains(strtolower($log->action), 'login') ? 'login' : (str_contains(strtolower($log->action), 'create') || str_contains(strtolower($log->action), 'update') || str_contains(strtolower($log->action), 'delete') ? 'action' : 'default') }}"></div>
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="fw-bold text-dark small">{{ ucfirst($log->action) }}</div>
                                            <div class="text-muted" style="font-size: 0.7rem;">
                                                <i class="bi bi-clock me-1"></i>{{ $log->created_at ? $log->created_at->diffForHumans() : 'N/A' }}
                                            </div>
                                        </div>
                                        <div class="text-muted small mt-1" style="font-size:0.78rem;">
                                            @if($log->entity_type)
                                                <span class="badge bg-light text-dark border me-1" style="font-size:0.68rem;">{{ class_basename($log->entity_type) }}</span>
                                            @endif
                                            @if($log->details)
                                                {{ is_string($log->details) ? $log->details : json_encode($log->details) }}
                                            @endif
                                        </div>
                                        <div class="text-muted mt-1 font-monospace" style="font-size: 0.68rem;">
                                            IP: {{ $log->ip_address ?? '127.0.0.1' }}
                                        </div>
                                    </div>
                                </div>
                                <div class="text-muted text-nowrap" style="font-size:0.7rem;">
                                    <i class="bi bi-clock me-1"></i>{{ $log->created_at ? $log->created_at->diffForHumans() : 'N/A' }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

        </div>

        {{-- Back to Dashboard --}}
        <a href="{{ route('dashboard') }}" class="btn btn-outline-dark">
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

    // Toggle password eye icon unmasking
    document.querySelectorAll('.toggle-password').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var targetId = this.getAttribute('data-target');
            var input = document.getElementById(targetId);
            var icon = this.querySelector('i');
            if (input) {
                if (input.type === 'password') {
                    input.type = 'text';
                    icon.classList.remove('bi-eye');
                    icon.classList.add('bi-eye-slash');
                } else {
                    input.type = 'password';
                    icon.classList.remove('bi-eye-slash');
                    icon.classList.add('bi-eye');
                }
            }
        });
    });

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
