@extends('layouts.app')

@section('content')
<h3 class="fw-bold mb-4"><i class="bi bi-shield-check me-2 text-dark"></i>Audit Trail & Security Logs</h3>
<div class="card card-custom p-4">
    <div class="alert alert-secondary border-start border-4 border-dark small mb-3">
        <i class="bi bi-lock-fill me-1"></i>Immutable system audit log trail recording all logins, evaluation scoring, and budget approvals.
    </div>

    <form method="GET" action="{{ route('admin.audit-logs') }}" class="row g-3 align-items-end mb-4">
        <div class="col-md-3">
            <label for="date_from" class="form-label small fw-semibold">Date From</label>
            <input type="date" class="form-control" id="date_from" name="date_from" value="{{ $filters['date_from'] ?? '' }}" onchange="this.form.submit()">
        </div>
        <div class="col-md-3">
            <label for="date_to" class="form-label small fw-semibold">Date To</label>
            <input type="date" class="form-control" id="date_to" name="date_to" value="{{ $filters['date_to'] ?? '' }}" onchange="this.form.submit()">
        </div>
        <div class="col-md-3">
            <label for="action" class="form-label small fw-semibold">Action Type</label>
            <select class="form-select" id="action" name="action" onchange="this.form.submit()">
                <option value="">All Actions</option>
                @php
                    $actions = ['Login', 'Logout', 'Create', 'Submit', 'Update', 'Edit', 'Delete', 'Reject', 'Terminate', 'Approve', 'Release'];
                    $currentAction = $filters['action'] ?? '';
                @endphp
                @foreach($actions as $actionType)
                    <option value="{{ $actionType }}" {{ $currentAction === $actionType ? 'selected' : '' }}>{{ $actionType }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label for="user_search" class="form-label small fw-semibold">User (Name/Email)</label>
            <input type="text" class="form-control" id="user_search" name="user_search" placeholder="Search user..." value="{{ $filters['user_search'] ?? '' }}" onchange="this.form.submit()">
        </div>
    </form>

    <div class="d-flex justify-content-end mb-3">
        <a href="#" class="btn btn-outline-dark" onclick="alert('Export functionality coming soon!'); return false;">
            <i class="bi bi-download me-1"></i>Export CSV
        </a>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered align-middle mb-0">
            <thead class="table-dark">
                <tr>
                    <th>Timestamp</th>
                    <th>User</th>
                    <th>Action</th>
                    <th>Entity</th>
                    <th>IP Address</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                <tr class="{{ $loop->iteration % 2 === 0 ? 'table-light' : '' }}">
                    <td>{{ $log->created_at ? $log->created_at->format('M d, Y H:i') : 'N/A' }}</td>
                    <td>{{ $log->user ? $log->user->name : 'System' }}</td>
                    <td>
                        @php
                            $action = strtolower($log->action ?? '');
                            $badgeClass = 'bg-dark';
                            if (str_contains($action, 'login') || str_contains($action, 'logout')) {
                                $badgeClass = 'bg-secondary';
                            } elseif (str_contains($action, 'create') || str_contains($action, 'submit')) {
                                $badgeClass = 'bg-success';
                            } elseif (str_contains($action, 'update') || str_contains($action, 'edit')) {
                                $badgeClass = 'bg-warning text-dark';
                            } elseif (str_contains($action, 'delete') || str_contains($action, 'reject') || str_contains($action, 'terminate')) {
                                $badgeClass = 'bg-danger';
                            } elseif (str_contains($action, 'approve') || str_contains($action, 'release')) {
                                $badgeClass = 'bg-success';
                            }
                        @endphp
                        <span class="badge {{ $badgeClass }}">{{ $log->action }}</span>
                    </td>
                    <td>{{ $log->entity_type }} #{{ $log->entity_id }}</td>
                    <td><code>{{ $log->ip_address ?: '127.0.0.1' }}</code></td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center text-muted py-4">
                        <i class="bi bi-shield-check fs-3 d-block mb-2"></i>
                        No security audit logs generated yet.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-3">
        {{ $logs->links() }}
    </div>
</div>
@endsection
