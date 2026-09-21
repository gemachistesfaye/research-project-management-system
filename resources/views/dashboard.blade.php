@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
    <div>
        <h3 class="fw-bold mb-0">Dashboard Overview</h3>
        <span class="text-muted">Welcome back, {{ preg_replace('/\s*\([^)]*\)/', '', $user->name) }}</span>
    </div>
    @if($role === 'pi')
        <a href="{{ route('projects.create') }}" class="btn btn-success fw-bold shadow-sm">
            <i class="bi bi-plus-lg me-1"></i> Submit New Proposal
        </a>
    @elseif($role === 'dh')
        <a href="{{ route('dh.screening') }}" class="btn btn-warning fw-bold text-dark shadow-sm">
            <i class="bi bi-ui-checks me-1"></i> Open Dept Screening Queue
        </a>
    @elseif($role === 'coordinator')
        {{-- Coordinator header kept clean and uncluttered --}}
    @elseif($role === 'dean')
        <a href="{{ route('dean.approvals') }}" class="btn btn-primary fw-bold shadow-sm">
            <i class="bi bi-bank me-1"></i> Open Dean Approvals
        </a>
    @elseif(in_array($role, ['vparttcs', 'rcsc']))
        {{-- Vice President & RCSC header kept clean and uncluttered --}}
    @elseif($role === 'finance')
        <a href="{{ route('finance.disbursement') }}" class="btn btn-success fw-bold shadow-sm">
            <i class="bi bi-cash-stack me-1"></i> Open Finance Disbursement
        </a>
    @elseif($role === 'tm')
        <a href="{{ route('progress.index') }}" class="btn btn-info fw-bold shadow-sm">
            <i class="bi bi-graph-up me-1"></i> My Progress Reports
        </a>
    @elseif($role === 'reviewer')
        <a href="{{ route('evaluations.index') }}" class="btn btn-primary fw-bold shadow-sm">
            <i class="bi bi-clipboard-check me-1"></i> My Evaluations
        </a>
    @endif
</div>

{{-- Pending Cancellation Requests Alert --}}
@php
    $pendingCancellations = App\Models\Project::where('status', 'PendingCancellation')->get();
@endphp
@if(in_array($role, ['admin', 'coordinator', 'dean', 'rcsc', 'vparttcs']) && $pendingCancellations->count() > 0)
<div class="alert alert-warning d-flex align-items-center mb-4 shadow-sm">
    <i class="bi bi-exclamation-triangle-fill fs-3 me-3"></i>
    <div class="flex-grow-1">
        <strong>{{ $pendingCancellations->count() }} pending withdrawal request(s)</strong> awaiting your review.
    </div>
    <a href="{{ route('projects.index') }}" class="btn btn-warning fw-bold">Review Now</a>
</div>
@endif

{{-- PI Quick Actions --}}
@if($role === 'pi')
<div class="card card-custom p-3 p-md-4 mb-4">
    <h5 class="fw-bold mb-3"><i class="bi bi-lightning me-2 text-dark"></i>Quick Actions</h5>
    <div class="row g-2">
        <div class="col-6 col-md-auto">
            <a href="{{ route('progress.index') }}" class="btn btn-outline-primary fw-bold w-100 text-nowrap py-2">
                <i class="bi bi-file-earmark-bar-graph me-1"></i> <span class="d-none d-sm-inline">Progress Reports</span><span class="d-inline d-sm-none">Progress</span>
            </a>
        </div>
        <div class="col-6 col-md-auto">
            <a href="{{ route('extensions.index') }}" class="btn btn-outline-info fw-bold w-100 text-nowrap py-2">
                <i class="bi bi-clock-history me-1"></i> <span class="d-none d-sm-inline">Extensions</span><span class="d-inline d-sm-none">Extensions</span>
            </a>
        </div>
        <div class="col-6 col-md-auto">
            <a href="{{ route('procurement.index') }}" class="btn btn-outline-secondary fw-bold w-100 text-nowrap py-2">
                <i class="bi bi-cart3 me-1"></i> <span class="d-none d-sm-inline">Procurement</span><span class="d-inline d-sm-none">Procurement</span>
            </a>
        </div>
        <div class="col-6 col-md-auto">
            <a href="{{ route('projects.index') }}" class="btn btn-outline-dark fw-bold w-100 text-nowrap py-2">
                <i class="bi bi-people me-1"></i> <span class="d-none d-sm-inline">Manage Team</span><span class="d-inline d-sm-none">Team</span>
            </a>
        </div>
    </div>
</div>
@endif

{{-- Coordinator Quick Actions --}}
@if($role === 'coordinator')
<div class="card card-custom p-3 p-md-4 mb-4">
    <h5 class="fw-bold mb-3"><i class="bi bi-lightning me-2 text-dark"></i>Coordinator Quick Actions</h5>
    <div class="row g-2">
        <div class="col-6 col-md-auto">
            <a href="{{ route('progress.index') }}" class="btn btn-outline-dark fw-bold w-100 text-nowrap py-2">
                <i class="bi bi-graph-up me-1"></i> <span class="d-none d-sm-inline">Milestone Progress &amp; Audit</span><span class="d-inline d-sm-none">Milestones</span>
            </a>
        </div>
        <div class="col-6 col-md-auto">
            <a href="{{ route('procurement.index') }}" class="btn btn-outline-secondary fw-bold w-100 text-nowrap py-2">
                <i class="bi bi-cart3 me-1"></i> <span class="d-none d-sm-inline">Procurement Tracker</span><span class="d-inline d-sm-none">Procurement</span>
            </a>
        </div>
        <div class="col-12 col-md-auto">
            <a href="{{ route('certificates') }}" class="btn btn-outline-success fw-bold w-100 text-nowrap py-2">
                <i class="bi bi-award me-1"></i> <span class="d-none d-sm-inline">Completion Certificates</span><span class="d-inline d-sm-none">Certificates</span>
            </a>
        </div>
    </div>
</div>
@endif

{{-- Vice President & RCSC Quick Actions --}}
@if(in_array($role, ['vparttcs', 'rcsc']))
<div class="card card-custom p-3 p-md-4 mb-4">
    <h5 class="fw-bold mb-3"><i class="bi bi-lightning me-2 text-dark"></i>Executive Quick Actions</h5>
    <div class="row g-2">
        <div class="col-6 col-md-auto">
            <a href="{{ route('analytics') }}" class="btn btn-outline-dark fw-bold w-100 text-nowrap py-2">
                <i class="bi bi-bar-chart-line me-1"></i> <span class="d-none d-sm-inline">Analytics &amp; Reports</span><span class="d-inline d-sm-none">Analytics</span>
            </a>
        </div>
        <div class="col-6 col-md-auto">
            <a href="{{ route('certificates') }}" class="btn btn-outline-success fw-bold w-100 text-nowrap py-2">
                <i class="bi bi-award me-1"></i> <span class="d-none d-sm-inline">Completion Certificates</span><span class="d-inline d-sm-none">Certificates</span>
            </a>
        </div>
        <div class="col-12 col-md-auto">
            <a href="{{ route('procurement.index') }}" class="btn btn-outline-secondary fw-bold w-100 text-nowrap py-2">
                <i class="bi bi-cart3 me-1"></i> <span class="d-none d-sm-inline">Procurement Tracker</span><span class="d-inline d-sm-none">Procurement</span>
            </a>
        </div>
    </div>
</div>
@endif

{{-- Admin Quick Actions --}}
@if($role === 'admin')
<div class="card card-custom p-4 mb-4">
    <h5 class="fw-bold mb-3"><i class="bi bi-lightning me-2 text-dark"></i>Quick Actions</h5>
    <div class="d-flex flex-wrap gap-2">
        <a href="{{ route('admin.users') }}" class="btn btn-outline-dark fw-bold">
            <i class="bi bi-people me-1"></i> User Management
        </a>
        <a href="{{ route('analytics') }}" class="btn btn-outline-dark fw-bold">
            <i class="bi bi-bar-chart-line me-1"></i> Analytics &amp; Reports
        </a>
        <a href="{{ route('admin.thematic-areas') }}" class="btn btn-outline-dark fw-bold">
            <i class="bi bi-diagram-3 me-1"></i> Thematic Areas
        </a>
        <a href="{{ route('admin.hrms-sync') }}" class="btn btn-outline-dark fw-bold">
            <i class="bi bi-hdd-network me-1"></i> HRMS Sync
        </a>
        <a href="{{ route('admin.audit-logs') }}" class="btn btn-outline-dark fw-bold">
            <i class="bi bi-shield-check me-1"></i> Audit Logs
        </a>
    </div>
</div>
@endif

{{-- Role-Specific KPI Summary Cards --}}
<div id="kpi-cards" class="row g-3 mb-4">
    {{-- PI KPI Cards --}}
    @if($role === 'pi')
    <div class="col-md-3 col-sm-6">
        <div class="card card-custom p-3 bg-white h-100">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small text-uppercase fw-bold" style="font-size: 0.72rem; letter-spacing: 0.5px;">My Projects</div>
                    <div class="fs-2 fw-bold text-dark mt-1">{{ $stats['my_projects'] ?? 0 }}</div>
                    <div class="small text-muted" style="font-size: 0.75rem;">All submissions</div>
                </div>
                <div class="stat-bubble">
                    <i class="bi bi-folder2 text-secondary" style="font-size: 1.35rem;"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="card card-custom p-3 bg-white h-100">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small text-uppercase fw-bold" style="font-size: 0.72rem; letter-spacing: 0.5px;">Drafts</div>
                    <div class="fs-2 fw-bold mt-1" text-dark>{{ $stats['draft'] ?? 0 }}</div>
                    <div class="small text-muted" style="font-size: 0.75rem;">Not yet submitted</div>
                </div>
                <div class="stat-bubble">
                    <i class="bi bi-pencil-square" text-dark></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="card card-custom p-3 bg-white h-100">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small text-uppercase fw-bold" style="font-size: 0.72rem; letter-spacing: 0.5px;">Active</div>
                    <div class="fs-2 fw-bold text-dark mt-1">{{ $stats['active'] ?? 0 }}</div>
                    <div class="small text-muted" style="font-size: 0.75rem;">Currently executing</div>
                </div>
                <div class="stat-bubble">
                    <i class="bi bi-play-circle text-dark" style="font-size: 1.35rem;"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="card card-custom p-3 bg-white h-100">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small text-uppercase fw-bold" style="font-size: 0.72rem; letter-spacing: 0.5px;">Total Budget</div>
                    <div class="fs-2 fw-bold text-dark mt-1">{{ $stats['total_budget'] ?? 0 }}</div>
                    <div class="small text-muted" style="font-size: 0.75rem;">ETB requested</div>
                </div>
                <div class="stat-bubble">
                    <i class="bi bi-wallet2 text-dark" style="font-size: 1.35rem;"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Reviewer KPI Cards --}}
    @elseif($role === 'reviewer')
    <div class="col-md-3 col-sm-6">
        <div class="card card-custom p-3 bg-white h-100">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small text-uppercase fw-bold" style="font-size: 0.72rem; letter-spacing: 0.5px;">Pending Reviews</div>
                    <div class="fs-2 fw-bold text-dark mt-1">{{ $stats['pending_reviews'] ?? 0 }}</div>
                    <div class="small text-muted" style="font-size: 0.75rem;">Awaiting evaluation</div>
                </div>
                <div class="stat-bubble">
                    <i class="bi bi-clock-history text-dark" style="font-size: 1.35rem;"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="card card-custom p-3 bg-white h-100">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small text-uppercase fw-bold" style="font-size: 0.72rem; letter-spacing: 0.5px;">Completed Reviews</div>
                    <div class="fs-2 fw-bold text-dark mt-1">{{ $stats['completed_reviews'] ?? 0 }}</div>
                    <div class="small text-muted" style="font-size: 0.75rem;">Evaluated proposals</div>
                </div>
                <div class="stat-bubble">
                    <i class="bi bi-check-circle text-dark" style="font-size: 1.35rem;"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="card card-custom p-3 bg-white h-100">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small text-uppercase fw-bold" style="font-size: 0.72rem; letter-spacing: 0.5px;">Avg Score Given</div>
                    <div class="fs-2 fw-bold text-dark mt-1">{{ $stats['avg_score'] ?? 0 }}</div>
                    <div class="small text-muted" style="font-size: 0.75rem;">/ 100 average</div>
                </div>
                <div class="stat-bubble">
                    <i class="bi bi-bar-chart text-secondary" style="font-size: 1.35rem;"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- DH KPI Cards --}}
    @elseif($role === 'dh')
    <div class="col-md-3 col-sm-6">
        <div class="card card-custom p-3 bg-white h-100">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small text-uppercase fw-bold" style="font-size: 0.72rem; letter-spacing: 0.5px;">Pending Screening</div>
                    <div class="fs-2 fw-bold text-dark mt-1">{{ $stats['pending_screening'] ?? 0 }}</div>
                    <div class="small text-muted" style="font-size: 0.75rem;">Dept proposals</div>
                </div>
                <div class="stat-bubble">
                    <i class="bi bi-funnel text-dark" style="font-size: 1.35rem;"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="card card-custom p-3 bg-white h-100">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small text-uppercase fw-bold" style="font-size: 0.72rem; letter-spacing: 0.5px;">Screened Today</div>
                    <div class="fs-2 fw-bold text-dark mt-1">{{ $stats['screened_today'] ?? 0 }}</div>
                    <div class="small text-muted" style="font-size: 0.75rem;">Completed today</div>
                </div>
                <div class="stat-bubble">
                    <i class="bi bi-check2-all text-dark" style="font-size: 1.35rem;"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="card card-custom p-3 bg-white h-100">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small text-uppercase fw-bold" style="font-size: 0.72rem; letter-spacing: 0.5px;">Dept Projects</div>
                    <div class="fs-2 fw-bold text-dark mt-1">{{ $stats['dept_projects'] ?? 0 }}</div>
                    <div class="small text-muted" style="font-size: 0.75rem;">Total in department</div>
                </div>
                <div class="stat-bubble">
                    <i class="bi bi-building text-secondary" style="font-size: 1.35rem;"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Coordinator KPI Cards --}}
    @elseif($role === 'coordinator')
    <div class="col-md-3 col-sm-6">
        <div class="card card-custom p-3 bg-white h-100">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small text-uppercase fw-bold" style="font-size: 0.72rem; letter-spacing: 0.5px;">Need Reviewer</div>
                    <div class="fs-2 fw-bold text-dark mt-1">{{ $stats['need_reviewer'] ?? 0 }}</div>
                    <div class="small text-muted" style="font-size: 0.75rem;">Awaiting assignment</div>
                </div>
                <div class="stat-bubble">
                    <i class="bi bi-person-plus text-dark" style="font-size: 1.35rem;"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="card card-custom p-3 bg-white h-100">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small text-uppercase fw-bold" style="font-size: 0.72rem; letter-spacing: 0.5px;">Under Review</div>
                    <div class="fs-2 fw-bold text-dark mt-1">{{ $stats['under_review'] ?? 0 }}</div>
                    <div class="small text-muted" style="font-size: 0.75rem;">Being evaluated</div>
                </div>
                <div class="stat-bubble">
                    <i class="bi bi-eye text-dark" style="font-size: 1.35rem;"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="card card-custom p-3 bg-white h-100">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small text-uppercase fw-bold" style="font-size: 0.72rem; letter-spacing: 0.5px;">Total Projects</div>
                    <div class="fs-2 fw-bold text-dark mt-1">{{ $stats['total_projects'] ?? 0 }}</div>
                    <div class="small text-muted" style="font-size: 0.75rem;">University-wide</div>
                </div>
                <div class="stat-bubble">
                    <i class="bi bi-folder2 text-secondary" style="font-size: 1.35rem;"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Dean KPI Cards --}}
    @elseif($role === 'dean')
    <div class="col-md-3 col-sm-6">
        <div class="card card-custom p-3 bg-white h-100">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small text-uppercase fw-bold" style="font-size: 0.72rem; letter-spacing: 0.5px;">Pending Approval</div>
                    <div class="fs-2 fw-bold text-dark mt-1">{{ $stats['pending_approval'] ?? 0 }}</div>
                    <div class="small text-muted" style="font-size: 0.75rem;">Awaiting decision</div>
                </div>
                <div class="stat-bubble">
                    <i class="bi bi-hourglass-split text-dark" style="font-size: 1.35rem;"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="card card-custom p-3 bg-white h-100">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small text-uppercase fw-bold" style="font-size: 0.72rem; letter-spacing: 0.5px;">Approved</div>
                    <div class="fs-2 fw-bold text-dark mt-1">{{ $stats['approved'] ?? 0 }}</div>
                    <div class="small text-muted" style="font-size: 0.75rem;">Approved projects</div>
                </div>
                <div class="stat-bubble">
                    <i class="bi bi-check-circle text-dark" style="font-size: 1.35rem;"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="card card-custom p-3 bg-white h-100">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small text-uppercase fw-bold" style="font-size: 0.72rem; letter-spacing: 0.5px;">Total Value</div>
                    <div class="fs-2 fw-bold text-dark mt-1">{{ $stats['total_value'] ?? 0 }}</div>
                    <div class="small text-muted" style="font-size: 0.75rem;">ETB managed</div>
                </div>
                <div class="stat-bubble">
                    <i class="bi bi-cash-stack text-secondary" style="font-size: 1.35rem;"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- IRERC KPI Cards --}}
    @elseif($role === 'irerc')
    <div class="col-md-3 col-sm-6">
        <div class="card card-custom p-3 bg-white h-100">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small text-uppercase fw-bold" style="font-size: 0.72rem; letter-spacing: 0.5px;">Pending Ethics</div>
                    <div class="fs-2 fw-bold text-dark mt-1">{{ $stats['pending_ethics'] ?? 0 }}</div>
                    <div class="small text-muted" style="font-size: 0.75rem;">Ethical clearance</div>
                </div>
                <div class="stat-bubble">
                    <i class="bi bi-shield-exclamation text-dark" style="font-size: 1.35rem;"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="card card-custom p-3 bg-white h-100">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small text-uppercase fw-bold" style="font-size: 0.72rem; letter-spacing: 0.5px;">Cleared</div>
                    <div class="fs-2 fw-bold text-dark mt-1">{{ $stats['cleared'] ?? 0 }}</div>
                    <div class="small text-muted" style="font-size: 0.75rem;">Approved projects</div>
                </div>
                <div class="stat-bubble">
                    <i class="bi bi-shield-check text-dark" style="font-size: 1.35rem;"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="card card-custom p-3 bg-white h-100">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small text-uppercase fw-bold" style="font-size: 0.72rem; letter-spacing: 0.5px;">Total Reviews</div>
                    <div class="fs-2 fw-bold text-dark mt-1">{{ $stats['total_reviews'] ?? 0 }}</div>
                    <div class="small text-muted" style="font-size: 0.75rem;">Ethics reviews</div>
                </div>
                <div class="stat-bubble">
                    <i class="bi bi-clipboard-data text-dark" style="font-size: 1.35rem;"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- RCSC/VP KPI Cards --}}
    @elseif(in_array($role, ['vparttcs', 'rcsc']))
    <div class="col-md-3 col-sm-6">
        <div class="card card-custom p-3 bg-white h-100">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small text-uppercase fw-bold" style="font-size: 0.72rem; letter-spacing: 0.5px;">Pending Governance</div>
                    <div class="fs-2 fw-bold text-dark mt-1">{{ $stats['rcsc_pending'] ?? 0 }}</div>
                    <div class="small text-muted" style="font-size: 0.75rem;">Under review (&ge;500k)</div>
                </div>
                <div class="stat-bubble">
                    <i class="bi bi-shield-lock text-dark" style="font-size: 1.35rem;"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="card card-custom p-3 bg-white h-100">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small text-uppercase fw-bold" style="font-size: 0.72rem; letter-spacing: 0.5px;">Pending Contracts</div>
                    <div class="fs-2 fw-bold text-dark mt-1">{{ $stats['pending_contracts'] ?? 0 }}</div>
                    <div class="small text-muted" style="font-size: 0.75rem;">Awaiting VP signature</div>
                </div>
                <div class="stat-bubble">
                    <i class="bi bi-pen text-dark" style="font-size: 1.35rem;"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="card card-custom p-3 bg-white h-100">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small text-uppercase fw-bold" style="font-size: 0.72rem; letter-spacing: 0.5px;">Approved / Active</div>
                    <div class="fs-2 fw-bold text-dark mt-1">{{ $stats['approved'] ?? 0 }}</div>
                    <div class="small text-muted" style="font-size: 0.75rem;">Approved proposals</div>
                </div>
                <div class="stat-bubble">
                    <i class="bi bi-check-circle text-dark" style="font-size: 1.35rem;"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="card card-custom p-3 bg-white h-100">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small text-uppercase fw-bold" style="font-size: 0.72rem; letter-spacing: 0.5px;">High Budget</div>
                    <div class="fs-2 fw-bold text-dark mt-1">{{ $stats['high_budget'] ?? 0 }}</div>
                    <div class="small text-muted" style="font-size: 0.75rem;">&ge; 500k ETB projects</div>
                </div>
                <div class="stat-bubble">
                    <i class="bi bi-cash-coin text-dark" style="font-size: 1.35rem;"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Finance KPI Cards --}}
    @elseif($role === 'finance')
    <div class="col-md-3 col-sm-6">
        <div class="card card-custom p-3 bg-white h-100">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small text-uppercase fw-bold" style="font-size: 0.72rem; letter-spacing: 0.5px;">Pending Release</div>
                    <div class="fs-2 fw-bold text-dark mt-1">{{ $stats['pending_release'] ?? 0 }}</div>
                    <div class="small text-muted" style="font-size: 0.75rem;">Tranche queue</div>
                </div>
                <div class="stat-bubble">
                    <i class="bi bi-hourglass-split text-dark" style="font-size: 1.35rem;"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="card card-custom p-3 bg-white h-100">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small text-uppercase fw-bold" style="font-size: 0.72rem; letter-spacing: 0.5px;">Disbursed</div>
                    <div class="fs-2 fw-bold text-dark mt-1">{{ $stats['disbursed'] ?? 0 }}</div>
                    <div class="small text-muted" style="font-size: 0.75rem;">Released tranches</div>
                </div>
                <div class="stat-bubble">
                    <i class="bi bi-check-circle text-dark" style="font-size: 1.35rem;"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="card card-custom p-3 bg-white h-100">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small text-uppercase fw-bold" style="font-size: 0.72rem; letter-spacing: 0.5px;">Transactions</div>
                    <div class="fs-2 fw-bold text-dark mt-1">{{ $stats['transactions'] ?? 0 }}</div>
                    <div class="small text-muted" style="font-size: 0.75rem;">Total processed</div>
                </div>
                <div class="stat-bubble">
                    <i class="bi bi-receipt text-secondary" style="font-size: 1.35rem;"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Admin KPI Cards --}}
    @elseif($role === 'admin')
    <div class="col-md-3 col-sm-6">
        <div class="card card-custom p-3 bg-white h-100">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small text-uppercase fw-bold" style="font-size: 0.72rem; letter-spacing: 0.5px;">Total Users</div>
                    <div class="fs-2 fw-bold text-dark mt-1">{{ $stats['total_users'] ?? 0 }}</div>
                    <div class="small text-muted" style="font-size: 0.75rem;">System accounts</div>
                </div>
                <div class="stat-bubble">
                    <i class="bi bi-people text-secondary" style="font-size: 1.35rem;"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="card card-custom p-3 bg-white h-100">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small text-uppercase fw-bold" style="font-size: 0.72rem; letter-spacing: 0.5px;">Active Users</div>
                    <div class="fs-2 fw-bold text-dark mt-1">{{ $stats['active_users'] ?? 0 }}</div>
                    <div class="small text-muted" style="font-size: 0.75rem;">Currently active</div>
                </div>
                <div class="stat-bubble">
                    <i class="bi bi-person-check text-dark" style="font-size: 1.35rem;"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="card card-custom p-3 bg-white h-100">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small text-uppercase fw-bold" style="font-size: 0.72rem; letter-spacing: 0.5px;">Projects</div>
                    <div class="fs-2 fw-bold text-dark mt-1">{{ $stats['projects'] ?? 0 }}</div>
                    <div class="small text-muted" style="font-size: 0.75rem;">Total projects</div>
                </div>
                <div class="stat-bubble">
                    <i class="bi bi-folder2 text-dark" style="font-size: 1.35rem;"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="card card-custom p-3 bg-white h-100">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small text-uppercase fw-bold" style="font-size: 0.72rem; letter-spacing: 0.5px;">Audit Logs</div>
                    <div class="fs-2 fw-bold text-dark mt-1">{{ $stats['audit_logs'] ?? 0 }}</div>
                    <div class="small text-muted" style="font-size: 0.75rem;">System events</div>
                </div>
                <div class="stat-bubble">
                    <i class="bi bi-shield-check text-dark" style="font-size: 1.35rem;"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Admin Second Row --}}
    <div class="col-md-4 col-sm-6">
        <div class="card card-custom p-3 bg-white h-100">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small text-uppercase fw-bold" style="font-size: 0.72rem; letter-spacing: 0.5px;">Thematic Areas</div>
                    <div class="fs-2 fw-bold text-dark mt-1">{{ $stats['thematic_areas'] ?? 0 }}</div>
                    <div class="small text-muted" style="font-size: 0.75rem;">Research categories</div>
                </div>
                <div class="stat-bubble">
                    <i class="bi bi-diagram-3 text-dark" style="font-size: 1.35rem;"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4 col-sm-6">
        <div class="card card-custom p-3 bg-white h-100">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small text-uppercase fw-bold" style="font-size: 0.72rem; letter-spacing: 0.5px;">Departments</div>
                    <div class="fs-2 fw-bold text-dark mt-1">{{ $stats['departments'] ?? 0 }}</div>
                    <div class="small text-muted" style="font-size: 0.75rem;">Academic units</div>
                </div>
                <div class="stat-bubble">
                    <i class="bi bi-building text-dark" style="font-size: 1.35rem;"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4 col-sm-6">
        <div class="card card-custom p-3 bg-white h-100">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small text-uppercase fw-bold" style="font-size: 0.72rem; letter-spacing: 0.5px;">Evaluations</div>
                    <div class="fs-2 fw-bold text-dark mt-1">{{ $stats['evaluations'] ?? 0 }}</div>
                    <div class="small text-muted" style="font-size: 0.75rem;">Peer reviews</div>
                </div>
                <div class="stat-bubble">
                    <i class="bi bi-clipboard-check text-dark" style="font-size: 1.35rem;"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- TM KPI Cards --}}
    @elseif($role === 'tm')
    <div class="col-md-3 col-sm-6">
        <div class="card card-custom p-3 bg-white h-100">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small text-uppercase fw-bold" style="font-size: 0.72rem; letter-spacing: 0.5px;">Team Projects</div>
                    <div class="fs-2 fw-bold text-dark mt-1">{{ $stats['team_projects'] ?? 0 }}</div>
                    <div class="small text-muted" style="font-size: 0.75rem;">Assigned to me</div>
                </div>
                <div class="stat-bubble">
                    <i class="bi bi-people text-secondary" style="font-size: 1.35rem;"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="card card-custom p-3 bg-white h-100">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small text-uppercase fw-bold" style="font-size: 0.72rem; letter-spacing: 0.5px;">Active Projects</div>
                    <div class="fs-2 fw-bold text-dark mt-1">{{ $stats['active_projects'] ?? 0 }}</div>
                    <div class="small text-muted" style="font-size: 0.75rem;">Currently executing</div>
                </div>
                <div class="stat-bubble">
                    <i class="bi bi-play-circle text-dark" style="font-size: 1.35rem;"></i>
                </div>
            </div>
        </div>
    </div>

    @endif
</div>

@if($role === 'pi')
    <div class="card card-custom p-4 mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold mb-0"><i class="bi bi-journal-text me-2 text-dark"></i>My Research Proposals</h5>
        </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Project Title</th>
                    <th>Thematic Area</th>
                    <th>Budget</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($myProjects as $p)
                <tr>
                    <td class="font-monospace text-muted">#{{ $p->project_id }}</td>
                    <td class="fw-bold text-dark">{{ $p->title }}</td>
                    <td><span class="badge bg-light text-dark border">{{ $p->thematicArea ? $p->thematicArea->title : 'General' }}</span></td>
                    <td class="fw-semibold text-dark">{{ number_format($p->requested_budget, 2) }} ETB</td>
                    <td>
                        @if($p->status === 'Draft')
                            <span class="badge bg-secondary text-white px-2 py-1"><i class="bi bi-pencil me-1"></i>Draft</span>
                        @elseif($p->status === 'Returned')
                            <span class="badge bg-warning text-dark px-2 py-1"><i class="bi bi-arrow-return-left me-1"></i>Returned</span>
                        @elseif($p->status === 'Approved')
                            <span class="badge bg-success text-white px-2 py-1"><i class="bi bi-check-circle me-1"></i>Approved</span>
                            @if($p->irercClearance)
                                @if($p->irercClearance->status === 'Approved')
                                    <div class="mt-1"><span class="badge bg-success text-white px-2 py-1" style="font-size: 0.72rem;"><i class="bi bi-shield-check me-1"></i>Ethics Cleared</span></div>
                                @elseif($p->irercClearance->status === 'Rejected')
                                    <div class="mt-1"><span class="badge bg-danger text-white px-2 py-1" style="font-size: 0.72rem;"><i class="bi bi-shield-x me-1"></i>Ethics Rejected</span></div>
                                @else
                                    <div class="mt-1"><span class="badge bg-warning text-dark px-2 py-1" style="font-size: 0.72rem;"><i class="bi bi-shield-exclamation me-1"></i>Ethics Pending</span></div>
                                @endif
                            @endif
                        @elseif($p->status === 'Completed')
                            <span class="badge bg-success text-white px-2 py-1"><i class="bi bi-check-circle me-1"></i>Completed</span>
                        @elseif($p->status === 'Active')
                            <span class="badge bg-primary text-white px-2 py-1"><i class="bi bi-play-circle me-1"></i>Active</span>
                        @elseif(in_array($p->status, ['Submitted', 'DH_Screened', 'UnderReview', 'Dean_Review', 'RCSC_Review']))
                            @if($p->status === 'Dean_Review')
                                <span class="badge bg-info text-dark px-2 py-1"><i class="bi bi-bank me-1"></i>Dean Review</span>
                            @elseif($p->status === 'RCSC_Review')
                                <span class="badge bg-danger text-white px-2 py-1"><i class="bi bi-bank me-1"></i>RCSC Review</span>
                            @else
                                <span class="badge bg-warning text-dark px-2 py-1"><i class="bi bi-hourglass-split me-1"></i>{{ $p->status }}</span>
                            @endif
                            @if($p->irercClearance)
                                @if($p->irercClearance->status === 'Approved')
                                    <div class="mt-1"><span class="badge bg-success text-white px-2 py-1" style="font-size: 0.72rem;"><i class="bi bi-shield-check me-1"></i>Ethics Cleared</span></div>
                                @elseif($p->irercClearance->status === 'Rejected')
                                    <div class="mt-1"><span class="badge bg-danger text-white px-2 py-1" style="font-size: 0.72rem;"><i class="bi bi-shield-x me-1"></i>Ethics Rejected</span></div>
                                @else
                                    <div class="mt-1"><span class="badge bg-warning text-dark px-2 py-1" style="font-size: 0.72rem;"><i class="bi bi-shield-exclamation me-1"></i>Ethics Pending</span></div>
                                @endif
                            @endif
                        @elseif($p->status === 'Rejected' || $p->status === 'Terminated')
                            <span class="badge bg-danger text-white px-2 py-1"><i class="bi bi-x-circle me-1"></i>{{ $p->status }}</span>
                        @else
                            <span class="badge bg-secondary text-white px-2 py-1">{{ $p->status }}</span>
                        @endif
                    </td>
                    <td>
                        <div class="d-flex gap-2 align-items-center flex-wrap">
                            @if(in_array($p->status, ['Draft', 'Returned']))
                                <form action="{{ route('projects.submit', $p->project_id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="button" class="btn btn-sm btn-success fw-bold confirm-btn" data-confirm-title="Submit Proposal" data-confirm-message="This proposal will be sent to the Department Head for initial screening. You won't be able to edit it after submission." data-confirm-icon="bi-send" data-confirm-color="text-dark" data-confirm-btn-text="Yes, Submit" data-confirm-btn-class="btn-success"><i class="bi bi-send me-1"></i>Submit</button>
                                </form>
                            @endif
                            @if($p->status === 'Approved' && (!$p->irercClearance || $p->irercClearance->status === 'Approved') && (!$p->pi_signature_date || !$p->vp_signature_date))
                            <a href="{{ route('contracts.show', $p->project_id) }}" class="btn btn-sm btn-primary fw-bold text-white shadow-sm">
                                <i class="bi bi-pen me-1"></i> Sign Contract
                            </a>
                            @endif
                            <a href="{{ route('projects.show', $p->project_id) }}" class="btn btn-sm btn-outline-secondary">
                                <i class="bi bi-arrow-right-circle me-1"></i> View Details
                            </a>
                            @if($p->proposal_document_url)
                            <a href="{{ Storage::url($p->proposal_document_url) }}" target="_blank" class="btn btn-sm btn-outline-danger" title="View Document">
                                <i class="bi bi-file-pdf"></i>
                            </a>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted py-4">
                        <i class="bi bi-inbox fs-3 d-block text-muted mb-2"></i>
                        No project proposals submitted yet.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endif

@if($role === 'reviewer')
<div class="card card-custom p-4 mb-4">
    <h5 class="fw-bold mb-3"><i class="bi bi-eye-slash me-2 text-dark"></i>Assigned Blind Peer Evaluations</h5>
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Eval ID</th>
                    <th>Proposal Title</th>
                    <th>Status</th>
                    <th>Score</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($assignedReviews as $rev)
                <tr>
                    <td class="font-monospace text-muted">#EV-{{ $rev->eval_id }}</td>
                    <td class="fw-bold text-dark">{{ $rev->project->title }}</td>
                    <td>
                        <span class="badge bg-light text-dark border px-2 py-1">{{ $rev->decision }}</span>
                    </td>
                    <td><span class="fw-bold text-dark">{{ $rev->score }}</span> <span class="text-muted small">/ 100</span></td>
                    <td>
                        <a href="{{ route('projects.show', $rev->project_id) }}" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-pencil-square me-1"></i> Score &amp; Critique
                        </a>
                        @if($rev->project->proposal_document_url)
                        <a href="{{ Storage::url($rev->project->proposal_document_url) }}" target="_blank" class="btn btn-sm btn-outline-danger" title="View Document">
                            <i class="bi bi-file-pdf"></i>
                        </a>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center text-muted py-4">
                        <i class="bi bi-check2-circle fs-3 d-block text-secondary mb-2"></i>
                        No pending peer evaluations assigned to you.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endif

@if(in_array($role, ['coordinator', 'dean', 'vparttcs', 'rcsc', 'dh', 'irerc']))
<div class="card card-custom p-4 mb-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold mb-0"><i class="bi bi-shield-check me-2 text-dark"></i>Institutional Governance Queue</h5>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>PI Name</th>
                    <th>Requested Budget</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pendingApprovals as $p)
                <tr>
                    <td class="font-monospace text-muted">#{{ $p->project_id }}</td>
                    <td class="fw-bold text-dark">{{ $p->title }}</td>
                    <td>
                        <span class="d-inline-flex align-items-center"><i class="bi bi-person-circle me-1 text-secondary"></i> {{ preg_replace('/\s*\([^)]*\)/', '', $p->pi->name) }}</span>
                    </td>
                    <td>
                        <div class="fw-bold text-dark">{{ number_format($p->requested_budget, 2) }} ETB</div>
                        @if($p->requested_budget >= 500000)
                            <span class="badge bg-light text-dark border mt-1" style="font-size:0.7rem;">RCSC Tier (&ge;500k)</span>
                        @else
                            <span class="badge bg-light text-secondary border mt-1" style="font-size:0.7rem;">Dean Tier (&lt;500k)</span>
                        @endif
                    </td>
                    <td>
                        <span class="badge bg-light text-dark border px-2 py-1">{{ $p->status }}</span>
                    </td>
                    <td>
                        @if($p->status === 'Dean_Review' && in_array($role, ['dean', 'vparttcs', 'admin']))
                        <a href="{{ route('projects.show', $p->project_id) }}" class="btn btn-sm btn-success text-white fw-bold">
                            <i class="bi bi-bank me-1"></i> Ratify Budget
                        </a>
                        @elseif($p->status === 'RCSC_Review' && in_array($role, ['rcsc', 'vparttcs', 'admin']))
                        <a href="{{ route('projects.show', $p->project_id) }}" class="btn btn-sm btn-danger text-white fw-bold">
                            <i class="bi bi-bank me-1"></i> Ratify Budget
                        </a>
                        @endif
                        @if($p->status === 'Approved' && in_array($role, ['vparttcs', 'admin']) && (!$p->irercClearance || $p->irercClearance->status === 'Approved'))
                        <a href="{{ route('contracts.show', $p->project_id) }}" class="btn btn-sm btn-danger text-white fw-bold">
                            <i class="bi bi-pen me-1"></i> Sign Contract
                        </a>
                        @endif
                        @if(in_array($p->status, ['Active', 'Approved', 'Completed']) && in_array($role, ['coordinator', 'dh', 'admin']))
                        <a href="{{ route('progress.show', $p->project_id) }}" class="btn btn-sm btn-outline-dark fw-semibold">
                            <i class="bi bi-graph-up me-1"></i> Progress
                        </a>
                        @endif
                        <a href="{{ route('projects.show', $p->project_id) }}" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-sliders me-1"></i> Review &amp; Governance
                        </a>
                        @if($p->proposal_document_url)
                        <a href="{{ Storage::url($p->proposal_document_url) }}" target="_blank" class="btn btn-sm btn-outline-danger" title="View Document">
                            <i class="bi bi-file-pdf"></i>
                        </a>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted py-4">
                        <i class="bi bi-inbox fs-3 d-block text-muted mb-2"></i>
                        Queue empty.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endif

@if($role === 'finance')
{{-- Pending Tranche Disbursements Queue --}}
<div class="card card-custom p-4 mb-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold mb-0"><i class="bi bi-cash-stack me-2 text-dark"></i>Pending Tranche Disbursements ({{ $pendingDisbursements->count() }})</h5>
        <a href="{{ route('finance.disbursement') }}" class="btn btn-sm btn-outline-dark fw-semibold">
            <i class="bi bi-box-arrow-up-right me-1"></i> Full Finance Portal
        </a>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Request ID</th>
                    <th>Project</th>
                    <th>PI Name</th>
                    <th>Tranche Phase</th>
                    <th>Approved Amount</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pendingDisbursements as $req)
                <tr>
                    <td class="font-monospace text-muted">#REQ-{{ $req->request_id }}</td>
                    <td class="fw-bold text-dark">{{ $req->project->title ?? 'N/A' }}</td>
                    <td>
                        <span class="d-inline-flex align-items-center"><i class="bi bi-person-circle me-1 text-secondary"></i> {{ preg_replace('/\s*\([^)]*\)/', '', $req->project->pi->name ?? 'N/A') }}</span>
                    </td>
                    <td>
                        @if($req->milestone_phase === 'Tranche 1')
                            <span class="badge bg-dark text-white px-2 py-1"><i class="bi bi-1-circle me-1"></i>Tranche 1 (30% Advance)</span>
                        @elseif($req->milestone_phase === 'Tranche 2')
                            <span class="badge bg-primary text-white px-2 py-1"><i class="bi bi-2-circle me-1"></i>Tranche 2 (40% Mid-Term)</span>
                        @elseif($req->milestone_phase === 'Tranche 3')
                            <span class="badge bg-info text-dark px-2 py-1"><i class="bi bi-3-circle me-1"></i>Tranche 3 (30% Final)</span>
                        @else
                            <span class="badge bg-secondary text-white px-2 py-1">{{ $req->milestone_phase }}</span>
                        @endif
                    </td>
                    <td class="fw-bold text-dark">{{ number_format($req->approved_amount ?? 0, 2) }} ETB</td>
                    <td><span class="badge bg-light text-dark border px-2 py-1">{{ $req->status }}</span></td>
                    <td>
                        <button class="btn btn-sm btn-success fw-bold text-white shadow-sm" data-bs-toggle="modal"
                                data-bs-target="#dashDisburseModal{{ $req->request_id }}">
                            <i class="bi bi-cash me-1"></i> Disburse
                        </button>
                    </td>
                </tr>

                {{-- Dashboard Disbursement Modal --}}
                <div class="modal fade" id="dashDisburseModal{{ $req->request_id }}" tabindex="-1">
                    <div class="modal-dialog modal-dialog-centered" style="max-width: 460px;">
                        <div class="modal-content border-0 shadow">
                            <form action="{{ route('finance.process-disbursement', $req->request_id) }}" method="POST">
                                @csrf
                                <div class="modal-header py-2 px-3 bg-dark text-white border-bottom">
                                    <h6 class="modal-title fw-bold text-white small mb-0">
                                        <i class="bi bi-cash me-1 text-success"></i> Release Disbursement &bull; {{ $req->milestone_phase ?? 'Tranche' }}
                                    </h6>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body p-3">
                                    {{-- Project Summary Snippet --}}
                                    <div class="p-2 mb-2 bg-light rounded border small">
                                        <div class="fw-bold text-dark text-truncate">{{ $req->project->title ?? 'N/A' }}</div>
                                        <div class="d-flex justify-content-between text-muted mt-1" style="font-size: 0.78rem;">
                                            <span><i class="bi bi-person me-1"></i>{{ $req->project->pi->name ?? 'N/A' }}</span>
                                            <span class="fw-bold text-dark">{{ number_format($req->approved_amount ?? 0, 2) }} ETB</span>
                                        </div>
                                    </div>

                                    <div class="row g-2 mb-2">
                                        <div class="col-6">
                                            <label class="form-label small fw-bold mb-1">Approved Amount (ETB)</label>
                                            <div class="input-group input-group-sm">
                                                <input type="text" class="form-control form-control-sm fw-bold bg-light"
                                                       value="{{ number_format($req->approved_amount, 2) }} ETB" readonly tabindex="-1">
                                                <span class="input-group-text bg-light text-muted"><i class="bi bi-lock-fill"></i></span>
                                            </div>
                                            <input type="hidden" name="amount" value="{{ $req->approved_amount }}">
                                            <small class="text-muted" style="font-size:0.7rem;">Locked to ratified tranche budget</small>
                                        </div>
                                        <div class="col-6">
                                            <label class="form-label small fw-bold mb-1">Method <span class="text-danger">*</span></label>
                                            <select name="payment_method" class="form-select form-select-sm @error('payment_method') is-invalid @enderror" required>
                                                <option value="Bank Transfer" selected>Bank Transfer</option>
                                                <option value="Check">Check</option>
                                                <option value="Cash">Cash</option>
                                            </select>
                                            @error('payment_method')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                    </div>

                                    <div class="mb-2">
                                        <label class="form-label small fw-bold mb-1">Reference / Note</label>
                                        <input type="text" name="notes" class="form-control form-control-sm @error('notes') is-invalid @enderror"
                                               placeholder="e.g. CBE Ref #GMU-2026-9812" value="{{ old('notes') }}">
                                        @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                                <div class="modal-footer py-2 px-3 bg-light border-top d-flex justify-content-between">
                                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-sm btn-success fw-bold px-3">
                                        <i class="bi bi-check-lg me-1"></i> Release Funds
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">
                        <i class="bi bi-check2-circle fs-3 d-block text-secondary mb-2"></i>
                        No pending tranche disbursements. All approved tranches are currently released.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Recent Disbursement History --}}
@if($disbursedHistory->count() > 0)
<div class="card card-custom p-4 mb-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold mb-0"><i class="bi bi-clock-history me-2 text-dark"></i>Recent Disbursement Transactions</h5>
        <a href="{{ route('finance.disbursement') }}" class="btn btn-sm btn-link text-decoration-none">View All History &rarr;</a>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Request ID</th>
                    <th>Project</th>
                    <th>PI</th>
                    <th>Tranche Phase</th>
                    <th>Released Amount</th>
                    <th>Method</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach($disbursedHistory as $hist)
                <tr>
                    <td class="font-monospace text-muted">#REQ-{{ $hist->request_id }}</td>
                    <td class="fw-bold text-dark">{{ $hist->project->title ?? 'N/A' }}</td>
                    <td>{{ $hist->project->pi->name ?? 'N/A' }}</td>
                    <td><span class="badge bg-light text-dark border">{{ $hist->milestone_phase ?? 'Tranche' }}</span></td>
                    <td class="fw-bold text-dark">{{ number_format($hist->approved_amount ?? 0, 2) }} ETB</td>
                    <td><span class="badge bg-info text-dark">{{ $hist->payment_method ?? 'N/A' }}</span></td>
                    <td>{{ $hist->disbursed_at ? $hist->disbursed_at->format('M d, Y') : 'N/A' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif
@endif

@if($role === 'tm')
<div class="card card-custom p-4 mb-4">
    <h5 class="fw-bold mb-3"><i class="bi bi-people me-2 text-dark"></i>My Team Projects</h5>
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Project ID</th>
                    <th>Project Title</th>
                    <th>PI</th>
                    <th>My Role</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($teamProjects as $member)
                <tr>
                    <td class="font-monospace text-muted">#{{ $member->project->project_id }}</td>
                    <td class="fw-bold text-dark">{{ $member->project->title }}</td>
                    <td>
                        <span class="d-inline-flex align-items-center"><i class="bi bi-person-circle me-1 text-secondary"></i> {{ preg_replace('/\s*\([^)]*\)/', '', $member->project->pi->name ?? 'N/A') }}</span>
                    </td>
                    <td><span class="badge bg-info text-dark">{{ $member->role_in_project }}</span></td>
                    <td>
                        @if($member->project->status === 'Approved' || $member->project->status === 'Completed')
                            <span class="badge bg-success text-white px-2 py-1">{{ $member->project->status }}</span>
                        @elseif($member->project->status === 'Active')
                            <span class="badge bg-primary text-white px-2 py-1">{{ $member->project->status }}</span>
                        @else
                            <span class="badge bg-secondary text-white px-2 py-1">{{ $member->project->status }}</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('projects.show', $member->project->project_id) }}" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-arrow-right-circle me-1"></i> View Details
                        </a>
                        @if($member->project->proposal_document_url)
                        <a href="{{ Storage::url($member->project->proposal_document_url) }}" target="_blank" class="btn btn-sm btn-outline-danger" title="View Document">
                            <i class="bi bi-file-pdf"></i>
                        </a>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted py-4">
                        <i class="bi bi-people fs-3 d-block text-secondary mb-2"></i>
                        You are not assigned to any team projects yet.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endif

@endsection
