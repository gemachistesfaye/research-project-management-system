@extends('layouts.app')

@section('content')
<style>
    .workload-bar { height: 8px; border-radius: 4px; background: #e9ecef; overflow: hidden; }
    .workload-fill { height: 100%; border-radius: 4px; transition: width 0.3s ease; }
    .workload-low { background: #198754; }
    .workload-medium { background: #ffc107; }
    .workload-high { background: #dc3545; }
    .reviewer-card { border-left: 4px solid #212529; transition: transform 0.15s ease; }
    .reviewer-card:hover { transform: translateX(4px); }
    .empty-state-icon { font-size: 3rem; color: #adb5bd; }
    .sidebar-section { max-height: 500px; overflow-y: auto; }
</style>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-1"><i class="bi bi-briefcase me-2 text-dark"></i>Coordinator Management Hub</h3>
        <p class="text-muted mb-0">Manage blind peer reviewer assignments for DH-screened proposals.</p>
    </div>
    <a href="{{ route('certificates') }}" class="btn btn-outline-dark fw-bold">
        <i class="bi bi-award me-1"></i> Certificates
    </a>
</div>

<div class="row g-4">
    {{-- Main Content: Projects Table --}}
    <div class="col-12">
        <div class="card card-custom">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold"><i class="bi bi-list-ul me-2"></i>Projects Awaiting Assignment</h6>
                <span class="badge bg-dark">{{ $projects->count() }} Projects</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Proposal Title</th>
                                <th>PI Name</th>
                                <th>Assigned</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($projects as $p)
                            <tr>
                                <td>#{{ $p->project_id }}</td>
                                <td class="fw-bold">{{ $p->title }}
                                    @if($p->proposal_document_url)
                                    <a href="{{ Storage::url($p->proposal_document_url) }}" target="_blank" class="text-decoration-none ms-1" title="View Proposal PDF">
                                        <i class="bi bi-file-pdf text-danger small"></i>
                                    </a>
                                    @endif
                                </td>
                                <td>{{ $p->pi->name }}</td>
                                <td>
                                    @php $count = $p->evaluations->count(); @endphp
                                    @if($count === 0)
                                        <span class="badge bg-warning text-dark"><i class="bi bi-exclamation-triangle me-1"></i>0 Reviewers</span>
                                    @elseif($count < 3)
                                        <span class="badge bg-secondary text-white"><i class="bi bi-person-check me-1"></i>{{ $count }} Reviewer{{ $count > 1 ? 's' : '' }}</span>
                                    @else
                                        <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>{{ $count }} Reviewers</span>
                                    @endif
                                </td>
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
                                    <div class="d-flex align-items-center gap-1 flex-wrap">
                                        <a href="{{ route('projects.show', $p->project_id) }}" class="btn btn-sm btn-dark py-1 px-2" style="font-size: 0.78rem;">
                                            <i class="bi bi-pencil-square me-1"></i>Manage
                                        </a>
                                        <a href="{{ route('progress.show', $p->project_id) }}" class="btn btn-sm btn-outline-dark py-1 px-2" style="font-size: 0.78rem;" title="View & Audit Progress Reports">
                                            <i class="bi bi-graph-up me-1"></i>Progress
                                        </a>
                                        <button class="btn btn-sm btn-outline-dark py-1 px-2 quick-assign-btn"
                                                data-project-id="{{ $p->project_id }}"
                                                data-project-title="{{ $p->title }}"
                                                style="font-size: 0.78rem;">
                                            <i class="bi bi-lightning me-1"></i>Quick Assign
                                        </button>
                                        @if(!$p->irercClearance)
                                        <form action="{{ route('projects.create-irerc-clearance', $p->project_id) }}" method="POST" class="d-inline mb-0">
                                            @csrf
                                            <button type="button" class="btn btn-sm btn-outline-dark py-1 px-2 fw-semibold confirm-btn" style="font-size: 0.78rem;" data-confirm-title="Request Ethics Clearance" data-confirm-message="Route this project to IRERC for ethics clearance?" data-confirm-icon="bi-shield-check" data-confirm-color="text-dark" data-confirm-btn-text="Yes, Send" data-confirm-btn-class="btn-dark">
                                                <i class="bi bi-shield-plus me-1"></i>Send to Ethics
                                            </button>
                                        </form>
                                        @else
                                            @if($p->irercClearance->status === 'Approved')
                                                <span class="badge bg-success-subtle text-success border border-success-subtle py-1 px-2" style="font-size: 0.72rem;"><i class="bi bi-shield-check me-1"></i>Ethics Cleared</span>
                                            @elseif($p->irercClearance->status === 'Rejected')
                                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle py-1 px-2" style="font-size: 0.72rem;"><i class="bi bi-shield-x me-1"></i>Ethics Rejected</span>
                                            @else
                                                <span class="badge bg-warning-subtle text-dark border border-warning-subtle py-1 px-2" style="font-size: 0.72rem;"><i class="bi bi-hourglass-split me-1"></i>Ethics Pending</span>
                                            @endif
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="empty-state-icon mb-3">
                                        <i class="bi bi-inbox"></i>
                                    </div>
                                    <h5 class="text-muted mb-2">No Projects Awaiting Assignment</h5>
                                    <p class="text-muted mb-3" style="max-width: 400px; margin: 0 auto;">
                                        All DH-screened proposals have been assigned reviewers, or there are no proposals pending coordinator review.
                                    </p>
                                    <div class="d-flex justify-content-center gap-2">
                                        <span class="badge bg-light text-dark border px-3 py-2">
                                            <i class="bi bi-info-circle me-1"></i>Projects appear here after DH Screening approval
                                        </span>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Bottom Section: Available Reviewers Grid --}}
    <div class="col-12">
        <div class="card card-custom">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold"><i class="bi bi-people me-2"></i>Available Reviewers</h6>
                <span class="badge bg-dark">{{ $reviewers->count() }} Reviewers</span>
            </div>
            <div class="card-body p-3">
                <div class="row g-3">
                    @forelse($reviewers as $r)
                    <div class="col-lg-4 col-md-6">
                        <div class="p-3 border rounded-3 bg-light reviewer-card h-100">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <h6 class="mb-0 fw-bold">{{ $r['user']->name }}</h6>
                                    <small class="text-muted">{{ $r['user']->department->name ?? 'Faculty' }}</small>
                                </div>
                                <span class="badge bg-white text-dark border">{{ $r['user']->staff_id }}</span>
                            </div>
                            <div class="d-flex gap-3 mb-2 small">
                                <span><i class="bi bi-clipboard-check text-dark me-1"></i><strong>{{ $r['active_reviews'] }}</strong> Active</span>
                                <span><i class="bi bi-check2-all text-success me-1"></i><strong>{{ $r['total_evaluations'] }}</strong> Done</span>
                                <span><i class="bi bi-star text-warning me-1"></i><strong>{{ $r['avg_score'] }}</strong> Avg</span>
                            </div>
                            <div class="workload-bar">
                                @php
                                    $workloadClass = 'workload-low';
                                    if ($r['workload_percent'] > 60) $workloadClass = 'workload-high';
                                    elseif ($r['workload_percent'] > 30) $workloadClass = 'workload-medium';
                                @endphp
                                <div class="workload-fill {{ $workloadClass }}" style="width: {{ $r['workload_percent'] }}%"></div>
                            </div>
                            <small class="text-muted mt-1 d-block">Workload: {{ number_format($r['workload_percent'], 0) }}%</small>
                        </div>
                    </div>
                    @empty
                    <div class="col-12 text-center py-4">
                        <i class="bi bi-person-x empty-state-icon mb-2"></i>
                        <p class="text-muted mb-0">No reviewers available for assignment.</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Quick Assign Modal --}}
<div class="modal fade" id="quickAssignModal" tabindex="-1" aria-labelledby="quickAssignModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title" id="quickAssignModalLabel">
                    <i class="bi bi-lightning me-2"></i>Quick Assign Reviewer
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="assignReviewerForm" method="POST">
                    @csrf
                    <input type="hidden" name="examiner_id" id="modal-reviewer-id">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Project</label>
                        <input type="text" class="form-control" id="modal-project-title" readonly>
                        <input type="hidden" name="project_id" id="modal-project-id">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Select Reviewer</label>
                        <select class="form-select" id="modal-reviewer-select" name="examiner_id" required>
                            <option value="">Choose a reviewer...</option>
                            @foreach($reviewers as $r)
                            <option value="{{ $r['user']->id }}">
                                {{ $r['user']->name }} - {{ $r['user']->department->name ?? 'N/A' }}
                                ({{ $r['active_reviews'] }} active, Avg: {{ $r['avg_score'] }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div id="reviewer-workload-info" class="alert alert-secondary d-none">
                    <small>
                        <i class="bi bi-info-circle me-1"></i>
                        This reviewer currently has <strong id="info-active"></strong> active reviews.
                    </small>
                </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-dark" id="confirm-assign-btn">
                    <i class="bi bi-check-circle me-1"></i>Assign Reviewer
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const modal = new bootstrap.Modal(document.getElementById('quickAssignModal'));

    document.querySelectorAll('.quick-assign-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            document.getElementById('modal-project-id').value = this.dataset.projectId;
            document.getElementById('modal-project-title').value = this.dataset.projectTitle;
            document.getElementById('modal-reviewer-select').value = '';
            document.getElementById('reviewer-workload-info').classList.add('d-none');
            modal.show();
        });
    });

    document.getElementById('modal-reviewer-select').addEventListener('change', function () {
        var selected = this.options[this.selectedIndex];
        if (this.value) {
            var activeCount = selected.text.match(/\((\d+) active/);
            if (activeCount) {
                document.getElementById('info-active').textContent = activeCount[1];
                document.getElementById('reviewer-workload-info').classList.remove('d-none');
            }
        } else {
            document.getElementById('reviewer-workload-info').classList.add('d-none');
        }
    });

    document.getElementById('confirm-assign-btn').addEventListener('click', function () {
        var form = document.getElementById('assignReviewerForm');
        var reviewerId = document.getElementById('modal-reviewer-select').value;
        if (!reviewerId) {
            alert('Please select a reviewer.');
            return;
        }
        var projectId = document.getElementById('modal-project-id').value;
        form.action = '{{ url("/projects") }}/' + projectId + '/assign-reviewer';
        form.submit();
    });
});
</script>
@endpush
@endsection
