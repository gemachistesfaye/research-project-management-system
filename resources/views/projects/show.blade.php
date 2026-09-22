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
<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
    <div class="flex-grow-1">
        <h2 class="fw-bold mb-1 text-dark fs-5 fs-md-2" style="word-break: break-word;">{{ $project->title }}</h2>
        <div class="d-flex align-items-center flex-wrap gap-2">
            <span class="text-muted small">ID: {{ $project->project_id }}</span>
            @if(in_array($project->status, ['Draft', 'Returned']))
                <span class="badge bg-secondary fs-6"><i class="bi bi-pencil me-1"></i>{{ $project->status }}</span>
                @if(Auth::user()->role === 'pi')
                <form action="{{ route('projects.submit', $project->project_id) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="button" class="btn btn-success btn-sm fw-bold confirm-btn" data-confirm-title="Submit Proposal" data-confirm-message="This proposal will be sent to the Department Head for initial screening. You won't be able to edit it after submission." data-confirm-icon="bi-send" data-confirm-color="text-success" data-confirm-btn-text="Yes, Submit" data-confirm-btn-class="btn-success"><i class="bi bi-send me-1"></i>Submit</button>
                </form>
                @endif
            @elseif($project->status === 'Approved')
                <span class="badge bg-success fs-6"><i class="bi bi-check-circle me-1"></i>{{ $project->status }}</span>
                @if(in_array(Auth::user()->role, ['pi', 'vparttcs']) && (!$project->irercClearance || $project->irercClearance->status === 'Approved'))
                <a href="{{ route('contracts.show', $project->project_id) }}" class="btn btn-dark btn-sm fw-bold shadow-sm">
                    <i class="bi bi-pen me-1"></i>Sign Contract
                </a>
                @endif
            @elseif($project->status === 'Active')
                <span class="badge bg-dark fs-6"><i class="bi bi-play-circle me-1"></i>{{ $project->status }}</span>
                <a href="{{ route('progress.show', $project->project_id) }}" class="btn btn-outline-dark btn-sm fw-bold shadow-sm">
                    <i class="bi bi-graph-up me-1"></i>Progress
                </a>
                @if(in_array(Auth::user()->role, ['coordinator', 'admin']))
                <form action="{{ route('projects.mark-complete', $project->project_id) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="button" class="btn btn-success btn-sm fw-bold shadow-sm confirm-btn" data-confirm-title="Mark Project as Complete" data-confirm-message="All tranches have been disbursed and milestones met. Mark this research project as officially Completed?" data-confirm-icon="bi-check-circle" data-confirm-color="text-success" data-confirm-btn-text="Yes, Complete Project" data-confirm-btn-class="btn-success">
                        <i class="bi bi-check-all me-1"></i>Complete
                    </button>
                </form>
                @endif
            @elseif($project->status === 'Completed')
                <span class="badge bg-success fs-6"><i class="bi bi-check-circle me-1"></i>Completed</span>
                <a href="{{ route('progress.show', $project->project_id) }}" class="btn btn-outline-dark btn-sm fw-bold shadow-sm">
                    <i class="bi bi-graph-up me-1"></i>Progress
                </a>
                @php $cert = $project->certificates ? $project->certificates->first() : null; @endphp
                @if($cert)
                    <a href="{{ route('certificates.view', $cert->id) }}" target="_blank" class="btn btn-outline-dark btn-sm fw-bold shadow-sm">
                        <i class="bi bi-eye me-1"></i>View Cert
                    </a>
                    <a href="{{ route('certificates.download', $cert->id) }}" class="btn btn-dark btn-sm fw-bold shadow-sm">
                        <i class="bi bi-download me-1"></i>Download
                    </a>
                @elseif(in_array(Auth::user()->role, ['coordinator', 'admin']))
                <a href="{{ route('certificates') }}" class="btn btn-success btn-sm fw-bold shadow-sm">
                    <i class="bi bi-award me-1"></i>Issue Cert
                </a>
                @endif
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
                <span class="badge bg-dark fs-6">RCSC Tier</span>
            @else
                <span class="badge bg-secondary fs-6">Dean Tier</span>
            @endif
            @if($project->irercClearance)
                @if($project->irercClearance->status === 'Approved')
                    <span class="badge bg-success fs-6"><i class="bi bi-shield-check me-1"></i>Ethics Cleared</span>
                @elseif($project->irercClearance->status === 'Rejected')
                    <span class="badge bg-danger fs-6"><i class="bi bi-shield-x me-1"></i>Ethics Rejected</span>
                @else
                    <span class="badge bg-warning text-dark fs-6"><i class="bi bi-shield-exclamation me-1"></i>Ethics Pending</span>
                @endif
            @endif
            <span class="text-muted small w-100 d-md-inline w-md-auto ms-md-auto mt-2 mt-md-0">
                <i class="bi bi-clock me-1"></i>Last updated {{ $project->updated_at ? $project->updated_at->diffForHumans() : $project->created_at->diffForHumans() }}
            </span>
        </div>
    </div>
    <div class="d-flex w-100 w-md-auto justify-content-start justify-content-md-end mt-2 mt-md-0">
        <a href="{{ route('projects.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Back to Projects
        </a>
    </div>
</div>

@if(in_array($project->status, ['Returned', 'Rejected']) && $project->feedback)
<div class="alert alert-warning border-start border-4 border-warning mb-4">
    <div class="d-flex align-items-start">
        <i class="bi bi-exclamation-triangle-fill fs-4 text-warning me-3 mt-1"></i>
        <div>
            <h6 class="fw-bold mb-1">{{ $project->status === 'Rejected' ? 'Rejected by Reviewer' : 'Returned — Modifications Required' }}</h6>
            <p class="mb-0">{{ $project->feedback }}</p>
        </div>
    </div>
</div>
@endif

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
        'Submitted'   => $project->submitted_at ?? null,
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
                {{-- Sign Contract --}}
                @if($project->status === 'Approved' && in_array(Auth::user()->role, ['pi', 'vparttcs']) && (!$project->irercClearance || $project->irercClearance->status === 'Approved'))
                <a href="{{ route('contracts.show', $project->project_id) }}" class="btn btn-dark w-100 mb-2 text-start fw-bold shadow-sm">
                    <i class="bi bi-pen me-2"></i>View & Sign Contract
                </a>
                @endif

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
                @if(in_array($project->status, ['Draft', 'Withdrawn', 'Returned', 'Rejected']) && Auth::user()->role === 'pi')
                <a href="{{ route('projects.edit', $project->project_id) }}" class="btn w-100 mb-2 text-start fw-bold" style="background:#fff;color:#212529;border:1.5px solid #dee2e6;" onmouseover="this.style.background='#212529';this.style.color='#fff';this.style.borderColor='#212529'" onmouseout="this.style.background='#fff';this.style.color='#212529';this.style.borderColor='#dee2e6'">
                    <i class="bi bi-pencil-square me-2"></i>Edit Proposal
                </a>
                @endif

                @if(in_array($project->status, ['Draft', 'Withdrawn', 'Returned', 'Rejected', 'Submitted', 'DH_Screened', 'UnderReview']) && Auth::user()->role === 'pi')
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
                @if(in_array($project->status, ['Draft', 'Withdrawn', 'Returned', 'Rejected']) && Auth::user()->role === 'pi')
                <form action="{{ route('projects.destroy', $project->project_id) }}" method="POST" class="mb-2">
                    @csrf
                    @method('DELETE')
                    <button type="button" class="btn w-100 text-start confirm-btn fw-bold" style="background:#212529;color:#fff;border:1.5px solid #dc3545;" onmouseover="this.style.background='#dc3545';this.style.borderColor='#dc3545'" onmouseout="this.style.background='#212529';this.style.borderColor='#dc3545'" data-confirm-title="Delete Proposal" data-confirm-message="This will permanently delete this draft proposal." data-confirm-icon="bi-trash" data-confirm-color="text-danger" data-confirm-btn-text="Yes, Delete" data-confirm-btn-class="btn-danger">
                        <i class="bi bi-trash me-2"></i>Delete Draft
                    </button>
                </form>
                @endif

                {{-- Procurement Tracker --}}
                @if(in_array($project->status, ['Active', 'Approved']) && in_array(Auth::user()->role, ['pi', 'coordinator', 'admin']))
                <a href="{{ route('procurement.index') }}" class="btn w-100 mb-2 text-start fw-bold" style="background:#fff;color:#0d6efd;border:1.5px solid #0d6efd;" onmouseover="this.style.background='#0d6efd';this.style.color='#fff'" onmouseout="this.style.background='#fff';this.style.color='#0d6efd'">
                    <i class="bi bi-cart3 me-2"></i>Procurement Tracker
                </a>
                @endif

                {{-- Extend --}}
                @if(in_array($project->status, ['Active', 'Approved']) && Auth::user()->role === 'pi' && (int)$project->pi_id === (int)Auth::id())
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
                            <div class="text-muted small text-uppercase fw-bold mb-1">Grant Budget</div>
                            <div class="fs-6 fw-bold">
                                @if($project->approved_budget && $project->approved_budget != $project->requested_budget)
                                    <span class="text-success fs-5">{{ number_format($project->approved_budget, 2) }} ETB</span>
                                    <small class="text-muted d-block" style="font-size:0.75rem;">(Orig. Requested: {{ number_format($project->requested_budget, 2) }} ETB + {{ number_format($project->approved_budget - $project->requested_budget, 2) }} ETB Amendment)</small>
                                @else
                                    <span class="text-dark fs-5 fw-bold">{{ number_format($project->approved_budget ?: $project->requested_budget, 2) }} ETB</span>
                                    <span class="badge bg-dark ms-1">Ratified</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <div class="text-muted small text-uppercase fw-bold mb-2">Abstract & Problem Statement</div>
                    <div class="p-3 bg-light rounded-3 border-start border-success border-3">
                        {{ $project->abstract_text }}
                    </div>
                </div>

                @if($project->proposal_document_url)
                <div class="mt-3">
                    <div class="text-muted small text-uppercase fw-bold mb-2">Proposal Document</div>
                    <a href="{{ Storage::url($project->proposal_document_url) }}" target="_blank" class="btn btn-sm btn-outline-dark fw-bold">
                        <i class="bi bi-file-pdf me-1"></i>View Proposal PDF
                    </a>
                </div>
                @endif
            </div>
        </div>

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

        {{-- Team Members Card --}}
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-people me-2 text-dark"></i>Team Members</h5>
                @if(Auth::user()->role === 'pi' && in_array($project->status, ['Draft', 'Submitted']))
                <a href="{{ route('team.index', $project->project_id) }}" class="btn btn-sm btn-outline-dark">
                    <i class="bi bi-plus-circle me-1"></i>Manage
                </a>
                @endif
            </div>
            <div class="card-body p-4">
                @forelse($project->members as $member)
                <div class="d-flex align-items-center justify-content-between p-3 bg-light rounded-3 mb-2">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-dark text-white d-flex align-items-center justify-content-center me-3" style="width:40px;height:40px;">
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
                    <a href="{{ route('team.index', $project->project_id) }}" class="btn btn-sm btn-outline-dark mt-2">
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
                            <div class="fs-5 fw-bold text-dark">{{ number_format($tranche1, 2) }} ETB</div>
                            <div class="progress mt-2" style="height: 6px;">
                                <div class="progress-bar bg-dark" style="width: 30%"></div>
                            </div>
                            <div class="small text-muted mt-1">30% of budget</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 bg-light rounded-3 text-center h-100">
                            <div class="small text-muted fw-bold mb-1">Tranche 2 (Mid-term)</div>
                            <div class="fs-5 fw-bold text-dark">{{ number_format($tranche2, 2) }} ETB</div>
                            <div class="progress mt-2" style="height: 6px;">
                                <div class="progress-bar bg-secondary" style="width: 40%"></div>
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

        {{-- Financial Budget Approval & Ratification Card (SDD SCR-08 Dual-Threshold) --}}
        @php
            $canApproveBudget = false;
            $userRole = Auth::user()->role;
            if ($userRole === 'admin') {
                $canApproveBudget = true;
            } elseif ($project->status === 'Dean_Review' && in_array($userRole, ['dean', 'vparttcs'])) {
                $canApproveBudget = true;
            } elseif ($project->status === 'RCSC_Review' && in_array($userRole, ['rcsc', 'vparttcs'])) {
                $canApproveBudget = true;
            }
        @endphp

        @if(in_array($project->status, ['Dean_Review', 'RCSC_Review', 'UnderReview']))
        <div class="card shadow-sm border-0 mb-4 border-top border-4 {{ $project->requested_budget >= 500000 ? 'border-danger' : 'border-dark' }}">
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-bank me-2 text-dark"></i>Financial Budget Approval</h5>
                @if($project->requested_budget >= 500000)
                    <span class="badge bg-danger fs-6">RCSC / VP Tier (&ge;500k ETB)</span>
                @else
                    <span class="badge bg-secondary fs-6">College Dean Tier (&lt;500k ETB)</span>
                @endif
            </div>
            <div class="card-body p-4">
                @if($project->requested_budget >= 500000)
                    <div class="alert alert-danger small mb-3 rounded-3">
                        <i class="bi bi-shield-exclamation me-1"></i>Scientific review complete. Requires formal ratification by <strong>RCSC Committee / Vice President</strong> threshold.
                    </div>
                @else
                    <div class="alert alert-secondary small mb-3 rounded-3">
                        <i class="bi bi-check-circle me-1"></i>Scientific review complete. Requires financial review & approval by <strong>College Dean</strong>.
                    </div>
                @endif

                @if($canApproveBudget)
                <form method="POST" action="{{ route('projects.approve-budget', $project->project_id) }}">
                    @csrf
                    <div class="row g-3 align-items-end">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Approved Amount (ETB) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" min="0" name="approved_budget" class="form-control form-control-lg fw-bold" value="{{ $project->requested_budget }}" required>
                        </div>
                        <div class="col-md-6">
                            <button type="button" class="btn {{ $project->requested_budget >= 500000 ? 'btn-danger' : 'btn-success' }} btn-lg fw-bold w-100 confirm-btn" data-confirm-title="Ratify Budget" data-confirm-message="This will ratify the approved grant budget and advance this proposal for contract signing. This action is final." data-confirm-icon="bi-check-circle" data-confirm-color="text-success" data-confirm-btn-text="Yes, Approve Budget" data-confirm-btn-class="btn-success">
                                <i class="bi bi-check-circle me-1"></i> Ratify & Approve Budget
                            </button>
                        </div>
                    </div>
                </form>
                @else
                <div class="p-3 bg-light rounded-3 text-muted text-center">
                    <i class="bi bi-hourglass-split me-1"></i>
                    Awaiting financial budget ratification by <strong>{{ $project->requested_budget >= 500000 ? 'RCSC Committee / Vice President' : 'College Dean' }}</strong>.
                </div>
                @endif
            </div>
        </div>
        @endif

        {{-- Blind Peer Review Evaluations --}}
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-eye-slash me-2 text-dark"></i>Blind Peer Review Evaluations</h5>
                @if(Auth::user()->role === 'coordinator' && in_array($project->status, ['Submitted', 'DH_Screened', 'UnderReview', 'Dean_Review', 'Approved', 'Active']))
                    @if(!$project->irercClearance)
                    <form action="{{ route('projects.create-irerc-clearance', $project->project_id) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="button" class="btn btn-sm btn-outline-dark fw-bold confirm-btn" data-confirm-title="Request Ethics Clearance" data-confirm-message="Route this project to the Institutional Research Ethics Review Committee (IRERC) for ethics clearance?" data-confirm-icon="bi-shield-check" data-confirm-color="text-dark" data-confirm-btn-text="Yes, Request" data-confirm-btn-class="btn-dark">
                            <i class="bi bi-shield-plus me-1"></i>Send to Ethics (IRERC)
                        </button>
                    </form>
                    @endif
                @endif
            </div>
            <div class="card-body p-4">
                @if(Auth::user()->role === 'coordinator' && in_array($project->status, ['DH_Screened', 'UnderReview']) && $project->evaluations->where('decision', 'Pending')->count() < 2 && $project->evaluations->count() < 2)
                <form method="POST" action="{{ route('projects.assign-reviewer', $project->project_id) }}" class="mb-4 p-3 bg-light rounded-3">
                    @csrf
                    <div class="fw-bold mb-2">Assign Blind Peer Examiner</div>
                    <div class="input-group">
                        <select name="examiner_id" class="form-select" required>
                            <option value="">-- Select Faculty Member for Blind Review --</option>
                            @foreach($reviewers->whereNotIn('id', $project->evaluations->pluck('examiner_id')) as $rev)
                                <option value="{{ $rev->id }}">{{ $rev->name }} ({{ $rev->department ? $rev->department->name : 'Faculty' }})</option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn btn-dark fw-bold confirm-btn" data-confirm-title="Assign Examiner" data-confirm-message="Assign this reviewer for blind evaluation?" data-confirm-icon="bi-person-check" data-confirm-color="text-dark" data-confirm-btn-text="Yes, Assign" data-confirm-btn-class="btn-dark">Assign Examiner</button>
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
                            @forelse($project->evaluations->filter(function($ev) {
                                if(Auth::user()->role === 'reviewer' && $ev->examiner_id === Auth::id() && $ev->decision === 'Pending') {
                                    return false;
                                }
                                return true;
                            }) as $ev)
                            <tr>
                                <td>
                                    @if(Auth::user()->role === 'pi' || Auth::user()->role === 'reviewer')
                                        <span class="badge bg-secondary"><i class="bi bi-incognito me-1"></i>Masked #{{ substr(md5($ev->examiner_id), 0, 4) }}</span>
                                    @else
                                        {{ $ev->examiner->name }}
                                    @endif
                                </td>
                                <td><span class="fw-bold fs-6">{{ $ev->score }} / 100</span></td>
                                <td><span class="badge bg-secondary">{{ $ev->decision }}</span></td>
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

        {{-- Financial Disbursements & Tranche Tracking Card (Visible to PI, Coordinator, DH, Dean, VP, Finance, Admin) --}}
        @if(in_array($project->status, ['Approved', 'Active', 'Completed']) && in_array(Auth::user()->role, ['pi', 'tm', 'coordinator', 'dh', 'dean', 'vparttcs', 'rcsc', 'finance', 'admin']))
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0 text-dark">
                    <i class="bi bi-cash-stack me-2 text-success"></i>Financial Disbursements &amp; Tranche Tracking
                </h5>
                @php
                    $totalRatified = $project->approved_budget ?: $project->requested_budget;
                    $totalReleased = $project->budgetRequests->where('status', 'Released')->sum('approved_amount');
                    $releasePct = $totalRatified > 0 ? round(($totalReleased / $totalRatified) * 100, 1) : 0;
                @endphp
                <span class="badge bg-light text-dark border px-2 py-1">
                    Released: <strong>{{ number_format($totalReleased, 2) }} ETB</strong> / {{ number_format($totalRatified, 2) }} ETB ({{ $releasePct }}%)
                </span>
            </div>
            <div class="card-body p-4">
                {{-- Overall Budget Progress Bar --}}
                <div class="mb-4">
                    <div class="d-flex justify-content-between text-muted small mb-1">
                        <span>Fund Release Progress</span>
                        <span class="fw-bold text-dark">{{ $releasePct }}% Disbursed</span>
                    </div>
                    <div class="progress" style="height: 12px; border-radius: 6px;">
                        <div class="progress-bar bg-success" role="progressbar" style="width: {{ $releasePct }}%; border-radius: 6px;"
                             aria-valuenow="{{ $releasePct }}" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>

                {{-- Tranches Table --}}
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Tranche Phase</th>
                                <th>Allocation %</th>
                                <th>Approved Amount</th>
                                <th>Status</th>
                                <th>Disbursement Method</th>
                                <th>Released Date</th>
                                <th>Voucher / Ref</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($project->budgetRequests->filter(fn($r) => str_starts_with($r->milestone_phase, 'Tranche') || $r->milestone_phase === 'Budget Amendment')->sortBy('id') as $bReq)
                            <tr>
                                <td class="fw-bold text-dark">
                                    @if($bReq->milestone_phase === 'Tranche 1')
                                        <span class="badge bg-dark text-white px-2 py-1"><i class="bi bi-1-circle me-1"></i>Tranche 1</span>
                                    @elseif($bReq->milestone_phase === 'Tranche 2')
                                        <span class="badge bg-dark text-white px-2 py-1"><i class="bi bi-2-circle me-1"></i>Tranche 2</span>
                                    @elseif($bReq->milestone_phase === 'Tranche 3')
                                        <span class="badge bg-dark text-white px-2 py-1"><i class="bi bi-3-circle me-1"></i>Tranche 3</span>
                                    @elseif($bReq->milestone_phase === 'Budget Amendment')
                                        <span class="badge bg-success text-white px-2 py-1"><i class="bi bi-cash-stack me-1"></i>Budget Amendment</span>
                                    @else
                                        <span class="badge bg-secondary text-white">{{ $bReq->milestone_phase }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if($bReq->milestone_phase === 'Tranche 1')
                                        30% (Advance)
                                    @elseif($bReq->milestone_phase === 'Tranche 2')
                                        40% (Mid-Term)
                                    @elseif($bReq->milestone_phase === 'Tranche 3')
                                        30% (Final)
                                    @elseif($bReq->milestone_phase === 'Budget Amendment')
                                        Supplemental / Amendment
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="fw-bold text-dark">{{ number_format($bReq->approved_amount ?? $bReq->requested_amount, 2) }} ETB</td>
                                <td>
                                    @if($bReq->status === 'Released')
                                        <span class="badge bg-success text-white px-2 py-1"><i class="bi bi-check-all me-1"></i>Disbursed</span>
                                    @elseif($bReq->status === 'Approved')
                                        <span class="badge bg-warning text-dark px-2 py-1"><i class="bi bi-hourglass-split me-1"></i>Pending Finance Release</span>
                                    @else
                                        <span class="badge bg-secondary text-white">{{ $bReq->status }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if($bReq->payment_method)
                                        <span class="badge bg-light text-dark border">{{ $bReq->payment_method }}</span>
                                    @else
                                        <span class="text-muted small">Pending Release</span>
                                    @endif
                                </td>
                                <td>
                                    @if($bReq->disbursed_at)
                                        <span class="fw-semibold text-dark">{{ $bReq->disbursed_at->format('M d, Y') }}</span>
                                        <small class="text-muted d-block" style="font-size:0.72rem;">{{ $bReq->disbursed_at->format('h:i A') }}</small>
                                    @else
                                        <span class="text-muted small">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($bReq->notes)
                                        <span class="badge bg-light text-dark border text-truncate" style="max-width: 150px;" title="{{ $bReq->notes }}">
                                            <i class="bi bi-receipt me-1"></i>{{ $bReq->notes }}
                                        </span>
                                    @else
                                        <span class="text-muted small">-</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    <i class="bi bi-wallet2 fs-3 d-block text-secondary mb-2"></i>
                                    Tranche releases will appear here as the contract is signed and milestones are approved.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

        {{-- Contract Section --}}
        @if(in_array($project->status, ['Approved', 'Active', 'Completed']))
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-file-earmark-text me-2 text-dark"></i>Project Contract</h5>
            </div>
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded-3 h-100">
                            <div class="small text-muted fw-bold mb-1">PI Signature</div>
                            @if($project->pi_signature_date)
                                <div class="text-success fw-bold"><i class="bi bi-check-circle me-1"></i>Signed</div>
                            @else
                                <div class="text-warning fw-bold"><i class="bi bi-clock me-1"></i>Pending</div>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded-3 h-100">
                            <div class="small text-muted fw-bold mb-1">VP Signature</div>
                            @if($project->vp_signature_date)
                                <div class="text-success fw-bold"><i class="bi bi-check-circle me-1"></i>Signed</div>
                            @else
                                <div class="text-warning fw-bold"><i class="bi bi-clock me-1"></i>Pending</div>
                            @endif
                        </div>
                    </div>
                </div>
                @if(in_array(Auth::user()->role, ['pi', 'vparttcs', 'coordinator', 'admin']))
                    @if(!$project->pi_signature_date || !$project->vp_signature_date)
                    <div class="d-flex gap-2 mt-3 flex-wrap">
                        <a href="{{ route('contracts.show', $project->project_id) }}" class="btn btn-dark fw-bold">
                            <i class="bi bi-pen me-1"></i>View &amp; Sign Contract
                        </a>
                        <a href="{{ route('contracts.download', $project->project_id) }}" class="btn btn-outline-dark">
                            <i class="bi bi-download me-1"></i>Download PDF
                        </a>
                    </div>
                    @else
                    <div class="d-flex gap-2 mt-3 flex-wrap">
                        <a href="{{ route('contracts.show', $project->project_id) }}" class="btn btn-outline-dark btn-sm">
                            <i class="bi bi-file-earmark-text me-1"></i>View Signed Contract
                        </a>
                        <a href="{{ route('contracts.download', $project->project_id) }}" class="btn btn-outline-dark btn-sm">
                            <i class="bi bi-download me-1"></i>Download PDF
                        </a>
                    </div>
                    @endif
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
            <div class="card shadow-sm border-0 mb-4 border-top border-dark border-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-2 text-dark"><i class="bi bi-pencil-square me-2 text-dark"></i>Submit Peer Critique</h5>
                    <p class="small text-muted mb-3">Score the research methodology, originality, and institutional feasibility.</p>
                    <h5 class="fw-bold mb-1 text-dark"><i class="bi bi-clipboard-check me-2 text-dark"></i>Standardized 100-Point Evaluation Rubric</h5>
                    <p class="small text-muted mb-3">Evaluate the proposal across 5 standardized academic dimensions. Total score is computed automatically.</p>

                    <form method="POST" action="{{ route('evaluations.submit', $myPendingEval->eval_id) }}" id="rubricEvaluationForm">
                        @csrf

                        {{-- 5-Dimension Rubric Inputs --}}
                        <div class="p-3 bg-light rounded-3 border mb-3">
                            <div class="row g-2">
                                <div class="col-md-6 col-12">
                                    <label class="form-label small fw-bold mb-1 d-flex justify-content-between">
                                        <span>1. Methodology &amp; Research Design</span>
                                        <span class="text-muted">(Max 25)</span>
                                    </label>
                                    <input type="number" step="0.5" min="0" max="25" name="rubric_methodology" id="rubric_methodology"
                                           class="form-control form-control-sm rubric-input" required placeholder="0 - 25" oninput="calculateTotalScore()">
                                </div>
                                <div class="col-md-6 col-12">
                                    <label class="form-label small fw-bold mb-1 d-flex justify-content-between">
                                        <span>2. Background &amp; Literature Review</span>
                                        <span class="text-muted">(Max 20)</span>
                                    </label>
                                    <input type="number" step="0.5" min="0" max="20" name="rubric_literature" id="rubric_literature"
                                           class="form-control form-control-sm rubric-input" required placeholder="0 - 20" oninput="calculateTotalScore()">
                                </div>
                                <div class="col-md-4 col-12">
                                    <label class="form-label small fw-bold mb-1 d-flex justify-content-between">
                                        <span>3. Work Plan &amp; Feasibility</span>
                                        <span class="text-muted">(Max 20)</span>
                                    </label>
                                    <input type="number" step="0.5" min="0" max="20" name="rubric_feasibility" id="rubric_feasibility"
                                           class="form-control form-control-sm rubric-input" required placeholder="0 - 20" oninput="calculateTotalScore()">
                                </div>
                                <div class="col-md-4 col-12">
                                    <label class="form-label small fw-bold mb-1 d-flex justify-content-between">
                                        <span>4. Regional Relevance &amp; Impact</span>
                                        <span class="text-muted">(Max 20)</span>
                                    </label>
                                    <input type="number" step="0.5" min="0" max="20" name="rubric_relevance" id="rubric_relevance"
                                           class="form-control form-control-sm rubric-input" required placeholder="0 - 20" oninput="calculateTotalScore()">
                                </div>
                                <div class="col-md-4 col-12">
                                    <label class="form-label small fw-bold mb-1 d-flex justify-content-between">
                                        <span>5. Budget Justification</span>
                                        <span class="text-muted">(Max 15)</span>
                                    </label>
                                    <input type="number" step="0.5" min="0" max="15" name="rubric_budget" id="rubric_budget"
                                           class="form-control form-control-sm rubric-input" required placeholder="0 - 15" oninput="calculateTotalScore()">
                                </div>
                            </div>
                        </div>

                        {{-- Total Score & Recommendation Verdict --}}
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold mb-1">Computed Total Score (0 - 100) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" step="0.01" min="0" max="100" name="score" id="totalScoreInput"
                                           class="form-control fw-bold bg-white text-dark fs-5" required placeholder="0.00" readonly>
                                    <span class="input-group-text bg-light fw-bold">/ 100</span>
                                </div>
                                <small class="text-muted" style="font-size:0.75rem;">Calculated automatically from rubric dimensions</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold mb-1">Recommendation Verdict <span class="text-danger">*</span></label>
                                <select name="decision" class="form-select fw-bold" required>
                                    <option value="Accepted">Accepted (Fund)</option>
                                    <option value="AcceptedWithMinorMods">Accepted (Minor Mods)</option>
                                    <option value="AcceptedWithMajorMods">Accepted (Major Mods)</option>
                                    <option value="Rejected">Rejected</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Constructive Comments &amp; Technical Critique <span class="text-danger">*</span></label>
                            <textarea name="comments" rows="4" class="form-control" required placeholder="Provide technical feedback, methodological strengths, and specific areas of required improvement..."></textarea>
                        </div>

                        <button type="button" class="btn btn-dark w-100 fw-bold confirm-btn" data-confirm-title="Submit Evaluation" data-confirm-message="This action cannot be undone. Your rubric score and recommendation verdict will be recorded under double-blind protocol." data-confirm-icon="bi-send-check" data-confirm-color="text-dark" data-confirm-btn-text="Yes, Submit Score" data-confirm-btn-class="btn-dark">
                            <i class="bi bi-send-check me-1"></i> Submit Evaluation Score
                        </button>
                    </form>

                    <script>
                    function calculateTotalScore() {
                        const m = parseFloat(document.getElementById('rubric_methodology').value) || 0;
                        const l = parseFloat(document.getElementById('rubric_literature').value) || 0;
                        const f = parseFloat(document.getElementById('rubric_feasibility').value) || 0;
                        const r = parseFloat(document.getElementById('rubric_relevance').value) || 0;
                        const b = parseFloat(document.getElementById('rubric_budget').value) || 0;
                        const total = Math.min(100, Math.max(0, m + l + f + r + b));
                        document.getElementById('totalScoreInput').value = total.toFixed(2);
                    }
                    </script>
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

