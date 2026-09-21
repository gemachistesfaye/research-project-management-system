@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-0"><i class="bi bi-calendar-range me-2 text-dark"></i> Extensions &amp; Amendments</h3>
        <span class="text-muted">Request time extensions or budget adjustments for your projects</span>
    </div>
</div>

@if(Auth::user()->role === 'pi')
    @php
        $myProjects = \App\Models\Project::where('pi_id', Auth::id())
            ->whereIn('status', ['Active', 'Approved'])
            ->with(['extensions', 'budgetAmendments'])
            ->get();
    @endphp

    @if($myProjects->isEmpty())
        <div class="card card-custom">
            <div class="card-body text-center py-5">
                <i class="bi bi-calendar-x fs-1 text-muted d-block mb-3"></i>
                <h5 class="fw-bold text-muted">No Active Projects</h5>
                <p class="text-muted mb-3">You don't have any active or approved projects eligible for extensions or budget amendments.</p>
                <p class="small text-muted">To request an extension, you must first have a project with <strong>Active</strong> or <strong>Approved</strong> status. Contact your department coordinator if you need assistance.</p>
            </div>
        </div>
    @else
        {{-- Approval Rules Info Box --}}
        <div class="alert alert-light border-start border-dark border-4 mb-4">
            <div class="d-flex align-items-start">
                <i class="bi bi-info-circle-fill text-dark fs-4 me-3 mt-1"></i>
                <div>
                    <h6 class="fw-bold mb-2">Extension &amp; Amendment Rules</h6>
                    <ul class="small mb-0">
                        <li><strong>Extensions 1 &amp; 2:</strong> Approved by Department Coordinator (max 6 months each).</li>
                        <li><strong>Extension 3:</strong> Requires RCSC (Research and Community Service Council) approval.</li>
                        <li><strong>Maximum:</strong> 3 extensions per project — no exceptions.</li>
                        <li><strong>Budget Amendments:</strong> Require RCSC approval with price inflation justification.</li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="row g-3">
            @foreach($myProjects as $project)
            <div class="col-md-6 mb-4">
                <div class="card card-custom h-100">
                    <div class="card-header bg-white border-bottom">
                        <h6 class="fw-bold mb-0">{{ Str::limit($project->title, 40) }}</h6>
                        @if($project->proposal_document_url)
                        <a href="{{ Storage::url($project->proposal_document_url) }}" target="_blank" class="text-decoration-none" title="View Proposal Document">
                            <i class="bi bi-file-pdf text-danger small"></i>
                        </a>
                        @endif
                    </div>
                    <div class="card-body">
                        {{-- Extension Section --}}
                        <div class="mb-4">
                            <h6 class="fw-bold text-dark mb-3"><i class="bi bi-clock-history me-1"></i> Time Extensions</h6>

                            @php
                                $extCount = $project->extensions->count();
                                $extPercentage = ($extCount / 3) * 100;
                                $extBarColor = $extCount >= 3 ? 'bg-danger' : ($extCount >= 2 ? 'bg-warning' : 'bg-dark');
                            @endphp

                            {{-- Progress Bar --}}
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <small class="fw-bold text-muted">Extensions Used</small>
                                    <small class="fw-bold {{ $extCount >= 3 ? 'text-danger' : ($extCount >= 2 ? 'text-warning' : 'text-dark') }}">{{ $extCount }}/3</small>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar {{ $extBarColor }}" style="width: {{ $extPercentage }}%"></div>
                                </div>
                            </div>

                            {{-- Warning when approaching limit --}}
                            @if($extCount >= 2 && $extCount < 3)
                                <div class="alert alert-warning small py-2 px-3 mb-3">
                                    <i class="bi bi-exclamation-triangle-fill me-1"></i>
                                    <strong>Warning:</strong> You have used 2 of 3 allowed extensions. One more request and no further extensions will be available for this project.
                                </div>
                            @endif

                            {{-- Extension request cards --}}
                            @if($extCount > 0)
                                <div class="row g-2 mb-3">
                                    @foreach($project->extensions as $ext)
                                        @php
                                            $borderColor = $ext->status === 'Approved' ? 'success' : ($ext->status === 'Rejected' ? 'danger' : 'warning');
                                            $icon = $ext->status === 'Approved' ? 'bi-check-circle-fill' : ($ext->status === 'Rejected' ? 'bi-x-circle-fill' : 'bi-hourglass-split');
                                            $bgLight = $ext->status === 'Approved' ? 'bg-success-subtle' : ($ext->status === 'Rejected' ? 'bg-danger-subtle' : 'bg-warning-subtle');
                                        @endphp
                                        <div class="col-12">
                                            <div class="card border-{{ $borderColor }} {{ $bgLight }} shadow-sm">
                                                <div class="card-body py-2 px-3">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <div>
                                                            <span class="fw-bold small">Extension #{{ $ext->extension_number }}</span>
                                                            <span class="text-muted small ms-2">{{ $ext->requested_months }} month(s)</span>
                                                        </div>
                                                        <span class="badge bg-{{ $borderColor }} text-white">
                                                            <i class="bi {{ $icon }} me-1"></i>{{ $ext->status }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center text-muted small py-3 mb-3 border rounded bg-light">
                                    <i class="bi bi-clock-history fs-4 d-block mb-1"></i>
                                    No extensions requested yet. You can request up to 3 extensions for this project.
                                </div>
                            @endif

                            {{-- Request button --}}
                            @if($extCount < 3)
                                <button class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                        data-bs-target="#extensionModal{{ $project->project_id }}">
                                    <i class="bi bi-plus-circle me-1"></i> Request Extension
                                </button>
                            @else
                                <button class="btn btn-sm btn-secondary" disabled>
                                    <i class="bi bi-lock-fill me-1"></i> Max Extensions Reached (3/3)
                                </button>
                            @endif
                        </div>

                        {{-- Budget Amendment Section --}}
                        <div>
                            <h6 class="fw-bold text-success mb-3"><i class="bi bi-cash-stack me-1"></i> Budget Amendments</h6>

                            @php $amendCount = $project->budgetAmendments->count(); @endphp

                            {{-- Amendment request cards --}}
                            @if($amendCount > 0)
                                <div class="row g-2 mb-3">
                                    @foreach($project->budgetAmendments as $amend)
                                        @php
                                            $borderColor = $amend->status === 'Approved' ? 'success' : ($amend->status === 'Rejected' ? 'danger' : 'warning');
                                            $icon = $amend->status === 'Approved' ? 'bi-check-circle-fill' : ($amend->status === 'Rejected' ? 'bi-x-circle-fill' : 'bi-hourglass-split');
                                            $bgLight = $amend->status === 'Approved' ? 'bg-success-subtle' : ($amend->status === 'Rejected' ? 'bg-danger-subtle' : 'bg-warning-subtle');
                                        @endphp
                                        <div class="col-12">
                                            <div class="card border-{{ $borderColor }} {{ $bgLight }} shadow-sm">
                                                <div class="card-body py-2 px-3">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <div>
                                                            <span class="fw-bold small">ETB {{ number_format($amend->delta_amount, 2) }}</span>
                                                        </div>
                                                        <span class="badge bg-{{ $borderColor }} text-white">
                                                            <i class="bi {{ $icon }} me-1"></i>{{ $amend->status }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center text-muted small py-3 mb-3 border rounded bg-light">
                                    <i class="bi bi-cash-stack fs-4 d-block mb-1"></i>
                                    No budget amendments submitted yet. Use this to request budget adjustments for inflation or cost changes.
                                </div>
                            @endif

                            <button class="btn btn-sm btn-success" data-bs-toggle="modal"
                                    data-bs-target="#amendmentModal{{ $project->project_id }}">
                                <i class="bi bi-plus-circle me-1"></i> Request Budget Amendment
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Extension Modal --}}
            <div class="modal fade" id="extensionModal{{ $project->project_id }}" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form action="{{ route('projects.request-extension', $project->project_id) }}" method="POST">
                            @csrf
                            <div class="modal-header bg-dark text-white">
                                <h6 class="modal-title fw-bold"><i class="bi bi-clock me-2"></i>Request Time Extension</h6>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="alert alert-secondary small mb-3">
                                    <i class="bi bi-info-circle me-1"></i>
                                    <strong>Extension #{{ $extCount + 1 }}:</strong>
                                    @if($extCount < 2)
                                        Approved by Department Coordinator. Max 6 months per extension.
                                    @else
                                        This is your 3rd extension — it requires <strong>RCSC approval</strong> and may take longer to process.
                                    @endif
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Additional Months</label>
                                    <select name="months" class="form-select" required>
                                        @for($m = 1; $m <= 6; $m++)
                                            <option value="{{ $m }}">{{ $m }} month(s)</option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Reason for Extension</label>
                                    <textarea name="reason" class="form-control" rows="4"
                                              placeholder="Explain why you need more time (fieldwork delays, data collection issues, etc.)..." required></textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="button" class="btn btn-dark fw-bold confirm-btn" data-confirm-title="Submit Extension" data-confirm-message="Submit this extension request for approval?" data-confirm-icon="bi-clock-history" data-confirm-color="text-dark" data-confirm-btn-text="Yes, Submit" data-confirm-btn-class="btn-dark">Submit Request</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Amendment Modal --}}
            <div class="modal fade" id="amendmentModal{{ $project->project_id }}" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form action="{{ route('projects.request-amendment', $project->project_id) }}" method="POST">
                            @csrf
                            <div class="modal-header bg-success text-white">
                                <h6 class="modal-title fw-bold"><i class="bi bi-cash me-2"></i>Request Budget Amendment</h6>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="alert alert-warning small mb-3">
                                    <i class="bi bi-exclamation-triangle me-1"></i>
                                    <strong>Note:</strong> Budget amendments require RCSC approval. You must provide valid price inflation justification or documented cost changes.
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Additional Amount (ETB)</label>
                                    <input type="number" name="delta_amount" class="form-control"
                                           min="0" step="0.01" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Justification</label>
                                    <textarea name="justification" class="form-control" rows="4"
                                              placeholder="Provide detailed justification — include invoices, market price comparisons, or official inflation data if available..." required></textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="button" class="btn btn-success fw-bold confirm-btn" data-confirm-title="Submit Amendment" data-confirm-message="Submit this budget amendment for approval?" data-confirm-icon="bi-cash-stack" data-confirm-color="text-success" data-confirm-btn-text="Yes, Submit" data-confirm-btn-class="btn-success">Submit Amendment</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    @endif
@else
    {{-- Coordinator/DH/VP/RCSC view: pending extensions to approve --}}
    @if(in_array(Auth::user()->role, ['coordinator', 'dh', 'vparttcs', 'rcsc']))
    @php
        $pendingExtensions = \App\Models\ProjectExtension::where('status', 'Pending')
            ->with('project.pi')
            ->get();
        $pendingAmendments = \App\Models\BudgetAmendment::where('status', 'Pending')
            ->with('project.pi')
            ->get();
    @endphp

    <div class="row g-4">
        <div class="col-md-6">
            <div class="card card-custom">
                <div class="card-header bg-dark text-white fw-bold">
                    <i class="bi bi-clock-history me-2"></i> Pending Time Extensions ({{ $pendingExtensions->count() }})
                </div>
                <div class="card-body p-0">
                    @forelse($pendingExtensions as $ext)
                    <div class="border-bottom p-3">
                        <h6 class="fw-bold mb-1">{{ $ext->project->title ?? 'Project #' . $ext->project_id }}</h6>
                        <p class="small text-muted mb-2">
                            Extension #{{ $ext->extension_number }} | {{ $ext->requested_months }} months |
                            PI: {{ $ext->project->pi->name ?? 'N/A' }}
                        </p>
                        <p class="small mb-2"><em>"{{ $ext->reason }}"</em></p>
                        <div class="d-flex gap-2">
                            <form action="{{ route('extensions.approve', $ext->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="button" class="btn btn-sm btn-success confirm-btn" data-confirm-title="Approve Extension" data-confirm-message="Approve this time extension request?" data-confirm-icon="bi-check-circle" data-confirm-color="text-success" data-confirm-btn-text="Yes, Approve" data-confirm-btn-class="btn-success">
                                    <i class="bi bi-check-lg me-1"></i> Approve
                                </button>
                            </form>
                            <form action="{{ route('extensions.reject', $ext->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="button" class="btn btn-sm btn-danger confirm-btn" data-confirm-title="Reject Extension" data-confirm-message="Reject this time extension request?" data-confirm-icon="bi-x-circle" data-confirm-color="text-danger" data-confirm-btn-text="Yes, Reject" data-confirm-btn-class="btn-danger">
                                    <i class="bi bi-x-lg me-1"></i> Reject
                                </button>
                            </form>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-check-circle fs-1 d-block mb-2 text-success"></i>
                        <h6 class="fw-bold">All Caught Up!</h6>
                        <p class="small mb-0">No pending extension requests to review at this time.</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card card-custom">
                <div class="card-header bg-dark text-white fw-bold">
                    <i class="bi bi-cash-stack me-2"></i> Pending Budget Amendments ({{ $pendingAmendments->count() }})
                </div>
                <div class="card-body p-0">
                    @forelse($pendingAmendments as $amend)
                    <div class="border-bottom p-3">
                        <h6 class="fw-bold mb-1">{{ $amend->project->title ?? 'Project #' . $amend->project_id }}</h6>
                        <p class="small text-muted mb-2">
                            Amount: ETB {{ number_format($amend->delta_amount, 2) }} |
                            PI: {{ $amend->project->pi->name ?? 'N/A' }}
                        </p>
                        <p class="small mb-2"><em>"{{ $amend->justification }}"</em></p>
                        <div class="d-flex gap-2">
                            <form action="{{ route('amendments.approve', $amend->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="button" class="btn btn-sm btn-success confirm-btn" data-confirm-title="Approve Amendment" data-confirm-message="Approve this budget amendment?" data-confirm-icon="bi-check-circle" data-confirm-color="text-success" data-confirm-btn-text="Yes, Approve" data-confirm-btn-class="btn-success">
                                    <i class="bi bi-check-lg me-1"></i> Approve
                                </button>
                            </form>
                            <form action="{{ route('amendments.reject', $amend->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="button" class="btn btn-sm btn-danger confirm-btn" data-confirm-title="Reject Amendment" data-confirm-message="Reject this budget amendment?" data-confirm-icon="bi-x-circle" data-confirm-color="text-danger" data-confirm-btn-text="Yes, Reject" data-confirm-btn-class="btn-danger">
                                    <i class="bi bi-x-lg me-1"></i> Reject
                                </button>
                            </form>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-check-circle fs-1 d-block mb-2 text-success"></i>
                        <h6 class="fw-bold">All Caught Up!</h6>
                        <p class="small mb-0">No pending budget amendment requests to review at this time.</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- Completed / Ratified Decisions History --}}
    @php
        $completedExtensions = \App\Models\ProjectExtension::where('status', '!=', 'Pending')
            ->with('project.pi')
            ->latest('updated_at')
            ->get();
        $completedAmendments = \App\Models\BudgetAmendment::where('status', '!=', 'Pending')
            ->with('project.pi')
            ->latest('updated_at')
            ->get();
    @endphp

    <div class="row g-4 mt-2">
        <div class="col-12">
            <div class="card card-custom">
                <div class="card-header bg-white border-bottom fw-bold d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-clock-history me-2 text-dark"></i> Decision &amp; Ratification History</span>
                    <span class="badge bg-light text-dark border">{{ $completedExtensions->count() + $completedAmendments->count() }} Decisions</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Type</th>
                                    <th>Project</th>
                                    <th>Principal Investigator</th>
                                    <th>Request Details</th>
                                    <th>Decision Status</th>
                                    <th>Decision Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($completedExtensions as $ext)
                                <tr>
                                    <td><span class="badge bg-dark text-white"><i class="bi bi-clock-history me-1"></i>Time Extension</span></td>
                                    <td class="fw-bold">{{ $ext->project->title ?? 'Project #' . $ext->project_id }}</td>
                                    <td>{{ $ext->project->pi->name ?? 'N/A' }}</td>
                                    <td>Extension #{{ $ext->extension_number }} (+{{ $ext->requested_months }} months)</td>
                                    <td>
                                        @if($ext->status === 'Approved')
                                            <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Approved</span>
                                        @else
                                            <span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i>Rejected</span>
                                        @endif
                                    </td>
                                    <td class="small text-muted">{{ $ext->updated_at ? $ext->updated_at->format('M d, Y') : '-' }}</td>
                                </tr>
                                @empty
                                @endforelse

                                @forelse($completedAmendments as $amend)
                                <tr>
                                    <td><span class="badge bg-success text-white"><i class="bi bi-cash-stack me-1"></i>Budget Amendment</span></td>
                                    <td class="fw-bold">{{ $amend->project->title ?? 'Project #' . $amend->project_id }}</td>
                                    <td>{{ $amend->project->pi->name ?? 'N/A' }}</td>
                                    <td>+ETB {{ number_format($amend->delta_amount, 2) }} (<em>"{{ Str::limit($amend->justification, 35) }}"</em>)</td>
                                    <td>
                                        @if($amend->status === 'Approved')
                                            <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Approved &amp; Queued to Finance</span>
                                        @else
                                            <span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i>Rejected</span>
                                        @endif
                                    </td>
                                    <td class="small text-muted">{{ $amend->updated_at ? $amend->updated_at->format('M d, Y') : '-' }}</td>
                                </tr>
                                @empty
                                @endforelse

                                @if($completedExtensions->isEmpty() && $completedAmendments->isEmpty())
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">
                                        <i class="bi bi-folder2-open fs-3 d-block mb-1"></i>
                                        No historical decisions found.
                                    </td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @else
    <div class="text-center py-5">
        <i class="bi bi-shield-lock fs-1 d-block mb-2 text-muted"></i>
        <h5 class="text-muted">Access Restricted</h5>
        <p class="text-muted">You do not have permission to view this page.</p>
    </div>
    @endif
@endif
@endsection
