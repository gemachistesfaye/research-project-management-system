@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="fw-bold mb-0"><i class="bi bi-shield-check me-2 text-dark"></i>Audit Trail &amp; Security Logs</h4>
        <span class="badge bg-secondary d-none d-sm-inline-block">Immutable System Event Trail</span>
    </div>
    <div>
        <button type="button" class="btn btn-outline-dark btn-sm fw-bold" id="exportAuditCsvBtn">
            <i class="bi bi-file-earmark-spreadsheet me-1"></i> <span class="d-none d-sm-inline">Export CSV</span><span class="d-inline d-sm-none">CSV</span>
        </button>
    </div>
</div>

<div class="card card-custom p-3 p-md-4">
    {{-- Collapsible Filter Accordion for Mobile / Compact on Desktop --}}
    <form method="GET" action="{{ route('admin.audit-logs') }}" class="row g-2 mb-3 align-items-end">
        <div class="col-6 col-md-3">
            <label for="date_from" class="form-label small fw-bold mb-1" style="font-size: 0.75rem;">Date From</label>
            <input type="date" class="form-control form-control-sm" id="date_from" name="date_from" value="{{ $filters['date_from'] ?? '' }}" onchange="this.form.submit()">
        </div>
        <div class="col-6 col-md-3">
            <label for="date_to" class="form-label small fw-bold mb-1" style="font-size: 0.75rem;">Date To</label>
            <input type="date" class="form-control form-control-sm" id="date_to" name="date_to" value="{{ $filters['date_to'] ?? '' }}" onchange="this.form.submit()">
        </div>
        <div class="col-6 col-md-3">
            <label for="action" class="form-label small fw-bold mb-1" style="font-size: 0.75rem;">Action Type</label>
            <select class="form-select form-select-sm" id="action" name="action" onchange="this.form.submit()">
                <option value="">All Actions</option>
                @php
                    $actions = ['LOGIN', 'LOGOUT', 'CREATE_USER', 'UPDATE_USER', 'STATUS_CHANGE', 'RESET_PASSWORD', 'DELETE_USER', 'PROPOSAL_SUBMITTED', 'EVALUATION_SCORED', 'BUDGET_DISBURSED', 'HRMS_SYNC_PROBE'];
                    $currentAction = $filters['action'] ?? '';
                @endphp
                @foreach($actions as $actionType)
                    <option value="{{ $actionType }}" {{ $currentAction === $actionType ? 'selected' : '' }}>{{ $actionType }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-6 col-md-3">
            <label for="user_search" class="form-label small fw-bold mb-1" style="font-size: 0.75rem;">User Search</label>
            <input type="text" class="form-control form-control-sm" id="user_search" name="user_search" placeholder="Name or email..." value="{{ $filters['user_search'] ?? '' }}" onchange="this.form.submit()">
        </div>
    </form>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" style="font-size: 0.84rem;">
            <thead class="table-dark">
                <tr>
                    <th style="white-space: nowrap;">Timestamp</th>
                    <th style="white-space: nowrap;">User</th>
                    <th style="white-space: nowrap;">Action</th>
                    <th style="white-space: nowrap;">Entity</th>
                    <th>Details</th>
                    <th style="white-space: nowrap;">IP Address</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                <tr class="{{ $loop->iteration % 2 === 0 ? 'table-light' : '' }}">
                    <td style="white-space: nowrap;">
                        <small class="text-muted d-block" style="font-size: 0.75rem;">{{ $log->created_at ? $log->created_at->format('M d, Y') : 'N/A' }}</small>
                        <small class="text-muted fw-semibold" style="font-size: 0.72rem;">{{ $log->created_at ? $log->created_at->format('H:i:s') : '' }}</small>
                    </td>
                    <td style="white-space: nowrap;">
                        <div class="fw-bold text-dark" style="font-size: 0.8rem;">{{ $log->user ? $log->user->name : 'System / Guest' }}</div>
                        <small class="text-muted" style="font-size: 0.72rem;">{{ $log->user ? $log->user->email : 'N/A' }}</small>
                    </td>
                    <td style="white-space: nowrap;">
                        @php
                            $action = strtolower($log->action ?? '');
                            $badgeClass = 'bg-dark';
                            if (str_contains($action, 'login') || str_contains($action, 'logout')) {
                                $badgeClass = 'bg-primary-subtle text-primary border border-primary-subtle';
                            } elseif (str_contains($action, 'create') || str_contains($action, 'submit')) {
                                $badgeClass = 'bg-success-subtle text-success border border-success-subtle';
                            } elseif (str_contains($action, 'update') || str_contains($action, 'edit') || str_contains($action, 'status')) {
                                $badgeClass = 'bg-warning-subtle text-dark border border-warning-subtle';
                            } elseif (str_contains($action, 'delete') || str_contains($action, 'reject') || str_contains($action, 'denied')) {
                                $badgeClass = 'bg-danger-subtle text-danger border border-danger-subtle';
                            } elseif (str_contains($action, 'approve') || str_contains($action, 'release') || str_contains($action, 'reset') || str_contains($action, 'probe')) {
                                $badgeClass = 'bg-info-subtle text-info-emphasis border border-info-subtle';
                            }
                        @endphp
                        <span class="badge {{ $badgeClass }} px-2 py-1" style="font-size: 0.7rem;">{{ $log->action }}</span>
                    </td>
                    <td style="white-space: nowrap;"><small class="fw-semibold">{{ $log->entity_type ? $log->entity_type . ' #' . $log->entity_id : 'System' }}</small></td>
                    <td><small class="text-dark">{{ $log->details ?: 'No additional metadata.' }}</small></td>
                    <td style="white-space: nowrap;"><span class="badge bg-light text-dark border font-monospace" style="font-size: 0.72rem;">{{ $log->ip_address ?: '127.0.0.1' }}</span></td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted py-4">
                        <i class="bi bi-shield-check fs-3 d-block mb-2"></i>
                        No security audit logs recorded yet.
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
<script>
    document.getElementById('exportAuditCsvBtn')?.addEventListener('click', function () {
        const table = document.querySelector('table');
        const rows = Array.from(table.querySelectorAll('tr'));
        const csv = rows.map(row =>
            Array.from(row.querySelectorAll('th, td')).map(cell =>
                '"' + cell.innerText.replace(/"/g, '""').trim() + '"'
            ).join(',')
        ).join('\n');

        const blob = new Blob([csv], { type: 'text/csv' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'gmu_audit_logs_' + new Date().toISOString().slice(0, 10) + '.csv';
        a.click();
        URL.revokeObjectURL(url);
    });
</script>
@endsection
