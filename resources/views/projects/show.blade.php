@extends('layouts.app')

@section('content')
{{-- ── Breadcrumbs ────────────────────────────────────────────────────────── --}}
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb breadcrumb-custom px-3 py-2 rounded bg-light">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none"><i class="bi bi-house-door me-1"></i>Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('projects.index') }}" class="text-decoration-none">Projects</a></li>
        <li class="breadcrumb-item active" aria-current="page">Project #{{ $project->project_id }}</li>
    </ol>
</nav>

{{-- ── Page Header ────────────────────────────────────────────────────────── --}}
<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <h2 class="fw-bold mb-1 text-dark">{{ $project->title }}</h2>
        <div class="d-flex align-items-center flex-wrap gap-2">
            <span class="text-muted small">ID: {{ $project->project_id }}</span>
            @if(in_array($project->status, ['Draft', 'Withdrawn']))
                <span class="badge bg-secondary fs-6"><i class="bi bi-pencil me-1"></i>{{ $project->status }}</span>
                @if(Auth::user()->role === 'pi')
                <form action="{{ route('projects.submit', $project->project_id) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="button" class="btn btn-success btn-sm fw-bold confirm-btn" data-confirm-title="Submit Proposal" data-confirm-message="This proposal will be sent to the Department Head for initial screening. You won't be able to edit it after submission." data-confirm-icon="bi-send" data-confirm-color="text-success" data-confirm-btn-text="Yes, Submit" data-confirm-btn-class="btn-success"><i class="bi bi-send me-1"></i>Submit for Review</button>
                </form>
                @endif
            @elseif($project->status === 'Approved' || $project->status === 'Completed')
                <span class="badge bg-success fs-6"><i class="bi bi-check-circle me-1"></i>{{ $project->status }}</span>
            @elseif($project->status === 'Active')
                <span class="badge bg-primary fs-6"><i class="bi bi-play-circle me-1"></i>{{ $project->status }}</span>
            @elseif(in_array($project->status, ['Submitted', 'DH_Screened', 'UnderReview']))
                <span class="badge bg-warning text-dark fs-6"><i class="bi bi-hourglass-split me-1"></i>{{ $project->status }}</span>
            @elseif($project->status === 'Rejected' || $project->status === 'Terminated')
                <span class="badge bg-danger fs-6"><i class="bi bi-x-circle me-1"></i>{{ $project->status }}</span>
            @elseif($project->status === 'Withdrawn')
                <span class="badge bg-dark fs-6"><i class="bi bi-x-circle me-1"></i>Withdrawn</span>
            @elseif($project->status === 'PendingCancellation')
                <span class="badge bg-warning text-dark fs-6"><i class="bi bi-hourglass-split me-1"></i>Pending Withdrawal</span>
            @else
                <span class="badge bg-secondary fs-6">{{ $project->status }}</span>
            @endif
            @if($project->requested_budget >= 500000)
                <span class="badge bg-danger fs-6">RCSC Governance Tier (≥500k ETB)</span>
            @else
                <span class="badge bg-info text-dark fs-6">College Dean Tier (&lt;500k ETB)</span>
            @endif
            <span class="text-muted small ms-auto">
                <i class="bi bi-clock me-1"></i>Last updated {{ $project->updated_at ? $project->updated_at->diffForHumans() : $project->created_at->diffForHumans() }}
            </span>
        </div>
    </div>
    <a href="{{ route('projects.index') }}" class="btn btn-outline-secondary btn-sm flex-shrink-0">
        <i class="bi bi-arrow-left me-1"></i> Back to Projects
    </a>
</div>

{{-- ── Project Lifecycle Status Timeline (SDD Figure 6) ─────────────────── --}}
@php
    $stages = [
        'Submitted'   => ['icon' => 'bi-send-fill',          'label' => 'Submitted',    'color' => 'secondary'],
        'DH_Screened' => ['icon' => 'bi-clipboard-check',    'label' => 'DH Screened',  'color' => 'info'],
        'UnderReview' => ['icon' => 'bi-eye-fill',           'label' => 'Peer Review',  'color' => 'primary'],
        'Approved'    => ['icon' => 'bi-patch-check-fill',   'label' => 'Approved',     'color' => 'success'],
        'Active'      => ['icon' => 'bi-activity',           'label' => 'Active',       'color' => 'success'],
        'Completed'   => ['icon' => 'bi-trophy-fill',        'label' => 'Completed',    'color' => 'warning'],
    ];
    $currentStatus = $project->status;
    $stageKeys = array_keys($stages);
    $currentIndex = array_search($currentStatus, $stageKeys);
    $dates = [
        'Submitted'   => $project->created_at,
        'DH_Screened' => $project->dh_screened_at ?? null,
        'UnderReview' => $project->under_review_at ?? null,
        'Approved'    => $project->approved_at ?? null,
        'Active'      => $project->activated_at ?? null,
        'Completed'   => $project->completed_at ?? null,
    ];
@endphp

<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-white border-bottom py-3">
        <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-diagram-3 me-2 text-success"></i>Project Lifecycle</h6>
    </div>
    <div class="card-body p-4">
        <div class="timeline-container">
            @foreach($stages as $key => $stage)
            @php
                $idx = array_search($key, $stageKeys);
                $isDone    = ($currentIndex !== false && $idx < $currentIndex) || $currentStatus === $key;
                $isCurrent = ($currentStatus === $key);
            @endphp
            <div class="timeline-item">
                <div class="timeline-circle {{ $isCurrent ? 'current' : ($isDone ? 'completed' : '') }}">
                    <i class="bi {{ $stage['icon'] }}"></i>
                </div>
                <div class="timeline-label {{ $isCurrent ? 'active' : '' }}">
                    {{ $stage['label'] }}
                </div>
                @if($dates[$key])
                    <div class="timeline-date">
                        {{ $dates[$key]->format('M d, Y') }}
                    </div>
                @endif
            </div>
            @endforeach
        </div>
    </div>
</div>

<div class="row g-4">
    {{-- ── Sidebar: Quick Actions ────────────────────────────────────────────── --}}
    <div class="col-lg-3 order-lg-2">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white border-bottom py-3">
                <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-lightning me-2 text-warning"></i>Quick Actions</h6>
            </div>
            <div class="card-body p-3">
                {{-- Submit --}}
                @if($project->status === 'Draft' && Auth::user()->role === 'pi')
                <form action="{{ route('projects.submit', $project->project_id) }}" method="POST" class="mb-2">
                    @csrf
                    <button type="button" class="btn w-100 text-start confirm-btn fw-bold" style="background:#212529;color:#fff;border:none;" onmouseover="this.style.background='#495057'" onmouseout="this.style.background='#212529'" data-confirm-title="Submit Proposal" data-confirm-message="This proposal will be sent to the Department Head for initial screening." data-confirm-icon="bi-send" data-confirm-color="text-success" data-confirm-btn-text="Yes, Submit" data-confirm-btn-class="btn-success">
                        <i class="bi bi-send me-2"></i>Submit for Review
                    </button>
                </form>
                @endif

                {{-- Edit --}}
                @if(in_array($project->status, ['Draft', 'Withdrawn']) && Auth::user()->role === 'pi')
                <a href="{{ route('projects.edit', $project->project_id) }}" class="btn w-100 mb-2 text-start fw-bold" style="background:#fff;color:#212529;border:1.5px solid #dee2e6;" onmouseover="this.style.background='#212529';this.style.color='#fff';this.style.borderColor='#212529'" onmouseout="this.style.background='#fff';this.style.color='#212529';this.style.borderColor='#dee2e6'">
                    <i class="bi bi-pencil-square me-2"></i>Edit Proposal
                </a>
                @endif

                @if(in_array($project->status, ['Draft', 'Withdrawn', 'Submitted', 'DH_Screened', 'UnderReview']) && Auth::user()->role === 'pi')
                <hr class="my-2">
                @endif

                {{-- Withdraw --}}
                @if(Auth::user()->role === 'pi' && in_array($project->status, ['Submitted', 'DH_Screened', 'UnderReview']))
                <form action="{{ route('projects.cancel', $project->project_id) }}" method="POST" class="mb-2">
                    @csrf
                    <button type="button" class="btn w-100 text-start confirm-btn fw-bold" style="background:#fff;color:#e67700;border:1.5px solid #e67700;" onmouseover="this.style.background='#e67700';this.style.color='#fff'" onmouseout="this.style.background='#fff';this.style.color='#e67700'" data-confirm-title="Withdraw Proposal" data-confirm-message="Are you sure you want to withdraw this proposal?" data-confirm-icon="bi-x-circle" data-confirm-color="text-warning" data-confirm-btn-text="Yes, Withdraw" data-confirm-btn-class="btn-warning">
                        <i class="bi bi-x-circle me-2"></i>Withdraw Proposal
                    </button>
                </form>
                @endif

                {{-- Delete --}}
                @if(in_array($project->status, ['Draft', 'Withdrawn']) && Auth::user()->role === 'pi')
                <form action="{{ route('projects.destroy', $project->project_id) }}" method="POST" class="mb-2">
                    @csrf
                    @method('DELETE')
                    <button type="button" class="btn w-100 text-start confirm-btn fw-bold" style="background:#212529;color:#fff;border:1.5px solid #dc3545;" onmouseover="this.style.background='#dc3545';this.style.borderColor='#dc3545'" onmouseout="this.style.background='#212529';this.style.borderColor='#dc3545'" data-confirm-title="Delete Proposal" data-confirm-message="This will permanently delete this draft proposal." data-confirm-icon="bi-trash" data-confirm-color="text-danger" data-confirm-btn-text="Yes, Delete" data-confirm-btn-class="btn-danger">
                        <i class="bi bi-trash me-2"></i>Delete Draft
                    </button>
                </form>
                @endif

                {{-- Extend --}}
                @if(in_array($project->status, ['Active', 'Approved']) && in_array(Auth::user()->role, ['pi', 'coordinator', 'admin']))
                <button class="btn w-100 mb-2 text-start fw-bold" style="background:#fff;color:#e67700;border:1.5px solid #e67700;" onmouseover="this.style.background='#e67700';this.style.color='#fff'" onmouseout="this.style.background='#fff';this.style.color='#e67700'" type="button" data-bs-toggle="collapse" data-bs-target="#extensionForm">
                    <i class="bi bi-hourglass-split me-2"></i>Request Extension
                </button>
                @endif

                {{-- Terminate --}}
                @if(!in_array($project->status, ['Completed', 'Terminated', 'Draft', 'Submitted', 'Withdrawn', 'PendingCancellation']) && in_array(Auth::user()->role, ['pi', 'coordinator', 'admin']))
                <button class="btn w-100 mb-2 text-start fw-bold" style="background:#fff;color:#dc3545;border:1.5px solid #dc3545;" onmouseover="this.style.background='#dc3545';this.style.color='#fff'" onmouseout="this.style.background='#fff';this.style.color='#dc3545'" type="button" data-bs-toggle="collapse" data-bs-target="#terminateForm">
                    <i class="bi bi-x-octagon me-2"></i>Terminate Project
                </button>
                @endif

                {{-- Mark Complete --}}
                @if($project->status === 'Active' && in_array(Auth::user()->role, ['coordinator', 'admin']))
                <form action="{{ route('projects.complete', $project->project_id) }}" method="POST" class="mb-2">
                    @csrf
                    <button type="button" class="btn w-100 text-start confirm-btn fw-bold" style="background:#198754;color:#fff;border:none;" onmouseover="this.style.background='#157347'" onmouseout="this.style.background='#198754'" data-confirm-title="Mark Complete" data-confirm-message="This will mark the project as Completed. A certificate can then be issued." data-confirm-icon="bi-trophy" data-confirm-color="text-success" data-confirm-btn-text="Yes, Complete" data-confirm-btn-class="btn-success">
                        <i class="bi bi-trophy me-2"></i>Mark Complete
                    </button>
                </form>
                @endif

                {{-- Simple Cancel (Submitted/DH_Screened/UnderReview) --}}
                @if(Auth::user()->role === 'pi' && in_array($project->status, ['Submitted', 'DH_Screened', 'UnderReview']))
                <form action="{{ route('projects.cancel', $project->project_id) }}" method="POST">
                    @csrf
                    <button type="button" class="btn w-100 mb-2 text-start confirm-btn fw-bold" style="background:#fff;color:#e67700;border:1.5px solid #e67700;" onmouseover="this.style.background='#e67700';this.style.color='#fff'" onmouseout="this.style.background='#fff';this.style.color='#e67700'" data-confirm-title="Cancel Proposal" data-confirm-message="Are you sure you want to cancel this proposal?" data-confirm-icon="bi-x-circle" data-confirm-color="text-warning" data-confirm-btn-text="Yes, Cancel" data-confirm-btn-class="btn-warning">
                        <i class="bi bi-x-circle me-2"></i>Cancel Proposal
                    </button>
                </form>
                @endif

                {{-- Request Withdrawal (Approved/Active - needs admin approval) --}}
                @if(Auth::user()->role === 'pi' && in_array($project->status, ['Approved', 'Active']))
                <button class="btn w-100 mb-2 text-start fw-bold" style="background:#fff;color:#e67700;border:1.5px solid #e67700;" onmouseover="this.style.background='#e67700';this.style.color='#fff'" onmouseout="this.style.background='#fff';this.style.color='#e67700'" type="button" data-bs-toggle="collapse" data-bs-target="#requestCancelForm">
                    <i class="bi bi-x-circle me-2"></i>Request Withdrawal
                </button>
                @endif

                <hr class="my-3">

                <div class="text-center mt-3">
                    <small class="text-muted">Created {{ $project->created_at ? $project->created_at->format('M d, Y') : 'N/A' }}</small>
                    <br>
                    <small class="text-muted">Updated {{ $project->updated_at ? $project->updated_at->diffForHumans() : 'N/A' }}</small>
                </div>
            </div>
        </div>

        {{-- Extension Form (collapsible) --}}
        @if(in_array($project->status, ['Active', 'Approved']) && in_array(Auth::user()->role, ['pi', 'coordinator', 'admin']))
        <div class="collapse mb-4" id="extensionForm">
            <div class="card shadow-sm border-0 border-start border-warning border-4">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-2"><i class="bi bi-hourglass-split me-2 text-warning"></i>Request Time Extension</h6>
                    <p class="small text-muted mb-3">Current extensions: <strong>{{ $project->extensions()->count() }} / 3</strong></p>
                    <form method="POST" action="{{ route('projects.request-extension', $project->project_id) }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Months (Max 6)</label>
                            <input type="number" min="1" max="6" name="months" class="form-control" value="6" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Justification</label>
                            <textarea name="reason" rows="3" class="form-control" required placeholder="Reason for extension request..."></textarea>
                        </div>
                        <button type="submit" class="btn btn-warning w-100 fw-bold confirm-btn" data-confirm-title="Submit Extension" data-confirm-message="Submit this extension request for approval?" data-confirm-icon="bi-clock-history" data-confirm-color="text-warning" data-confirm-btn-text="Yes, Submit" data-confirm-btn-class="btn-warning">Submit Extension Request</button>
                    </form>
                </div>
            </div>
        </div>
        @endif

        {{-- Terminate Form (collapsible) --}}
        @if(!in_array($project->status, ['Completed', 'Terminated', 'Draft', 'Submitted']) && in_array(Auth::user()->role, ['pi', 'coordinator', 'admin']))
        <div class="collapse mb-4" id="terminateForm">
            <div class="card shadow-sm border-0 border-start border-danger border-4">
                <div class="card-body p-4">
                    <h6 class="fw-bold text-danger mb-2"><i class="bi bi-exclamation-triangle me-2"></i>Terminate Project</h6>
                    <div class="alert alert-danger small py-2 mb-3">
                        <i class="bi bi-info-circle me-1"></i>This will generate a refund calculation for Finance Office review and cannot be undone.
                    </div>
                    <form method="POST" action="{{ route('projects.terminate', $project->project_id) }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Verified Deliverable Value (ETB)</label>
                            <input type="number" step="0.01" min="0" name="verified_value" class="form-control" value="0.00" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Reason for Termination</label>
                            <textarea name="reason" rows="3" class="form-control" required placeholder="Explain why this project is being terminated..."></textarea>
                        </div>
                        <button type="button" class="btn btn-danger w-100 fw-bold confirm-btn" data-confirm-title="Terminate Project" data-confirm-message="This will generate a refund calculation for Finance Office review and cannot be undone." data-confirm-icon="bi-exclamation-triangle" data-confirm-color="text-danger" data-confirm-btn-text="Yes, Terminate" data-confirm-btn-class="btn-danger">Terminate Project & Audit Refund</button>
                    </form>
                </div>
            </div>
        </div>
        @endif
    </div>

    {{-- ── Main Content ──────────────────────────────────────────────────────── --}}
    <div class="col-lg-9 order-lg-1">
        {{-- Project Overview Card --}}
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-info-circle me-2 text-success"></i>Project Overview</h5>
            </div>
            <div class="card-body p-4">
                @if($anonymized && Auth::user()->role === 'reviewer')
                    <div class="alert alert-warning py-2 mb-3 rounded-3">
                        <i class="bi bi-eye-slash-fill me-2"></i><strong>DOUBLE-BLIND PEER REVIEW PROTOCOL ACTIVE:</strong> Author metadata, department, and personal identity are programmatically stripped.
                    </div>
                @endif

                <div class="row g-3 mb-4">
                    @if(!$anonymized)
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded-3 h-100">
                            <div class="text-muted small text-uppercase fw-bold mb-1">Principal Investigator</div>
                            <div class="fs-6 fw-bold">{{ $project->pi->name }} <span class="text-muted">({{ $project->pi->staff_id }})</span></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded-3 h-100">
                            <div class="text-muted small text-uppercase fw-bold mb-1">Department & College</div>
                            <div class="fs-6 fw-bold">{{ $project->department ? $project->department->name : 'N/A' }}</div>
                        </div>
                    </div>
                    @endif
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded-3 h-100">
                            <div class="text-muted small text-uppercase fw-bold mb-1">Thematic Area</div>
                            <div class="fs-6 fw-bold">{{ $project->thematicArea ? $project->thematicArea->title : 'General' }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded-3 h-100">
                            <div class="text-muted small text-uppercase fw-bold mb-1">Budget</div>
                            <div class="fs-5 fw-bold text-primary">
                                {{ number_format($project->requested_budget, 2) }} ETB
                                @if($project->approved_budget)
                                    <span class="text-success fs-6">/ {{ number_format($project->approved_budget, 2) }} ETB (Ratified)</span>
        @endif

        {{-- Request Withdrawal Form (PI: Approved/Active needs admin approval) --}}
        @if(Auth::user()->role === 'pi' && in_array($project->status, ['Approved', 'Active']))
        <div class="collapse mb-4" id="requestCancelForm">
            <div class="card shadow-sm border-0 border-start border-danger border-4">
                <div class="card-body p-4">
                    <h6 class="fw-bold text-danger mb-2"><i class="bi bi-x-circle me-2"></i>Request Withdrawal</h6>
                    <div class="alert alert-warning small py-2 mb-3">
                        <i class="bi bi-info-circle me-1"></i>This project is already approved/active. Withdrawal requires admin approval.
                    </div>
                    <form method="POST" action="{{ route('projects.request-cancel', $project->project_id) }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Reason for Withdrawal *</label>
                            <textarea name="reason" rows="4" class="form-control" required placeholder="Explain why you want to withdraw this project (minimum 10 characters)..." minlength="10"></textarea>
                        </div>
                        <button type="button" class="btn btn-danger w-100 fw-bold confirm-btn" data-confirm-title="Request Withdrawal" data-confirm-message="This will submit a withdrawal request for admin approval. The project will be paused until a decision is made." data-confirm-icon="bi-exclamation-triangle" data-confirm-color="text-danger" data-confirm-btn-text="Yes, Request Withdrawal" data-confirm-btn-class="btn-danger">Submit Withdrawal Request</button>
                    </form>
                </div>
            </div>
        </div>
        @endif

        {{-- Admin: PendingCancellation Approve/Reject --}}
        @if($project->status === 'PendingCancellation' && in_array(Auth::user()->role, ['admin', 'coordinator', 'dean', 'rcsc']))
        <div class="mb-4">
            <div class="card shadow-sm border-0 border-start border-warning border-4">
                <div class="card-body p-4">
                    <h6 class="fw-bold text-warning mb-2"><i class="bi bi-exclamation-triangle me-2"></i>Pending Withdrawal Request</h6>
                    <div class="alert alert-warning small py-2 mb-3">
                        <strong>PI Reason:</strong> {{ $project->cancellation_reason ?? 'No reason provided' }}
                    </div>
                    <div class="d-flex gap-2">
                        <form action="{{ route('projects.approve-cancel', $project->project_id) }}" method="POST" class="flex-fill">
                            @csrf
                            <button type="button" class="btn btn-danger w-100 fw-bold confirm-btn" data-confirm-title="Approve Withdrawal" data-confirm-message="This will permanently withdraw the project. Are you sure?" data-confirm-icon="bi-check-circle" data-confirm-color="text-danger" data-confirm-btn-text="Yes, Approve Withdrawal" data-confirm-btn-class="btn-danger">
                                <i class="bi bi-check-circle me-1"></i>Approve Withdrawal
                            </button>
                        </form>
                        <form action="{{ route('projects.reject-cancel', $project->project_id) }}" method="POST" class="flex-fill">
                            @csrf
                            <button type="button" class="btn btn-success w-100 fw-bold confirm-btn" data-confirm-title="Reject Withdrawal" data-confirm-message="The project will be restored to its previous status. Are you sure?" data-confirm-icon="bi-x-circle" data-confirm-color="text-success" data-confirm-btn-text="Yes, Reject" data-confirm-btn-class="btn-success">
                                <i class="bi bi-x-circle me-1"></i>Reject Withdrawal
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @endif

    </div>
                        </div>
                    </div>
                </div>

                <div>
                    <div class="text-muted small text-uppercase fw-bold mb-2">Abstract & Problem Statement</div>
                    <div class="p-3 bg-light rounded-3 border-start border-success border-3">
                        {{ $project->abstract_text }}
                    </div>
                </div>

                @if($project->proposal_document_url)
                <div>
                    <div class="text-muted small text-uppercase fw-bold mb-2">Proposal Document</div>
                    <a href="{{ Storage::url($project->proposal_document_url) }}" target="_blank" class="btn btn-sm btn-outline-primary fw-bold">
                        <i class="bi bi-file-pdf me-1"></i>View Proposal PDF
                    </a>
                </div>
                @endif
            </div>
        </div>

        {{-- Team Members Card --}}
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-people me-2 text-info"></i>Team Members</h5>
                @if(Auth::user()->role === 'pi' && in_array($project->status, ['Draft', 'Submitted']))
                <a href="{{ route('team.index', $project->project_id) }}" class="btn btn-sm btn-outline-info">
                    <i class="bi bi-plus-circle me-1"></i>Manage
                </a>
                @endif
            </div>
            <div class="card-body p-4">
                @forelse($project->members as $member)
                <div class="d-flex align-items-center justify-content-between p-3 bg-light rounded-3 mb-2">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-info text-white d-flex align-items-center justify-content-center me-3" style="width:40px;height:40px;">
                            <i class="bi bi-person"></i>
                        </div>
                        <div>
                            <div class="fw-bold">{{ $member->user->name }}</div>
                            <div class="small text-muted">{{ $member->role_in_project }} — {{ $member->contribution_percentage }}% contribution</div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center py-4 text-muted">
                    <i class="bi bi-people fs-3 d-block mb-2"></i>
                    No team members added yet.
                    @if(Auth::user()->role === 'pi')
                    <a href="{{ route('team.index', $project->project_id) }}" class="btn btn-sm btn-outline-info mt-2">
                        <i class="bi bi-plus-circle me-1"></i>Add Team Members
                    </a>
                    @endif
                </div>
                @endforelse
            </div>
        </div>

        {{-- Budget Tranche Breakdown --}}
        @if(in_array($project->status, ['Approved', 'Active', 'Completed']))
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-wallet2 me-2 text-success"></i>Budget Tranche Breakdown</h5>
            </div>
            <div class="card-body p-4">
                @php
                    $budget = $project->approved_budget ?? $project->requested_budget;
                    $tranche1 = $budget * 0.30;
                    $tranche2 = $budget * 0.40;
                    $tranche3 = $budget * 0.30;
                @endphp
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="p-3 bg-light rounded-3 text-center h-100">
                            <div class="small text-muted fw-bold mb-1">Tranche 1 (Advance)</div>
                            <div class="fs-5 fw-bold text-primary">{{ number_format($tranche1, 2) }} ETB</div>
                            <div class="progress mt-2" style="height: 6px;">
                                <div class="progress-bar bg-primary" style="width: 30%"></div>
                            </div>
                            <div class="small text-muted mt-1">30% of budget</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 bg-light rounded-3 text-center h-100">
                            <div class="small text-muted fw-bold mb-1">Tranche 2 (Mid-term)</div>
                            <div class="fs-5 fw-bold text-info">{{ number_format($tranche2, 2) }} ETB</div>
                            <div class="progress mt-2" style="height: 6px;">
                                <div class="progress-bar bg-info" style="width: 40%"></div>
                            </div>
                            <div class="small text-muted mt-1">40% of budget</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 bg-light rounded-3 text-center h-100">
                            <div class="small text-muted fw-bold mb-1">Tranche 3 (Final)</div>
                            <div class="fs-5 fw-bold text-success">{{ number_format($tranche3, 2) }} ETB</div>
                            <div class="progress mt-2" style="height: 6px;">
                                <div class="progress-bar bg-success" style="width: 30%"></div>
                            </div>
                            <div class="small text-muted mt-1">30% of budget</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- Budget Ratification Card --}}
        @if(in_array(Auth::user()->role, ['dean', 'vparttcs', 'rcsc', 'admin']) && in_array($project->status, ['Submitted', 'DH_Screened', 'UnderReview']))
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-bank me-2 text-success"></i>Financial Budget Approval</h5>
            </div>
            <div class="card-body p-4">
                @if($project->requested_budget >= 500000)
                    <div class="alert alert-danger small mb-3 rounded-3">
                        <i class="bi bi-shield-exclamation me-1"></i>Requires <strong>RCSC / Vice President</strong> approval threshold.
                    </div>
                @else
                    <div class="alert alert-info small mb-3 rounded-3">
                        <i class="bi bi-check-circle me-1"></i>Approved at <strong>College Dean</strong> level.
                    </div>
                @endif

                <form method="POST" action="{{ route('projects.approve-budget', $project->project_id) }}">
                    @csrf
                    <div class="row g-3 align-items-end">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Approved Amount (ETB)</label>
                            <input type="number" step="0.01" min="0" name="approved_budget" class="form-control form-control-lg" value="{{ $project->requested_budget }}" required>
                        </div>
                        <div class="col-md-6">
                            <button type="button" class="btn btn-success btn-lg fw-bold w-100 confirm-btn" data-confirm-title="Ratify Budget" data-confirm-message="This will approve the budget for disbursement. This action cannot be undone." data-confirm-icon="bi-check-circle" data-confirm-color="text-success" data-confirm-btn-text="Yes, Approve" data-confirm-btn-class="btn-success">
                                <i class="bi bi-check-circle me-1"></i> Ratify & Approve Budget
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        @endif

        {{-- Blind Peer Review Evaluations --}}
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-eye-slash me-2 text-primary"></i>Blind Peer Review Evaluations</h5>
            </div>
            <div class="card-body p-4">
                @if(Auth::user()->role === 'coordinator')
                <form method="POST" action="{{ route('projects.assign-reviewer', $project->project_id) }}" class="mb-4 p-3 bg-light rounded-3">
                    @csrf
                    <div class="fw-bold mb-2">Assign Blind Peer Examiner</div>
                    <div class="input-group">
                        <select name="examiner_id" class="form-select" required>
                            <option value="">-- Select Faculty Member for Blind Review --</option>
                            @foreach($reviewers as $rev)
                                <option value="{{ $rev->id }}">{{ $rev->name }} ({{ $rev->department ? $rev->department->name : 'Faculty' }})</option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn btn-primary fw-bold confirm-btn" data-confirm-title="Assign Examiner" data-confirm-message="Assign this reviewer for blind evaluation?" data-confirm-icon="bi-person-check" data-confirm-color="text-primary" data-confirm-btn-text="Yes, Assign" data-confirm-btn-class="btn-primary">Assign Examiner</button>
                    </div>
                </form>
                @endif

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Examiner</th>
                                <th>Score</th>
                                <th>Verdict</th>
                                <th>Comments</th>
                                <th>Evaluated At</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($project->evaluations as $ev)
                            <tr>
                                <td>
                                    @if(Auth::user()->role === 'pi' || Auth::user()->role === 'reviewer')
                                        <span class="badge bg-secondary"><i class="bi bi-incognito me-1"></i>Masked #{{ substr(md5($ev->examiner_id), 0, 4) }}</span>
                                    @else
                                        {{ $ev->examiner->name }}
                                    @endif
                                </td>
                                <td><span class="fw-bold fs-6">{{ $ev->score }} / 100</span></td>
                                <td><span class="badge bg-info text-dark">{{ $ev->decision }}</span></td>
                                <td class="small">{{ $ev->comments ?: 'Pending critique submission...' }}</td>
                                <td class="small">{{ $ev->evaluated_at ? $ev->evaluated_at->diffForHumans() : 'Pending' }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                                    No examiners assigned yet.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Contract Section --}}
        @if(in_array($project->status, ['Approved', 'Active', 'Completed']))
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-file-earmark-text me-2 text-primary"></i>Project Contract</h5>
            </div>
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded-3 h-100">
                            <div class="small text-muted fw-bold mb-1">PI Signature</div>
                            @if(isset($project->contract) && $project->contract->pi_signed_at)
                                <div class="text-success fw-bold"><i class="bi bi-check-circle me-1"></i>Signed</div>
                            @else
                                <div class="text-warning fw-bold"><i class="bi bi-clock me-1"></i>Pending</div>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded-3 h-100">
                            <div class="small text-muted fw-bold mb-1">VP Signature</div>
                            @if(isset($project->contract) && $project->contract->vp_signed_at)
                                <div class="text-success fw-bold"><i class="bi bi-check-circle me-1"></i>Signed</div>
                            @else
                                <div class="text-warning fw-bold"><i class="bi bi-clock me-1"></i>Pending</div>
                            @endif
                        </div>
                    </div>
                </div>
                @if(Auth::user()->role === 'pi' && !isset($project->contract))
                <a href="{{ route('contracts.show', $project->project_id) }}" class="btn btn-outline-primary mt-3">
                    <i class="bi bi-pen me-1"></i>View & Sign Contract
                </a>
                @endif
            </div>
        </div>
        @endif

        {{-- Reviewer Evaluation Form --}}
        @if(Auth::user()->role === 'reviewer')
            @php
                $myPendingEval = $project->evaluations->where('examiner_id', Auth::id())->where('decision', 'Pending')->first();
                $myCompletedEval = $project->evaluations->where('examiner_id', Auth::id())->where('decision', '!=', 'Pending')->first();
            @endphp

            @if($myPendingEval)
            <div class="card shadow-sm border-0 mb-4 border-top border-primary border-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-2 text-dark"><i class="bi bi-pencil-square me-2 text-primary"></i>Submit Peer Critique</h5>
                    <p class="small text-muted mb-3">Score the research methodology, originality, and institutional feasibility.</p>

                    <form method="POST" action="{{ route('evaluations.submit', $myPendingEval->eval_id) }}">
                        @csrf
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Evaluation Score (0 - 100) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" min="0" max="100" name="score" class="form-control form-control-lg fw-bold" required placeholder="e.g. 85.00">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Recommendation Verdict <span class="text-danger">*</span></label>
                                <select name="decision" class="form-select form-select-lg" required>
                                    <option value="Accepted">Accepted (Recommended for Funding)</option>
                                    <option value="AcceptedWithMinorMods">Accepted with Minor Modifications</option>
                                    <option value="AcceptedWithMajorMods">Accepted with Major Modifications</option>
                                    <option value="Rejected">Rejected (Does Not Meet Standards)</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label small fw-bold">Constructive Comments & Critique <span class="text-danger">*</span></label>
                            <textarea name="comments" rows="5" class="form-control" required placeholder="Provide technical feedback, strengths, and areas of improvement..."></textarea>
                        </div>

                        <button type="button" class="btn btn-dark btn-lg w-100 fw-bold confirm-btn" data-confirm-title="Submit Evaluation" data-confirm-message="This action cannot be undone. Your score and recommendation will be final." data-confirm-icon="bi-send-check" data-confirm-color="text-primary" data-confirm-btn-text="Yes, Submit" data-confirm-btn-class="btn-dark">
                            <i class="bi bi-send-check me-1"></i> Submit Evaluation Score
                        </button>
                    </form>
                </div>
            </div>
            @elseif($myCompletedEval)
            <div class="card shadow-sm border-0 mb-4 border-top border-success border-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-2 text-success"><i class="bi bi-check-circle-fill me-2"></i>Evaluation Submitted</h5>
                    <p class="small text-muted mb-3">You have completed your peer review critique for this proposal.</p>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="p-3 bg-light rounded-3 text-center">
                                <div class="small text-muted mb-1">Your Score</div>
                                <div class="fs-4 fw-bold">{{ $myCompletedEval->score }} / 100</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 bg-light rounded-3 text-center">
                                <div class="small text-muted mb-1">Verdict</div>
                                <span class="badge bg-light text-dark border fs-6">{{ $myCompletedEval->decision }}</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 bg-light rounded-3 text-center">
                                <div class="small text-muted mb-1">Submitted</div>
                                <div class="fs-6 fw-bold">{{ $myCompletedEval->evaluated_at ? $myCompletedEval->evaluated_at->diffForHumans() : 'N/A' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        @endif
    </div>
</div>
@endsection

