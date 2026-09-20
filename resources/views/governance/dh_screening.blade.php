@extends('layouts.app')

@section('content')
<h3 class="fw-bold mb-4"><i class="bi bi-ui-checks me-2 text-warning"></i>Department Head Screening Queue</h3>
<div class="card card-custom p-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <span class="text-muted small">Proposals submitted for DH screening and decision</span>
        <span class="badge bg-warning text-dark">{{ $projects->count() }} in Queue</span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th style="width: 40px;"></th>
                    <th>ID</th>
                    <th>Proposal Title</th>
                    <th>PI Name</th>
                    <th>Requested Budget</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($projects as $p)
                <tr id="project-row-{{ $p->project_id }}">
                    <td>
                        <button class="btn btn-sm btn-link text-decoration-none p-0 toggle-details"
                                data-project-id="{{ $p->project_id }}"
                                title="Show details">
                            <i class="bi bi-plus-circle fs-5 text-primary"></i>
                        </button>
                    </td>
                    <td>#{{ $p->project_id }}</td>
                    <td class="fw-bold">{{ $p->title }}</td>
                    <td>{{ $p->pi->name }}</td>
                    <td>{{ number_format($p->requested_budget, 2) }} ETB</td>
                    <td>
                        @if($p->status === 'Submitted')
                            <span class="badge bg-warning text-dark">{{ $p->status }}</span>
                        @elseif(in_array($p->status, ['Approved', 'Completed']))
                            <span class="badge bg-success">{{ $p->status }}</span>
                        @elseif(in_array($p->status, ['Rejected', 'Terminated']))
                            <span class="badge bg-danger">{{ $p->status }}</span>
                        @else
                            <span class="badge bg-secondary">{{ $p->status }}</span>
                        @endif
                    </td>
                    <td>
                        <div class="btn-group btn-group-sm">
                            <a href="{{ route('projects.show', $p->project_id) }}" class="btn btn-outline-info" title="View Full Details">
                                <i class="bi bi-box-arrow-up-right me-1"></i> View Details
                            </a>
                            @if($p->status === 'Submitted')
                            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#dhApproveModal{{ $p->project_id }}">
                                <i class="bi bi-check-lg"></i> Approve
                            </button>
                            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#dhRejectModal{{ $p->project_id }}">
                                <i class="bi bi-x-lg"></i> Reject
                            </button>
                            @endif
                        </div>
                    </td>
                </tr>
                <tr id="details-{{ $p->project_id }}" class="details-row" style="display: none;">
                    <td colspan="7" class="bg-light">
                        <div class="p-3">
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <div class="card h-100 border-0 shadow-sm">
                                        <div class="card-body">
                                            <h6 class="card-title text-primary mb-3">
                                                <i class="bi bi-file-text me-2"></i>Project Abstract
                                            </h6>
                                            <div class="abstract-text" id="abstract-{{ $p->project_id }}">
                                                @if($p->abstract && strlen($p->abstract) > 200)
                                                    <span class="abstract-short">{{ substr($p->abstract, 0, 200) }}...</span>
                                                    <span class="abstract-full" style="display: none;">{{ $p->abstract }}</span>
                                                    <a href="#" class="read-more-link text-primary small" data-project-id="{{ $p->project_id }}">Read more</a>
                                                @elseif($p->abstract)
                                                    {{ $p->abstract }}
                                                @else
                                                    <span class="text-muted fst-italic">No abstract available</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card h-100 border-0 shadow-sm">
                                        <div class="card-body">
                                            <h6 class="card-title text-dark fw-bold mb-3">
                                                <i class="bi bi-info-circle me-2 text-dark"></i>Project Details
                                            </h6>
                                            <ul class="list-unstyled mb-0">
                                                <li class="mb-2">
                                                    <strong class="text-muted">Budget Requested:</strong>
                                                    <span class="fw-bold text-success">{{ number_format($p->requested_budget, 2) }} ETB</span>
                                                    @if($p->requested_budget <= 500000)
                                                        <span class="badge bg-secondary ms-2">Tier 1 - Small</span>
                                                    @elseif($p->requested_budget <= 2000000)
                                                        <span class="badge bg-warning text-dark ms-2">Tier 2 - Medium</span>
                                                    @else
                                                        <span class="badge bg-danger ms-2">Tier 3 - Large</span>
                                                    @endif
                                                </li>
                                                <li class="mb-2">
                                                    <strong class="text-muted">Principal Investigator:</strong>
                                                    <span class="fw-bold">{{ $p->pi->name }}</span>
                                                </li>
                                                <li class="mb-2">
                                                    <strong class="text-muted">Department:</strong>
                                                    <span>{{ $p->pi->department ?? 'N/A' }}</span>
                                                </li>
                                                <li>
                                                    <strong class="text-muted">Thematic Area:</strong>
                                                    <span class="badge bg-secondary">{{ $p->thematic_area ?? 'N/A' }}</span>
                                                </li>
                                            </ul>
                                            @if($p->proposal_document_url)
                                            <div class="mt-3">
                                                <a href="{{ Storage::url($p->proposal_document_url) }}" target="_blank" class="btn btn-sm btn-outline-dark fw-bold">
                                                    <i class="bi bi-file-pdf me-1"></i>View Proposal PDF
                                                </a>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">
                        <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                        No submissions pending DH screening.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@foreach($projects as $p)
@if($p->status === 'Submitted')
<div class="modal fade" id="dhApproveModal{{ $p->project_id }}" tabindex="-1" aria-labelledby="dhApproveModalLabel{{ $p->project_id }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('dh.screening.decision', $p->project_id) }}">
                @csrf
                @method('POST')
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title fw-bold" id="dhApproveModalLabel{{ $p->project_id }}">
                        <i class="bi bi-check-circle me-2"></i>Approve Proposal
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="decision" value="approve">
                    <div class="alert alert-success small py-2">
                        <i class="bi bi-info-circle me-1"></i>
                        Approving will move <strong>{{ $p->title }}</strong> to <span class="badge bg-success">DH_Screened</span> status and forward it to the coordinator.
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">DH Comments (optional)</label>
                        <textarea name="comments" class="form-control" rows="3" placeholder="Add any comments or notes for the coordinator..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success fw-bold px-4">
                        <i class="bi bi-check-circle me-1"></i> Confirm Approval
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="dhRejectModal{{ $p->project_id }}" tabindex="-1" aria-labelledby="dhRejectModalLabel{{ $p->project_id }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('dh.screening.decision', $p->project_id) }}">
                @csrf
                @method('POST')
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title fw-bold" id="dhRejectModalLabel{{ $p->project_id }}">
                        <i class="bi bi-x-circle me-2"></i>Reject Proposal
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="decision" value="reject">
                    <div class="alert alert-danger small py-2">
                        <i class="bi bi-exclamation-triangle me-1"></i>
                        Rejecting will send <strong>{{ $p->title }}</strong> back to <span class="badge bg-warning text-dark">Submitted</span> status with feedback to the PI.
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Reason for Rejection / Feedback <span class="text-danger">*</span></label>
                        <textarea name="comments" class="form-control" rows="4" required placeholder="Explain why this proposal is being rejected or what needs to be revised..."></textarea>
                        <div class="form-text">This feedback will be visible to the Principal Investigator.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger fw-bold px-4">
                        <i class="bi bi-x-circle me-1"></i> Confirm Rejection
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endforeach

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.toggle-details').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            var projectId = this.getAttribute('data-project-id');
            var detailsRow = document.getElementById('details-' + projectId);
            var icon = this.querySelector('i');
            
            if (detailsRow.style.display === 'none') {
                detailsRow.style.display = '';
                icon.classList.remove('bi-plus-circle');
                icon.classList.add('bi-dash-circle');
            } else {
                detailsRow.style.display = 'none';
                icon.classList.remove('bi-dash-circle');
                icon.classList.add('bi-plus-circle');
            }
        });
    });

    document.querySelectorAll('.read-more-link').forEach(function(link) {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            var projectId = this.getAttribute('data-project-id');
            var abstractDiv = document.getElementById('abstract-' + projectId);
            var shortText = abstractDiv.querySelector('.abstract-short');
            var fullText = abstractDiv.querySelector('.abstract-full');
            
            if (fullText.style.display === 'none') {
                shortText.style.display = 'none';
                fullText.style.display = 'inline';
                this.textContent = 'Show less';
            } else {
                shortText.style.display = 'inline';
                fullText.style.display = 'none';
                this.textContent = 'Read more';
            }
        });
    });
});
</script>
@endpush
@endsection
