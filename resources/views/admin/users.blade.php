@extends('layouts.app')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h3 class="fw-bold mb-0"><i class="bi bi-people me-2 text-success"></i>User & Role Management</h3>
        <span class="badge bg-secondary">System Administration Console</span>
    </div>
    <div class="d-flex align-items-center gap-2 flex-wrap">
        <button type="button" class="btn btn-outline-dark fw-bold flex-grow-1 flex-md-grow-0" id="exportCsvBtn">
            <i class="bi bi-file-earmark-spreadsheet me-1"></i> Export CSV
        </button>
        <button type="button" class="btn btn-dark fw-bold flex-grow-1 flex-md-grow-0" data-bs-toggle="modal" data-bs-target="#createUserModal">
            <i class="bi bi-person-plus-fill me-1"></i> Create User
        </button>
    </div>
</div>

<div class="card card-custom p-3 p-md-4">
    <form method="GET" action="{{ route('admin.users') }}" class="row g-2 g-md-3 mb-4">
        <div class="col-12 col-md-4">
            <label class="form-label fw-bold small mb-1">Search Name / Email</label>
            <input type="text" name="search" class="form-control form-control-sm form-control-md" placeholder="Search name or email..." value="{{ request('search') }}" onchange="this.form.submit()">
        </div>
        <div class="col-6 col-md-4">
            <label class="form-label fw-bold small mb-1">Role</label>
            <select name="role" class="form-select form-select-sm form-select-md" onchange="this.form.submit()">
                <option value="">All Roles</option>
                @foreach(['pi','tm','reviewer','dh','coordinator','dean','irerc','vparttcs','rcsc','finance','admin'] as $r)
                    <option value="{{ $r }}" {{ request('role') === $r ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $r)) }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-6 col-md-4">
            <label class="form-label fw-bold small mb-1">Status</label>
            <select name="status" class="form-select form-select-sm form-select-md" onchange="this.form.submit()">
                <option value="">All Statuses</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-dark">
                <tr>
                    <th style="white-space: nowrap;">Staff ID</th>
                    <th>User & Email</th>
                    <th style="white-space: nowrap;">Role</th>
                    <th style="white-space: nowrap;">Department / College</th>
                    <th style="white-space: nowrap;">Status</th>
                    <th style="white-space: nowrap;">Last Login</th>
                    <th style="white-space: nowrap; text-align: right;">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $u)
                <tr class="{{ $loop->index % 2 === 0 ? '' : 'table-light' }}">
                    <td style="white-space: nowrap;">
                        <span class="badge bg-light text-dark border font-monospace px-2 py-1" style="font-size: 0.8rem;">
                            {{ $u->staff_id ?: 'N/A' }}
                        </span>
                    </td>
                    <td>
                        <div class="fw-bold text-dark">{{ $u->name }}</div>
                        <div class="small text-muted">{{ $u->email }}</div>
                    </td>
                    <td style="white-space: nowrap;">
                        <span class="badge bg-dark text-uppercase px-2 py-1" style="font-size: 0.72rem;">{{ str_replace('_', ' ', $u->role) }}</span>
                    </td>
                    <td>
                        @if($u->department)
                            <div class="fw-semibold small text-dark">{{ $u->department->name }}</div>
                            <div class="text-muted" style="font-size: 0.75rem;">{{ $u->department->college ? $u->department->college->name : '' }}</div>
                        @else
                            <span class="text-muted small">Central Admin</span>
                        @endif
                    </td>
                    <td style="white-space: nowrap;">
                        <span class="badge {{ $u->status === 'active' ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-secondary-subtle text-secondary border border-secondary-subtle' }} px-2 py-1" style="font-size: 0.75rem; text-transform: capitalize;">
                            {{ $u->status }}
                        </span>
                    </td>
                    <td style="white-space: nowrap;">
                        <small class="text-muted">{{ $u->last_login_at ? $u->last_login_at->diffForHumans() : 'Never' }}</small>
                    </td>
                    <td style="white-space: nowrap; text-align: right;">
                        <div class="d-inline-flex align-items-center gap-1">
                            {{-- 0. View User Details Modal Trigger --}}
                            <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2" data-bs-toggle="modal" data-bs-target="#viewUserModal{{ $u->id }}" title="View User Details">
                                <i class="bi bi-eye"></i>
                            </button>

                            {{-- 1. Edit User --}}
                            <button type="button" class="btn btn-sm btn-outline-dark py-1 px-2" data-bs-toggle="modal" data-bs-target="#editUserModal{{ $u->id }}" title="Edit User">
                                <i class="bi bi-pencil-square"></i>
                            </button>

                            {{-- 2. Reset Password --}}
                            <button type="button" class="btn btn-sm btn-outline-dark py-1 px-2" data-bs-toggle="modal" data-bs-target="#resetPasswordModal{{ $u->id }}" title="Reset Password">
                                <i class="bi bi-key-fill"></i>
                            </button>

                            {{-- 3. Toggle Status (Active / Inactive) --}}
                            @if(Auth::id() !== $u->id)
                            <form method="POST" action="{{ route('admin.users.toggle-status', $u->id) }}" class="d-inline mb-0">
                                @csrf
                                <button type="button" class="btn btn-sm {{ $u->status === 'active' ? 'btn-outline-secondary' : 'btn-outline-success' }} py-1 px-2 confirm-btn"
                                        data-confirm-title="{{ $u->status === 'active' ? 'Deactivate User' : 'Activate User' }}"
                                        data-confirm-message="Are you sure you want to {{ $u->status === 'active' ? 'deactivate' : 'activate' }} the account for {{ $u->name }}?"
                                        data-confirm-icon="{{ $u->status === 'active' ? 'bi-person-slash' : 'bi-person-check' }}"
                                        data-confirm-color="{{ $u->status === 'active' ? 'text-secondary' : 'text-success' }}"
                                        data-confirm-btn-text="Yes, {{ $u->status === 'active' ? 'Deactivate' : 'Activate' }}"
                                        data-confirm-btn-class="{{ $u->status === 'active' ? 'btn-secondary' : 'btn-success' }}"
                                        title="{{ $u->status === 'active' ? 'Deactivate Account' : 'Activate Account' }}">
                                    <i class="bi {{ $u->status === 'active' ? 'bi-person-slash' : 'bi-person-check' }}"></i>
                                </button>
                            </form>

                            {{-- 4. Delete User --}}
                            <form method="POST" action="{{ route('admin.users.destroy', $u->id) }}" class="d-inline mb-0">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn btn-sm btn-outline-danger py-1 px-2 confirm-btn"
                                        data-confirm-title="Delete User Account"
                                        data-confirm-message="Are you sure you want to permanently delete account for {{ $u->name }} ({{ $u->email }})? This action cannot be undone."
                                        data-confirm-icon="bi-trash"
                                        data-confirm-color="text-danger"
                                        data-confirm-btn-text="Yes, Delete"
                                        data-confirm-btn-class="btn-danger"
                                        title="Delete Account">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">
                        <i class="bi bi-people fs-3 d-block mb-2"></i>
                        No user accounts found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-3">
        {{ $users->links() }}
    </div>
</div>

{{-- MODALS PLACED OUTSIDE TABLE FOR PROPER DOM RENDERING & ZERO RTL INHERITANCE --}}
@foreach($users as $u)
    {{-- 1. View User Details Modal --}}
    <div class="modal fade text-start" id="viewUserModal{{ $u->id }}" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content text-start">
                <div class="modal-header bg-dark text-white py-2">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-person-badge me-2 text-success"></i>User Profile — {{ $u->name }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-start py-3">
                    <div class="text-center mb-3">
                        <div class="rounded-circle bg-dark text-white d-inline-flex align-items-center justify-content-center fw-bold fs-4 mb-2" style="width: 55px; height: 55px;">
                            {{ strtoupper(substr($u->name, 0, 1)) }}
                        </div>
                        <h6 class="fw-bold mb-0 text-start text-center">{{ $u->name }}</h6>
                        <span class="badge bg-dark text-uppercase mt-1">{{ str_replace('_', ' ', $u->role) }}</span>
                        <span class="badge {{ $u->status === 'active' ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-secondary-subtle text-secondary border border-secondary-subtle' }} ms-1">
                            {{ ucfirst($u->status) }}
                        </span>
                    </div>

                    <table class="table table-sm table-bordered mb-0 text-start">
                        <tbody>
                            <tr>
                                <th class="bg-light" style="width: 40%;">Staff / Employee ID</th>
                                <td><span class="font-monospace fw-bold">{{ $u->staff_id ?: 'N/A' }}</span></td>
                            </tr>
                            <tr>
                                <th class="bg-light">Email Address</th>
                                <td><a href="mailto:{{ $u->email }}" class="text-decoration-none">{{ $u->email }}</a></td>
                            </tr>
                            <tr>
                                <th class="bg-light">College / Faculty</th>
                                <td>{{ $u->department && $u->department->college ? $u->department->college->name : 'N/A (Central Admin)' }}</td>
                            </tr>
                            <tr>
                                <th class="bg-light">Department</th>
                                <td>{{ $u->department ? $u->department->name . ' (' . $u->department->code . ')' : 'N/A (Central Admin)' }}</td>
                            </tr>
                            <tr>
                                <th class="bg-light">Account Created</th>
                                <td>{{ $u->created_at ? $u->created_at->format('M d, Y H:i') : 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th class="bg-light">Last Login</th>
                                <td>{{ $u->last_login_at ? $u->last_login_at->format('M d, Y H:i') . ' (' . $u->last_login_at->diffForHumans() . ')' : 'Never logged in' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer py-2">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    {{-- 2. Edit User Modal --}}
    <div class="modal fade text-start" id="editUserModal{{ $u->id }}" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content text-start">
                <form method="POST" action="{{ route('admin.users.update', $u->id) }}">
                    @csrf
                    @method('PUT')
                    <div class="modal-header bg-dark text-white py-2">
                        <h5 class="modal-title fw-bold">
                            <i class="bi bi-pencil-square me-2 text-warning"></i>Edit User Account — {{ $u->name }}
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body text-start py-3">
                        <div class="row g-2 mb-3 text-start">
                            <div class="col-md-6">
                                <label class="form-label fw-bold small mb-1 text-start d-block">Staff / Employee ID</label>
                                <input type="text" name="staff_id" class="form-control form-control-sm text-start" value="{{ $u->staff_id }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small mb-1 text-start d-block">Full Name</label>
                                <input type="text" name="name" class="form-control form-control-sm text-start" value="{{ $u->name }}" required>
                            </div>
                        </div>

                        <div class="row g-2 mb-3 text-start">
                            <div class="col-md-6">
                                <label class="form-label fw-bold small mb-1 text-start d-block">Email Address</label>
                                <input type="email" name="email" class="form-control form-control-sm text-start" value="{{ $u->email }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small mb-1 text-start d-block">Account Status</label>
                                <select name="status" class="form-select form-select-sm" required {{ Auth::id() === $u->id ? 'disabled' : '' }}>
                                    <option value="active" {{ $u->status === 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ $u->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                                @if(Auth::id() === $u->id)
                                    <input type="hidden" name="status" value="active">
                                @endif
                            </div>
                        </div>

                        <div class="row g-2 mb-2 text-start">
                            <div class="col-md-6">
                                <label class="form-label fw-bold small mb-1 text-start d-block">System Role</label>
                                <select name="role" class="form-select form-select-sm" required {{ Auth::id() === $u->id ? 'disabled' : '' }}>
                                    <option value="pi" {{ $u->role === 'pi' ? 'selected' : '' }}>Principal Investigator (PI)</option>
                                    <option value="tm" {{ $u->role === 'tm' ? 'selected' : '' }}>Team Member / Co-Researcher (TM)</option>
                                    <option value="reviewer" {{ $u->role === 'reviewer' ? 'selected' : '' }}>Blind Peer Examiner (Reviewer)</option>
                                    <option value="dh" {{ $u->role === 'dh' ? 'selected' : '' }}>Department Head (DH)</option>
                                    <option value="coordinator" {{ $u->role === 'coordinator' ? 'selected' : '' }}>College Research Coordinator</option>
                                    <option value="dean" {{ $u->role === 'dean' ? 'selected' : '' }}>College Dean</option>
                                    <option value="irerc" {{ $u->role === 'irerc' ? 'selected' : '' }}>IRERC Ethics Review Committee</option>
                                    <option value="vparttcs" {{ $u->role === 'vparttcs' ? 'selected' : '' }}>Vice President (ARTTCS)</option>
                                    <option value="rcsc" {{ $u->role === 'rcsc' ? 'selected' : '' }}>RCSC Committee / President</option>
                                    <option value="finance" {{ $u->role === 'finance' ? 'selected' : '' }}>Finance Office</option>
                                    <option value="admin" {{ $u->role === 'admin' ? 'selected' : '' }}>System Administrator (Admin)</option>
                                </select>
                                @if(Auth::id() === $u->id)
                                    <input type="hidden" name="role" value="admin">
                                @endif
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small mb-1 text-start d-block">Department &amp; College</label>
                                <select name="dept_id" class="form-select form-select-sm">
                                    <option value="">-- Central Admin / Research Project Management System-wide --</option>
                                    @foreach($departments as $d)
                                        <option value="{{ $d->id }}" {{ $u->dept_id == $d->id ? 'selected' : '' }}>{{ $d->name }} ({{ $d->code }} - {{ $d->college->name ?? '' }})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer py-2">
                        <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-dark fw-bold px-3 btn-sm confirm-btn" data-confirm-title="Save Changes" data-confirm-message="Update details for {{ $u->name }}?" data-confirm-icon="bi-pencil" data-confirm-color="text-dark" data-confirm-btn-text="Yes, Save" data-confirm-btn-class="btn-dark">
                            <i class="bi bi-check-lg me-1"></i> Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- 3. Password Reset Modal --}}
    <div class="modal fade text-start" id="resetPasswordModal{{ $u->id }}" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content text-start">
                <form method="POST" action="{{ route('admin.users.reset-password', $u->id) }}">
                    @csrf
                    <div class="modal-header bg-dark text-white py-2">
                        <h5 class="modal-title fw-bold">
                            <i class="bi bi-shield-lock me-2 text-warning"></i>Admin Password Reset — {{ $u->name }}
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body text-start py-3">
                        <div class="alert alert-secondary border-start border-4 border-dark small py-2 mb-3 text-start">
                            <i class="bi bi-info-circle me-1"></i>
                            This is the <strong>only</strong> password reset path. Verify the user's identity before proceeding.
                        </div>
                        <div class="mb-3 text-start">
                            <label class="form-label fw-bold text-start d-block">New Password</label>
                            <input type="password" name="new_password" class="form-control text-start" required
                                   placeholder="Min 8 chars, upper, lower, number, symbol">
                            <ul class="mt-2 mb-0 small text-muted ps-3 text-start">
                                <li>Minimum <strong>8 characters</strong></li>
                                <li>Uppercase + lowercase letters</li>
                                <li>At least one number and one symbol</li>
                            </ul>
                        </div>
                    </div>
                    <div class="modal-footer py-2">
                        <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-dark fw-bold btn-sm confirm-btn" data-confirm-title="Reset Password" data-confirm-message="Reset password for {{ $u->name }}?" data-confirm-icon="bi-key" data-confirm-color="text-warning" data-confirm-btn-text="Yes, Reset" data-confirm-btn-class="btn-dark">
                            <i class="bi bi-key-fill me-1 text-warning"></i> Reset Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endforeach

<!-- Modal for Creating New User -->
<div class="modal fade text-start" id="createUserModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content text-start">
            <form method="POST" action="{{ route('admin.users.store') }}">
                @csrf
                <div class="modal-header bg-dark text-white py-2">
                    <h5 class="modal-title fw-bold"><i class="bi bi-person-plus me-2 text-success"></i>Create New User Account</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-start py-2">
                    <div class="row g-2 mb-2 text-start">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small mb-1 text-start d-block">Staff / Employee ID</label>
                            <input type="text" name="staff_id" class="form-control form-control-sm text-start" required placeholder="e.g. UNI-STAFF-999">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small mb-1 text-start d-block">Full Name</label>
                            <input type="text" name="name" class="form-control form-control-sm text-start" required placeholder="Dr. John Doe">
                        </div>
                    </div>

                    <div class="row g-2 mb-2 text-start">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small mb-1 text-start d-block">Email Address</label>
                            <input type="email" name="email" class="form-control form-control-sm text-start" required placeholder="johndoe@rpms.local">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small mb-1 text-start d-block">Initial Password <span class="text-danger">*</span></label>
                            <input type="password" id="create_password" name="password" class="form-control form-control-sm text-start" required
                                   placeholder="Min 8 chars, upper, lower, number, symbol">
                            <div class="form-text small text-muted text-start" style="font-size: 0.72rem;">Min 8 chars, upper, lower, number &amp; symbol.</div>
                        </div>
                    </div>

                    <div class="mb-2 text-start">
                        <label class="form-label fw-bold small mb-1 text-start d-block">Confirm Password <span class="text-danger">*</span></label>
                        <input type="password" id="create_password_confirmation" name="password_confirmation" class="form-control form-control-sm text-start" required
                               placeholder="Re-enter password to match">
                        <div id="passwordMatchFeedback" class="small mt-1" style="font-size: 0.75rem; display: none;"></div>
                    </div>

                    <div class="row g-2 mb-2 text-start">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small mb-1 text-start d-block">System Role <span class="text-danger">*</span></label>
                            <select name="role" id="createUserRoleSelect" class="form-select form-select-sm" required>
                                <option value="pi" selected>Principal Investigator (PI)</option>
                                <option value="tm">Team Member / Co-Researcher (TM)</option>
                                <option value="reviewer">Blind Peer Examiner (Reviewer)</option>
                                <option value="dh">Department Head (DH)</option>
                                <option value="coordinator">College Research Coordinator</option>
                                <option value="dean">College Dean</option>
                                <option value="irerc">IRERC Ethics Review Committee</option>
                                <option value="vparttcs">Vice President (ARTTCS)</option>
                                <option value="rcsc">RCSC Committee / President</option>
                                <option value="finance">Finance Office</option>
                                <option value="admin">System Administrator (Admin)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small mb-1 text-start d-block" id="deptLabel">
                                Academic Department <span id="deptRequiredBadge" class="badge bg-primary-subtle text-primary border ms-1">Required for Academic</span>
                            </label>
                            <select name="dept_id" id="createUserDeptSelect" class="form-select form-select-sm">
                                <option value="">-- Central Admin / Research Project Management System-wide --</option>
                                @foreach($departments as $d)
                                    <option value="{{ $d->id }}">{{ $d->name }} ({{ $d->code }} - {{ $d->college->name ?? '' }})</option>
                                @endforeach
                            </select>
                            <div id="deptHelpText" class="form-text small text-muted text-start" style="font-size: 0.72rem;">
                                Academic proposals, DH screening &amp; College Dean oversight route through this unit.
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer py-2">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-dark fw-bold px-3 btn-sm confirm-btn" data-confirm-title="Create User" data-confirm-message="Create this new user account?" data-confirm-icon="bi-person-plus" data-confirm-color="text-dark" data-confirm-btn-text="Yes, Create" data-confirm-btn-class="btn-dark">
                        <i class="bi bi-person-plus me-1"></i> Create User Account
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.getElementById('exportCsvBtn')?.addEventListener('click', function () {
        const table = document.querySelector('table');
        const rows = Array.from(table.querySelectorAll('tr'));
        const csv = rows.map(row =>
            Array.from(row.querySelectorAll('th, td')).map(cell =>
                '"' + cell.innerText.replace(/"/g, '""') + '"'
            ).join(',')
        ).join('\n');

        const blob = new Blob([csv], { type: 'text/csv' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'users_export.csv';
        a.click();
        URL.revokeObjectURL(url);
    });

    // Dynamic Role-to-Department behavior for Create User modal
    const roleSelect = document.getElementById('createUserRoleSelect');
    const deptSelect = document.getElementById('createUserDeptSelect');
    const deptBadge = document.getElementById('deptRequiredBadge');
    const deptHelp = document.getElementById('deptHelpText');

    function updateDeptRequirement() {
        const academicRoles = ['pi', 'tm', 'reviewer', 'dh', 'coordinator', 'dean'];
        const selectedRole = roleSelect.value;
        if (academicRoles.includes(selectedRole)) {
            deptBadge.className = 'badge bg-primary-subtle text-primary border ms-1';
            deptBadge.textContent = 'Required for Academic';
            deptHelp.textContent = 'Proposals, DH screening & College oversight route through this department & college.';
        } else {
            deptBadge.className = 'badge bg-secondary-subtle text-secondary border ms-1';
            deptBadge.textContent = 'Central Admin (Optional)';
            deptHelp.textContent = 'Central System and finance roles operate Research Project Management System-wide across all colleges.';
        }
    }

    roleSelect?.addEventListener('change', updateDeptRequirement);
    document.getElementById('createUserModal')?.addEventListener('shown.bs.modal', updateDeptRequirement);
    updateDeptRequirement();

    // Real-time password match check in Create User modal
    const passInput = document.getElementById('create_password');
    const confirmPassInput = document.getElementById('create_password_confirmation');
    const matchFeedback = document.getElementById('passwordMatchFeedback');

    function validatePasswordMatch() {
        const p1 = passInput ? passInput.value : '';
        const p2 = confirmPassInput ? confirmPassInput.value : '';

        if (!p2) {
            matchFeedback.style.display = 'none';
            confirmPassInput.classList.remove('is-valid', 'is-invalid');
            return;
        }

        matchFeedback.style.display = 'block';
        if (p1 === p2) {
            confirmPassInput.classList.remove('is-invalid');
            confirmPassInput.classList.add('is-valid');
            matchFeedback.className = 'small mt-1 text-success fw-bold';
            matchFeedback.innerHTML = '<i class="bi bi-check-circle-fill me-1"></i> Passwords match';
        } else {
            confirmPassInput.classList.remove('is-valid');
            confirmPassInput.classList.add('is-invalid');
            matchFeedback.className = 'small mt-1 text-danger fw-bold';
            matchFeedback.innerHTML = '<i class="bi bi-x-circle-fill me-1"></i> Passwords do not match';
        }
    }

    passInput?.addEventListener('input', validatePasswordMatch);
    confirmPassInput?.addEventListener('input', validatePasswordMatch);
</script>
@endsection
