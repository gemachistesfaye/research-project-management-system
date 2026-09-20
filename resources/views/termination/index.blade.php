@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-0"><i class="bi bi-exclamation-triangle me-2 text-danger"></i> Termination & Refund (SCR-11)</h3>
        <span class="text-muted">Manage project terminations and refund calculations</span>
    </div>
</div>

@if(Auth::user()->role === 'pi')
    @php
        $myProjects = \App\Models\Project::where('pi_id', Auth::id())
            ->whereIn('status', ['Active', 'Approved', 'Terminated'])
            ->with(['termination', 'budgetRequests'])
            ->get();
    @endphp

    <div class="row g-3">
        @foreach($myProjects as $project)
        <div class="col-md-6">
            <div class="card card-custom h-100">
                <div class="card-header bg-white border-bottom">
                    <div class="d-flex justify-content-between">
                        <h6 class="fw-bold mb-0">{{ Str::limit($project->title, 40) }}</h6>
                        @if($project->proposal_document_url)
                        <a href="{{ Storage::url($project->proposal_document_url) }}" target="_blank" class="text-decoration-none" title="View Proposal Document">
                            <i class="bi bi-file-pdf text-danger small"></i>
                        </a>
                        @endif
                        <span class="badge bg-{{ $project->status === 'Terminated' ? 'danger' : 'success' }}">{{ $project->status }}</span>
                    </div>
                </div>
                <div class="card-body">
                    @if($project->termination)
                        <div class="alert alert-danger py-2">
                            <strong>Terminated:</strong> {{ $project->termination->reason }}<br>
                            <strong>Refund Due:</strong> ETB {{ number_format($project->termination->refund_due, 2) }}
                        </div>
                    @elseif(in_array($project->status, ['Active', 'Approved']))
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="small text-muted">Approved Budget</span>
                                <span class="small fw-bold">ETB {{ number_format($project->requested_budget, 2) }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-1">
                                <span class="small text-muted">Status</span>
                                <span class="badge bg-{{ $project->status === 'Approved' ? 'success' : 'dark' }}">{{ $project->status }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-1">
                                <span class="small text-muted">Days Active</span>
                                <span class="small fw-bold">{{ $project->created_at->diffForHumans([], \Carbon\CarbonInterface::DIFF_ABSOLUTE) }}</span>
                            </div>
                        </div>

                        <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal"
                                data-bs-target="#terminateModal{{ $project->project_id }}">
                            <i class="bi bi-x-circle me-1"></i> Request Termination
                        </button>
                    @endif
                </div>
            </div>
        </div>

        {{-- Termination Modal --}}
        <div class="modal fade" id="terminateModal{{ $project->project_id }}" tabindex="-1">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                    <form action="{{ route('projects.terminate', $project->project_id) }}" method="POST" id="termForm{{ $project->project_id }}">
                        @csrf
                        <div class="modal-header bg-danger text-white py-2">
                            <h6 class="modal-title fw-bold" style="white-space:normal;"><i class="bi bi-exclamation-triangle me-2"></i>Terminate Project</h6>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">

                            {{-- Step Indicators --}}
                            <div class="d-flex justify-content-center mb-4">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-danger rounded-circle p-2 term-step-badge step1-active" data-project="{{ $project->project_id }}" id="stepBadge1_{{ $project->project_id }}">1</span>
                                    <span class="small fw-bold term-step-label step1-active" data-project="{{ $project->project_id }}" id="stepLabel1_{{ $project->project_id }}">Fill Form</span>
                                    <div class="border-top border-2 w-50 mx-2 term-step-line" data-project="{{ $project->project_id }}" id="stepLine{{ $project->project_id }}"></div>
                                    <span class="badge bg-secondary rounded-circle p-2 term-step-badge" data-project="{{ $project->project_id }}" id="stepBadge2_{{ $project->project_id }}">2</span>
                                    <span class="small fw-bold text-muted term-step-label" data-project="{{ $project->project_id }}" id="stepLabel2_{{ $project->project_id }}">Review & Confirm</span>
                                </div>
                            </div>

                            {{-- DANGER BANNER --}}
                            <div class="alert alert-danger d-flex align-items-center small mb-3 py-2" style="white-space:normal; word-wrap:break-word;">
                                <i class="bi bi-shield-exclamation fs-4 me-2"></i>
                                <div>
                                    <strong>Critical Warning:</strong> Project termination is <u>irreversible</u>.
                                    All allocated funds will be recalled and the project record will be permanently marked as terminated.
                                </div>
                            </div>

                            {{-- STEP 1: Form --}}
                            <div id="termStep1_{{ $project->project_id }}">
                                {{-- Project Details --}}
                                <div class="card border-danger mb-3">
                                    <div class="card-header bg-light fw-bold small">
                                        <i class="bi bi-info-circle me-1"></i> Project Details
                                    </div>
                                    <div class="card-body py-2">
                                        <div class="row small">
                                            <div class="col-6 mb-1">
                                                <span class="text-muted">Title:</span>
                                                <span class="fw-bold">{{ Str::limit($project->title, 35) }}</span>
                                            </div>
                                            <div class="col-6 mb-1">
                                                <span class="text-muted">Approved Budget:</span>
                                                <span class="fw-bold text-primary">ETB {{ number_format($project->requested_budget, 2) }}</span>
                                            </div>
                                            <div class="col-6 mb-1">
                                                <span class="text-muted">Status:</span>
                                                <span class="badge bg-success">{{ $project->status }}</span>
                                            </div>
                                            <div class="col-6">
                                                <span class="text-muted">Active Since:</span>
                                                <span class="fw-bold">{{ $project->created_at->format('M d, Y') }} ({{ $project->created_at->diffForHumans([], \Carbon\CarbonInterface::DIFF_ABSOLUTE) }})</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Reason for Termination <span class="text-danger">*</span></label>
                                    <textarea name="reason" class="form-control" rows="4"
                                              placeholder="Provide a detailed explanation for terminating this project..." required></textarea>
                                    <div class="form-text">Explain the circumstances and justification for termination.</div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Verified Asset Value (ETB) <span class="text-danger">*</span></label>
                                    <input type="number" name="verified_value" class="form-control"
                                           min="0" step="0.01" value="0" required
                                           id="verifiedValue_{{ $project->project_id }}"
                                           oninput="calculateRefund({{ $project->project_id }}, {{ $project->requested_budget }})">
                                    <div class="form-text">Total value of purchased equipment/assets to be returned.</div>
                                </div>

                                {{-- Refund Calculator --}}
                                <div class="card border-warning mb-2">
                                    <div class="card-header bg-warning bg-opacity-10 fw-bold small">
                                        <i class="bi bi-calculator me-1"></i> Refund Calculator
                                    </div>
                                    <div class="card-body py-2">
                                        <div class="d-flex justify-content-between mb-2 small">
                                            <span>Approved Budget</span>
                                            <span class="fw-bold">ETB {{ number_format($project->requested_budget, 2) }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2 small">
                                            <span>Verified Asset Value</span>
                                            <span class="fw-bold text-danger" id="assetDisplay_{{ $project->project_id }}">ETB 0.00</span>
                                        </div>
                                        <hr class="my-2">
                                        <div class="d-flex justify-content-between">
                                            <span class="fw-bold text-success"><i class="bi bi-arrow-return-right me-1"></i>Refund Amount</span>
                                            <span class="fw-bold fs-5 text-success" id="refundDisplay_{{ $project->project_id }}">ETB {{ number_format($project->requested_budget, 2) }}</span>
                                        </div>
                                        <div class="small text-muted mt-1" id="refundNote_{{ $project->project_id }}">
                                            Formula: Approved Budget &minus; Verified Asset Value = Refund
                                        </div>
                                    </div>
                                </div>

                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="termAcknowledge_{{ $project->project_id }}" required>
                                    <label class="form-check-label small" for="termAcknowledge_{{ $project->project_id }}">
                                        I understand that this action is <strong>permanent</strong> and cannot be undone.
                                    </label>
                                </div>
                            </div>

                            {{-- STEP 2: Review & Confirm --}}
                            <div id="termStep2_{{ $project->project_id }}" style="display:none;">
                                <div class="alert alert-danger d-flex align-items-center mb-3">
                                    <i class="bi bi-exclamation-octagon-fill fs-3 me-2"></i>
                                    <div class="fw-bold">Final Review — Please verify all details below before confirming.</div>
                                </div>

                                <div class="card border-dark mb-3">
                                    <div class="card-header bg-dark text-white fw-bold small">
                                        <i class="bi bi-clipboard-check me-1"></i> Termination Summary
                                    </div>
                                    <div class="card-body">
                                        <div class="row small mb-2">
                                            <div class="col-5 text-muted">Project Title:</div>
                                            <div class="col-7 fw-bold">{{ Str::limit($project->title, 40) }}</div>
                                        </div>
                                        <div class="row small mb-2">
                                            <div class="col-5 text-muted">Approved Budget:</div>
                                            <div class="col-7 fw-bold">ETB {{ number_format($project->requested_budget, 2) }}</div>
                                        </div>
                                        <div class="row small mb-2">
                                            <div class="col-5 text-muted">Verified Asset Value:</div>
                                            <div class="col-7 fw-bold text-danger" id="reviewAsset_{{ $project->project_id }}">ETB 0.00</div>
                                        </div>
                                        <hr>
                                        <div class="row small mb-2">
                                            <div class="col-5 text-success fw-bold">Refund Due:</div>
                                            <div class="col-7 fw-bold text-success fs-5" id="reviewRefund_{{ $project->project_id }}">ETB {{ number_format($project->requested_budget, 2) }}</div>
                                        </div>
                                        <hr>
                                        <div class="row small mb-2">
                                            <div class="col-5 text-muted">Reason:</div>
                                            <div class="col-7 fw-bold" id="reviewReason_{{ $project->project_id }}">—</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="alert alert-warning d-flex align-items-center small mb-0">
                                    <i class="bi bi-info-circle-fill me-2 fs-5"></i>
                                    <div>
                                        The refund amount will be reviewed by the Finance Office.
                                        You may be required to return assets or provide proof of disposal.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer py-2">
                            <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-warning fw-bold btn-sm" id="nextBtn_{{ $project->project_id }}"
                                    onclick="goToStep2({{ $project->project_id }})">
                                <i class="bi bi-arrow-right-circle me-1"></i> Next: Review
                            </button>
                            <button type="button" class="btn btn-danger fw-bold btn-sm confirm-btn" id="confirmBtn_{{ $project->project_id }}" style="display:none;"
                                    data-confirm-title="Terminate Project" data-confirm-message="FINAL CONFIRMATION: Are you absolutely sure you want to terminate this project? This action CANNOT be undone." data-confirm-icon="bi-exclamation-triangle" data-confirm-color="text-danger" data-confirm-btn-text="Yes, Terminate" data-confirm-btn-class="btn-danger">
                                <i class="bi bi-exclamation-triangle me-1"></i> Confirm Termination
                            </button>
                            <button type="button" class="btn btn-outline-danger fw-bold btn-sm" id="backBtn_{{ $project->project_id }}" style="display:none;"
                                    onclick="goToStep1({{ $project->project_id }})">
                                <i class="bi bi-arrow-left-circle me-1"></i> Back
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <script>
        function calculateRefund(projectId, budget) {
            var verified = parseFloat(document.getElementById('verifiedValue_' + projectId).value) || 0;
            var refund = budget - verified;
            if (refund < 0) refund = 0;

            document.getElementById('assetDisplay_' + projectId).textContent = 'ETB ' + verified.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
            document.getElementById('refundDisplay_' + projectId).textContent = 'ETB ' + refund.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});

            if (verified > 0) {
                document.getElementById('refundNote_' + projectId).textContent = 'ETB ' + budget.toLocaleString('en-US', {minimumFractionDigits: 2}) + ' − ETB ' + verified.toLocaleString('en-US', {minimumFractionDigits: 2}) + ' = ETB ' + refund.toLocaleString('en-US', {minimumFractionDigits: 2});
            } else {
                document.getElementById('refundNote_' + projectId).textContent = 'Formula: Approved Budget − Verified Asset Value = Refund';
            }

            document.getElementById('reviewAsset_' + projectId).textContent = 'ETB ' + verified.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
            document.getElementById('reviewRefund_' + projectId).textContent = 'ETB ' + refund.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        }

        function goToStep2(projectId) {
            var form = document.getElementById('termForm' + projectId);
            if (!form.checkValidity()) { form.reportValidity(); return; }

            var reasonVal = form.querySelector('textarea[name="reason"]').value.trim();
            document.getElementById('reviewReason_' + projectId).textContent = reasonVal || '—';

            document.getElementById('termStep1_' + projectId).style.display = 'none';
            document.getElementById('termStep2_' + projectId).style.display = 'block';

            document.getElementById('nextBtn_' + projectId).style.display = 'none';
            document.getElementById('confirmBtn_' + projectId).style.display = 'inline-block';
            document.getElementById('backBtn_' + projectId).style.display = 'inline-block';

            document.getElementById('stepBadge1_' + projectId).className = 'badge bg-success rounded-circle p-2 term-step-badge';
            document.getElementById('stepLabel1_' + projectId).className = 'small fw-bold text-success term-step-label';
            document.getElementById('stepLine{{ $project->project_id }}').className = 'border-top border-2 w-50 mx-2 term-step-line border-success';
            document.getElementById('stepBadge2_' + projectId).className = 'badge bg-danger rounded-circle p-2 term-step-badge';
            document.getElementById('stepLabel2_' + projectId).className = 'small fw-bold text-danger term-step-label';
        }

        function goToStep1(projectId) {
            document.getElementById('termStep1_' + projectId).style.display = 'block';
            document.getElementById('termStep2_' + projectId).style.display = 'none';

            document.getElementById('nextBtn_' + projectId).style.display = 'inline-block';
            document.getElementById('confirmBtn_' + projectId).style.display = 'none';
            document.getElementById('backBtn_' + projectId).style.display = 'none';

            document.getElementById('stepBadge1_' + projectId).className = 'badge bg-danger rounded-circle p-2 term-step-badge';
            document.getElementById('stepLabel1_' + projectId).className = 'small fw-bold text-danger term-step-label';
            document.getElementById('stepLine{{ $project->project_id }}').className = 'border-top border-2 w-50 mx-2 term-step-line border-danger';
            document.getElementById('stepBadge2_' + projectId).className = 'badge bg-secondary rounded-circle p-2 term-step-badge';
            document.getElementById('stepLabel2_' + projectId).className = 'small fw-bold text-muted term-step-label';
        }
        </script>
        @endforeach
    </div>

@elseif(in_array(Auth::user()->role, ['coordinator', 'admin', 'finance']))
    {{-- Coordinator/Admin view: pending terminations --}}
    @php
        $pendingTerminations = \App\Models\ProjectTermination::where('status', 'Pending')
            ->with(['project.pi'])
            ->get();
        $completedTerminations = \App\Models\ProjectTermination::where('status', '!=', 'Pending')
            ->with(['project.pi'])
            ->latest()
            ->get();
    @endphp

    <div class="row g-4">
        <div class="col-md-6">
            <div class="card card-custom">
                <div class="card-header bg-danger text-white fw-bold">
                    <i class="bi bi-exclamation-triangle me-2"></i> Pending Terminations ({{ $pendingTerminations->count() }})
                </div>
                <div class="card-body p-0">
                    @forelse($pendingTerminations as $term)
                    <div class="border-bottom p-3">
                        <h6 class="fw-bold mb-1">{{ $term->project->title ?? 'Project #' . $term->project_id }}</h6>
                        <p class="small text-muted mb-1">
                            PI: {{ $term->project->pi->name ?? 'N/A' }} |
                            Refund Due: <strong>ETB {{ number_format($term->refund_due, 2) }}</strong>
                        </p>
                        <p class="small mb-2"><em>"{{ $term->reason }}"</em></p>
                        <div class="d-flex gap-2">
                            <form action="{{ route('termination.approve', $term->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="button" class="btn btn-sm btn-success confirm-btn" data-confirm-title="Approve Termination" data-confirm-message="Approve this project termination? This action cannot be undone." data-confirm-icon="bi-check-circle" data-confirm-color="text-success" data-confirm-btn-text="Yes, Approve" data-confirm-btn-class="btn-success">
                                    <i class="bi bi-check-lg me-1"></i> Approve
                                </button>
                            </form>
                            <form action="{{ route('termination.reject', $term->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="button" class="btn btn-sm btn-danger confirm-btn" data-confirm-title="Reject Termination" data-confirm-message="Reject this termination request?" data-confirm-icon="bi-x-circle" data-confirm-color="text-danger" data-confirm-btn-text="Yes, Reject" data-confirm-btn-class="btn-danger">
                                    <i class="bi bi-x-lg me-1"></i> Reject
                                </button>
                            </form>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-exclamation-triangle fs-3 d-block mb-2"></i>
                        No pending terminations.
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card card-custom">
                <div class="card-header bg-white border-bottom fw-bold">
                    <i class="bi bi-clock-history me-2 text-secondary"></i> Processed Terminations
                </div>
                <div class="card-body p-0">
                    @forelse($completedTerminations->take(10) as $term)
                    <div class="border-bottom p-3">
                        <div class="d-flex justify-content-between">
                            <h6 class="fw-bold mb-0 small">{{ $term->project->title ?? 'Project #' . $term->project_id }}</h6>
                            <span class="badge bg-{{ $term->status === 'Approved' ? 'success' : 'danger' }}">{{ $term->status }}</span>
                        </div>
                        <p class="small text-muted mb-0">
                            Refund: ETB {{ number_format($term->refund_due, 2) }} |
                            {{ $term->created_at ? $term->created_at->diffForHumans() : '' }}
                        </p>
                    </div>
                    @empty
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-clipboard-check fs-3 d-block mb-2"></i>
                        No processed terminations.
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@else
    <div class="alert alert-secondary">
        <i class="bi bi-lock me-2"></i>You don't have access to this page.
    </div>
@endif
@endsection
