@extends('layouts.app')

@section('content')
<h3 class="fw-bold mb-4"><i class="bi bi-award me-2 text-danger"></i>RCSC High Budget (&ge;500k ETB) & Defense Portal</h3>

<h5 class="fw-bold mb-3"><i class="bi bi-hourglass-split me-2 text-warning"></i>Pending RCSC Budget Requests ({{ $pending->count() }})</h5>
<div class="card card-custom p-4 mb-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Request ID</th>
                    <th>Project</th>
                    <th>Project Budget</th>
                    <th>Phase</th>
                    <th>Requested Amount</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pending as $r)
                <tr>
                    <td>#REQ-{{ $r->request_id }}</td>
                    <td class="fw-bold">{{ $r->project->title ?? 'N/A' }}
                        @if($r->project && $r->project->proposal_document_url)
                        <a href="{{ Storage::url($r->project->proposal_document_url) }}" target="_blank" class="text-decoration-none ms-1" title="View Proposal PDF">
                            <i class="bi bi-file-pdf text-danger small"></i>
                        </a>
                        @endif
                    </td>
                    <td><span class="fw-bold text-danger">{{ number_format($r->project->requested_budget ?? 0, 2) }} ETB</span></td>
                    <td>Phase {{ $r->milestone_phase }}</td>
                    <td>{{ number_format($r->requested_amount, 2) }} ETB</td>
                    <td><span class="badge bg-warning text-dark">{{ $r->status }}</span></td>
                    <td>
                        <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#rcscDecisionModal{{ $r->request_id }}">
                            <i class="bi bi-check2-square"></i> Review
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">
                        <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                        No pending RCSC budget requests.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<h5 class="fw-bold mb-3"><i class="bi bi-check-circle me-2 text-success"></i>Completed RCSC Decisions ({{ $completed->count() }})</h5>
<div class="card card-custom p-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Request ID</th>
                    <th>Project</th>
                    <th>Requested Amount</th>
                    <th>Approved Amount</th>
                    <th>Decision</th>
                    <th>Decided At</th>
                </tr>
            </thead>
            <tbody>
                @forelse($completed as $r)
                <tr>
                    <td>#REQ-{{ $r->request_id }}</td>
                    <td>{{ $r->project->title ?? 'N/A' }}</td>
                    <td>{{ number_format($r->requested_amount, 2) }} ETB</td>
                    <td>{{ $r->approved_amount ? number_format($r->approved_amount, 2) . ' ETB' : 'N/A' }}</td>
                    <td>
                        @if($r->status === 'Approved')
                            <span class="badge bg-success">{{ $r->status }}</span>
                        @else
                            <span class="badge bg-danger">{{ $r->status }}</span>
                        @endif
                    </td>
                    <td>{{ $r->updated_at ? $r->updated_at->format('M d, Y H:i') : 'N/A' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted py-4">
                        <i class="bi bi-clipboard-check fs-1 d-block mb-2"></i>
                        No completed RCSC decisions yet.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@foreach($pending as $r)
<div class="modal fade" id="rcscDecisionModal{{ $r->request_id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" action="{{ route('rcsc.decision', $r->request_id) }}">
                @csrf
                <div class="modal-header bg-danger-subtle">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-award me-2"></i>RCSC Budget Decision — {{ $r->project->title ?? 'N/A' }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Project</label>
                            <input type="text" class="form-control" value="{{ $r->project->title ?? 'N/A' }}" disabled>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Project Budget</label>
                            <input type="text" class="form-control fw-bold text-danger" value="{{ number_format($r->project->requested_budget ?? 0, 2) }} ETB" disabled>
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Phase</label>
                            <input type="text" class="form-control" value="Phase {{ $r->milestone_phase }}" disabled>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Requested Amount</label>
                            <input type="text" class="form-control" value="{{ number_format($r->requested_amount, 2) }} ETB" disabled>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">RCSC Decision <span class="text-danger">*</span></label>
                        <select name="decision" class="form-select" required id="rcscDecision{{ $r->request_id }}" onchange="toggleRcscSubmitBtn({{ $r->request_id }})">
                            <option value="">-- Select Decision --</option>
                            <option value="Approved">Approved</option>
                            <option value="Rejected">Rejected</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Approved Amount (ETB)</label>
                        <input type="number" name="approved_amount" class="form-control" min="0" step="0.01" placeholder="{{ $r->requested_amount }}">
                        <div class="form-text">Leave blank or equal to requested amount if approving full amount.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Comments</label>
                        <textarea name="comments" class="form-control" rows="3" placeholder="Optional comments on the decision..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger fw-bold px-4 confirm-btn" id="rcscSubmitBtn{{ $r->request_id }}" data-confirm-title="Submit RCSC Decision" data-confirm-message="Submit your RCSC budget decision? This action cannot be undone." data-confirm-icon="bi-check-circle" data-confirm-color="text-danger" data-confirm-btn-text="Yes, Submit" data-confirm-btn-class="btn-danger">
                        <i class="bi bi-check-circle me-1"></i> Submit RCSC Decision
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

<script>
function toggleRcscSubmitBtn(id) {
    var sel = document.getElementById('rcscDecision' + id);
    var btn = document.getElementById('rcscSubmitBtn' + id);
    if (sel.value === 'Approved') {
        btn.className = 'btn btn-success fw-bold px-4 confirm-btn';
    } else if (sel.value === 'Rejected') {
        btn.className = 'btn btn-danger fw-bold px-4 confirm-btn';
    } else {
        btn.className = 'btn btn-danger fw-bold px-4 confirm-btn';
    }
}
</script>
@endsection
