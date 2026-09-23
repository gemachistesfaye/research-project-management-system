@extends('layouts.app')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h3 class="fw-bold mb-0"><i class="bi bi-hdd-network me-2 text-dark"></i>HRMS &amp; External Integration Monitor</h3>
        <span class="badge bg-secondary">System Administration Console</span>
    </div>
    <div class="d-flex align-items-center gap-2 flex-wrap">
        <a href="{{ route('admin.audit-logs') }}" class="btn btn-outline-dark fw-bold flex-grow-1 flex-md-grow-0">
            <i class="bi bi-shield-check me-1"></i> Audit Trail
        </a>
        <a href="{{ route('admin.users') }}" class="btn btn-dark fw-bold flex-grow-1 flex-md-grow-0">
            <i class="bi bi-people me-1"></i> User Directory
        </a>
    </div>
</div>

<div class="row g-2 g-md-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card card-custom p-3 text-center bg-white h-100">
            <div class="text-muted small fw-semibold">Synchronized Staff</div>
            <h3 class="fw-bold my-1 text-dark">{{ $staffCount }}</h3>
            <small class="text-success fw-semibold"><i class="bi bi-check-circle me-1"></i>Payroll Linked</small>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card card-custom p-3 text-center bg-white h-100">
            <div class="text-muted small fw-semibold">Active Accounts</div>
            <h3 class="fw-bold my-1 text-success">{{ $activeStaffCount }}</h3>
            <small class="text-muted">Authenticated</small>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card card-custom p-3 text-center bg-white h-100">
            <div class="text-muted small fw-semibold">Academic Staff</div>
            <h3 class="fw-bold my-1 text-primary">{{ $academicStaffCount }}</h3>
            <small class="text-muted">PI / Reviewers</small>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card card-custom p-3 text-center bg-white h-100">
            <div class="text-muted small fw-semibold">Connected Units</div>
            <h3 class="fw-bold my-1 text-dark">{{ $departmentsCount }} Depts</h3>
            <small class="text-muted">{{ $collegesCount }} Colleges</small>
        </div>
    </div>
</div>

<div class="card card-custom p-4 mb-4">
    <h5 class="fw-bold mb-3"><i class="bi bi-broadcast me-2 text-success"></i>Live Gateway Connectors</h5>
    <div class="row g-3">
        {{-- 1. UNI HRMS Staff API --}}
        <div class="col-md-6">
            <div class="p-3 border rounded-3 bg-light h-100 d-flex flex-column justify-content-between">
                <div>
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div class="fw-bold fs-6 text-dark">
                            <i class="bi bi-person-vcard-fill text-success me-2 fs-5"></i>UNI HRMS Staff Payroll API
                        </div>
                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                            <i class="bi bi-circle-fill me-1" style="font-size: 0.55rem;"></i>Healthy
                        </span>
                    </div>
                    <p class="small text-muted mb-2">
                        Real-time bridge validating employee IDs, active employment status, academic ranks, and department assignments.
                    </p>
                    <ul class="list-unstyled small text-muted mb-3 ps-1">
                        <li><i class="bi bi-shield-lock text-dark me-1"></i> Protocol: <strong>TLS 1.3 REST API (HMAC-SHA256)</strong></li>
                        <li><i class="bi bi-clock-history text-dark me-1"></i> Auto-Heartbeat: <strong>Every 15 minutes</strong></li>
                        <li><i class="bi bi-database text-dark me-1"></i> Directory: <strong>Institution Central HR</strong></li>
                    </ul>
                </div>
                <form method="POST" action="{{ route('admin.hrms-sync.ping') }}" class="mt-2">
                    @csrf
                    <input type="hidden" name="gateway" value="hrms">
                    <button type="submit" class="btn btn-sm btn-dark w-100 fw-bold py-2">
                        <i class="bi bi-arrow-repeat me-1"></i> Ping HRMS Service Handshake
                    </button>
                </form>
            </div>
        </div>

        {{-- 2. UNI Procurement Gateway --}}
        <div class="col-md-6">
            <div class="p-3 border rounded-3 bg-light h-100 d-flex flex-column justify-content-between">
                <div>
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div class="fw-bold fs-6 text-dark">
                            <i class="bi bi-cart-check-fill text-primary me-2 fs-5"></i>UNI Procurement &amp; Inventory Gateway
                        </div>
                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                            <i class="bi bi-circle-fill me-1" style="font-size: 0.55rem;"></i>Healthy
                        </span>
                    </div>
                    <p class="small text-muted mb-2">
                        Enterprise interface syncing approved research purchase orders, lab chemicals, and institutional property tags.
                    </p>
                    <ul class="list-unstyled small text-muted mb-3 ps-1">
                        <li><i class="bi bi-shield-lock text-dark me-1"></i> Protocol: <strong>JSON Webhooks &amp; REST</strong></li>
                        <li><i class="bi bi-clock-history text-dark me-1"></i> Event Trigger: <strong>Purchase Approval / Delivery</strong></li>
                        <li><i class="bi bi-building text-dark me-1"></i> Destination: <strong>UNI Property Administration</strong></li>
                    </ul>
                </div>
                <form method="POST" action="{{ route('admin.hrms-sync.ping') }}" class="mt-2">
                    @csrf
                    <input type="hidden" name="gateway" value="procurement">
                    <button type="submit" class="btn btn-sm btn-outline-dark w-100 fw-bold py-2">
                        <i class="bi bi-arrow-repeat me-1"></i> Ping Procurement Gateway
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="card card-custom p-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold mb-0"><i class="bi bi-journal-text me-2 text-dark"></i>Recent Gateway Probes &amp; Sync Events</h5>
        <span class="small text-muted">Audited &amp; Logged</span>
    </div>

    <div class="table-responsive">
        <table class="table table-sm table-bordered align-middle mb-0">
            <thead class="table-dark">
                <tr>
                    <th style="white-space: nowrap;">Timestamp</th>
                    <th style="white-space: nowrap;">Connector</th>
                    <th style="white-space: nowrap;">Status</th>
                    <th>Audit Details</th>
                    <th style="white-space: nowrap;">IP Address</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentSyncLogs as $log)
                <tr>
                    <td style="white-space: nowrap;"><small class="text-muted">{{ $log->created_at->format('M d, Y H:i:s') }}</small></td>
                    <td style="white-space: nowrap;"><span class="badge bg-dark">{{ $log->entity_type ?: 'Integration' }}</span></td>
                    <td style="white-space: nowrap;"><span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">200 OK</span></td>
                    <td><small class="text-dark">{{ $log->details }}</small></td>
                    <td style="white-space: nowrap;"><span class="badge bg-light text-dark border font-monospace">{{ $log->ip_address ?: '127.0.0.1' }}</span></td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center text-muted py-3">
                        <i class="bi bi-info-circle me-1"></i>No recent manual probes recorded. Click "Ping Handshake" above to test the bridge.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
