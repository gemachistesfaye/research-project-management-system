@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-0"><i class="bi bi-people me-2 text-success"></i>User & Role Management</h3>
        <span class="badge bg-secondary">System Administration Console</span>
    </div>
    <div>
        <button type="button" class="btn btn-outline-success fw-bold me-2" id="exportCsvBtn">
            <i class="bi bi-file-earmark-spreadsheet me-1"></i> Export CSV
        </button>
        <button type="button" class="btn btn-success fw-bold" data-bs-toggle="modal" data-bs-target="#createUserModal">
            <i class="bi bi-person-plus-fill me-1"></i> Create New User Account
        </button>
    </div>
</div>

<div class="card card-custom p-4">
    <form method="GET" action="{{ route('admin.users') }}" class="row g-3 mb-4">
        <div class="col-md-4">
            <label class="form-label fw-bold small">Search Name / Email</label>
            <input type="text" name="search" class="form-control" placeholder="Search..." value="{{ request('search') }}" onchange="this.form.submit()">
        </div>
        <div class="col-md-4">
            <label class="form-label fw-bold small">Role</label>
            <select name="role" class="form-select" onchange="this.form.submit()">
                <option value="">All Roles</option>
                @foreach(['pi','tm','reviewer','dh','coordinator','dean','irerc','vparttcs','rcsc','finance','admin'] as $r)
                    <option value="{{ $r }}" {{ request('role') === $r ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $r)) }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label fw-bold small">Status</label>
            <select name="status" class="form-select" onchange="this.form.submit()">
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
                    <th>Staff ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Department</th>
                    <th>Status</th>
                    <th>Last Login</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $u)
                <tr class="{{ $loop->index % 2 === 0 ? '' : 'table-light' }}">
                    <td><code>{{ $u->staff_id ?: 'N/A' }}</code></td>
                    <td class="fw-bold">{{ $u->name }}</td>
                    <td>{{ $u->email }}</td>
                    <td><span class="badge bg-dark text-uppercase">{{ $u->role }}</span></td>
                    <td>{{ $u->department ? $u->department->name : 'Central Admin' }}</td>
                    <td><span class="badge {{ $u->status === 'active' ? 'bg-success' : 'bg-secondary' }}">{{ $u->status }}</span></td>
                    <td>{{ $u->last_login_at ? $u->last_login_at->diffForHumans() : 'Never' }}</td>
                    <td>
                        <button type="button" class="btn btn-sm btn-outline-warning text-dark" data-bs-toggle="modal" data-bs-target="#resetPasswordModal{{ $u->id }}">
                            <i class="bi bi-key-fill me-1"></i> Reset Password
                        </button>

                        <div class="modal fade" id="resetPasswordModal{{ $u->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form method="POST" action="{{ route('admin.users.reset-password', $u->id) }}">
                                        @csrf
                                        <div class="modal-header bg-warning-subtle">
                                            <h5 class="modal-title fw-bold">
                                                <i class="bi bi-shield-lock me-2"></i>Admin Password Reset — {{ $u->name }}
                                            </h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="alert alert-info small py-2 mb-3">
                                                <i class="bi bi-info-circle me-1"></i>
                                                This is the <strong>only</strong> password reset path. Verify the user's identity in person before proceeding.
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">New Password</label>
                                                <input type="password" name="new_password" class="form-control" required
                                                       placeholder="Min 8 chars, upper, lower, number, symbol">
                                                <ul class="mt-1 mb-0 small text-muted ps-3">
                                                    <li>Minimum <strong>8 characters</strong></li>
                                                    <li>Uppercase + lowercase letters</li>
                                                    <li>At least one number and one symbol</li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <button type="button" class="btn btn-warning fw-bold text-dark confirm-btn" data-confirm-title="Reset Password" data-confirm-message="Reset password for {{ $u->name }}?" data-confirm-icon="bi-key" data-confirm-color="text-warning" data-confirm-btn-text="Yes, Reset" data-confirm-btn-class="btn-warning">
                                                <i class="bi bi-key-fill me-1"></i> Reset Password
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center text-muted py-4">
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

<!-- Modal for Creating New User -->
<div class="modal fade" id="createUserModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.users.store') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold"><i class="bi bi-person-plus me-2 text-success"></i>Create New Institutional User Account</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Staff / Employee ID</label>
                            <input type="text" name="staff_id" class="form-control" required placeholder="e.g. GMU-STAFF-999">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Full Name</label>
                            <input type="text" name="name" class="form-control" required placeholder="Dr. John Doe">
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Email Address</label>
                            <input type="email" name="email" class="form-control" required placeholder="johndoe@gmu.edu.et">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Initial Password</label>
                            <input type="password" name="password" class="form-control" required
                                   placeholder="Min 8 chars, upper, lower, number, symbol">
                            <div class="form-text small text-muted">User must change this on first login.</div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Confirm Password</label>
                        <input type="password" name="password_confirmation" class="form-control" required
                               placeholder="Re-enter password">
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">System Role</label>
                            <select name="role" class="form-select" required>
                                <option value="pi">Principal Investigator (PI)</option>
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
                            <label class="form-label fw-bold">Department (Optional)</label>
                            <select name="dept_id" class="form-select">
                                <option value="">-- Central Admin / University-wide --</option>
                                @foreach($departments as $d)
                                    <option value="{{ $d->id }}">{{ $d->name }} ({{ $d->code }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success fw-bold px-4 confirm-btn" data-confirm-title="Create User" data-confirm-message="Create this new user account?" data-confirm-icon="bi-person-plus" data-confirm-color="text-success" data-confirm-btn-text="Yes, Create" data-confirm-btn-class="btn-success">
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
</script>
@endsection
