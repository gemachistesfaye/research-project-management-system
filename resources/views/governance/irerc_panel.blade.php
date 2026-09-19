@extends('layouts.app')

@section('content')
<h3 class="fw-bold mb-4"><i class="bi bi-shield-exclamation me-2 text-dark"></i>IRERC Institutional Ethics Review Panel</h3>

<div class="card card-custom border-0 mb-4" style="background: #1e293b;">
    <div class="card-body">
        <h6 class="fw-bold text-white mb-3"><i class="bi bi-info-circle me-1"></i> Risk Level Definitions</h6>
        <div class="row g-3">
            <div class="col-md-6 col-lg-3">
                <div class="d-flex align-items-center">
                    <span class="badge bg-secondary me-2" style="min-width: 60px;">Low</span>
                    <small class="text-light">Minimal risk, no sensitive data.</small>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="d-flex align-items-center">
                    <span class="badge bg-secondary me-2" style="min-width: 60px;">Medium</span>
                    <small class="text-light">Some risk, standard protocols needed.</small>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="d-flex align-items-center">
                    <span class="badge bg-dark border border-secondary me-2" style="min-width: 60px;">High</span>
                    <small class="text-light">Significant risk, full review required.</small>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="d-flex align-items-center">
                    <span class="badge bg-dark border border-danger me-2" style="min-width: 60px;">Critical</span>
                    <small class="text-light">Extreme risk, board review required.</small>
                </div>
            </div>
        </div>
    </div>
</div>

<h5 class="fw-bold mb-3"><i class="bi bi-hourglass-split me-2 text-dark"></i>Pending Ethics Reviews ({{ $pending->count() }})</h5>
<div class="card card-custom border-0 p-4 mb-4" style="border-left: 4px solid #334155 !important;">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-dark">
                <tr>
                    <th>Clearance ID</th>
                    <th>Project</th>
                    <th>Risk Level</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pending as $c)
                <tr>
                    <td><code>#{{ $c->id }}</code></td>
                    <td class="fw-bold">{{ $c->project->title ?? 'N/A' }}
                        @if($c->project && $c->project->proposal_document_url)
                        <a href="{{ Storage::url($c->project->proposal_document_url) }}" target="_blank" class="text-decoration-none ms-1" title="View Proposal PDF">
                            <i class="bi bi-file-earmark-text small text-dark"></i>
                        </a>
                        @endif
                    </td>
                    <td>
                        @if($c->risk_level === 'Low')
                            <span class="badge bg-secondary">{{ $c->risk_level }}</span>
                        @elseif($c->risk_level === 'Medium')
                            <span class="badge bg-dark border border-secondary">{{ $c->risk_level }}</span>
                        @else
                            <span class="badge bg-dark border border-danger">{{ $c->risk_level }}</span>
                        @endif
                    </td>
                    <td><span class="badge bg-dark border border-warning"><i class="bi bi-hourglass-split me-1"></i>{{ $c->status }}</span></td>
                    <td>
                        <div class="btn-group btn-group-sm">
                            <a href="{{ route('projects.show', $c->project->project_id) }}" class="btn btn-outline-dark">
                                <i class="bi bi-eye"></i>
                            </a>
                            <button type="button" class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#irercDecisionModal{{ $c->id }}">
                                <i class="bi bi-check2-square"></i> Review
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center text-muted py-4">
                        <i class="bi bi-shield-check fs-1 d-block mb-2"></i>
                        No pending ethical clearances.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<h5 class="fw-bold mb-3"><i class="bi bi-check-circle me-2 text-dark"></i>Completed Ethics Reviews ({{ $completed->count() }})</h5>
<div class="card card-custom border-0 p-4" style="border-left: 4px solid #334155 !important;">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-dark">
                <tr>
                    <th>Clearance ID</th>
                    <th>Project</th>
                    <th>Risk Level</th>
                    <th>Decision</th>
                    <th>Clearance Code</th>
                    <th>Decided At</th>
                </tr>
            </thead>
            <tbody>
                @forelse($completed as $c)
                <tr>
                    <td><code>#{{ $c->id }}</code></td>
                    <td>{{ $c->project->title ?? 'N/A' }}</td>
                    <td>
                        @if($c->risk_level === 'Low')
                            <span class="badge bg-secondary">{{ $c->risk_level }}</span>
                        @elseif($c->risk_level === 'Medium')
                            <span class="badge bg-dark border border-secondary">{{ $c->risk_level }}</span>
                        @else
                            <span class="badge bg-dark border border-danger">{{ $c->risk_level }}</span>
                        @endif
                    </td>
                    <td>
                        @if($c->status === 'Approved')
                            <span class="badge bg-dark border border-success">{{ $c->status }}</span>
                        @else
                            <span class="badge bg-dark border border-danger">{{ $c->status }}</span>
                        @endif
                    </td>
                    <td><code>{{ $c->clearance_code ?: 'N/A' }}</code></td>
                    <td>{{ $c->issued_at ? $c->issued_at->format('M d, Y H:i') : 'N/A' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted py-4">
                        <i class="bi bi-clipboard-check fs-1 d-block mb-2"></i>
                        No completed ethics reviews yet.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-3 p-3" style="background: #f1f5f9;">
        <h6 class="fw-bold mb-1"><i class="bi bi-info-circle me-1"></i> Clearance Code Format</h6>
        <small class="text-muted">Format: <code>IRERC-[YEAR]-[ID]</code> — e.g., <code>IRERC-2026-00142</code>. The clearance code is auto-generated upon approval and must be referenced in all project documentation and submissions.</small>
    </div>
</div>

@foreach($pending as $c)
<div class="modal fade" id="irercDecisionModal{{ $c->id }}" tabindex="-1" aria-labelledby="irercDecisionModalLabel{{ $c->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <form method="POST" action="{{ route('irerc.decision', $c->id) }}">
                @csrf
                <div class="modal-header py-2" style="background: #1e293b;">
                    <h5 class="modal-title fw-bold text-white" id="irercDecisionModalLabel{{ $c->id }}" style="white-space:normal;">
                        <i class="bi bi-shield-exclamation me-2"></i>IRERC Ethics Review — {{ $c->project->title ?? 'N/A' }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body py-2">
                    <div class="row g-2 mb-2">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small mb-1">Project</label>
                            <input type="text" class="form-control form-control-sm" value="{{ $c->project->title ?? 'N/A' }}" disabled>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small mb-1">Current Risk Level</label>
                            <input type="text" class="form-control form-control-sm" value="{{ $c->risk_level }}" disabled>
                        </div>
                    </div>

                    <div class="mb-2 p-2" style="background: #f1f5f9;">
                        <label class="form-label fw-bold mb-1 small"><i class="bi bi-clipboard2-check me-1"></i> Ethics Checklist</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="ethics_checklist[]" value="human_subjects" id="humanSubjects{{ $c->id }}">
                            <label class="form-check-label" for="humanSubjects{{ $c->id }}">Human subjects involved</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="ethics_checklist[]" value="animal_subjects" id="animalSubjects{{ $c->id }}">
                            <label class="form-check-label" for="animalSubjects{{ $c->id }}">Animal subjects involved</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="ethics_checklist[]" value="environmental_impact" id="envImpact{{ $c->id }}">
                            <label class="form-check-label" for="envImpact{{ $c->id }}">Environmental impact assessment</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="ethics_checklist[]" value="informed_consent" id="informedConsent{{ $c->id }}">
                            <label class="form-check-label" for="informedConsent{{ $c->id }}">Informed consent obtained</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="ethics_checklist[]" value="data_protection" id="dataProtection{{ $c->id }}">
                            <label class="form-check-label" for="dataProtection{{ $c->id }}">Data protection measures</label>
                        </div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label fw-bold small mb-1">IRERC Decision <span class="text-danger">*</span></label>
                        <select name="decision" class="form-select form-select-sm" required id="irercDecision{{ $c->id }}" onchange="toggleIrercSubmitBtn({{ $c->id }})">
                            <option value="">-- Select Decision --</option>
                            <option value="Approved">Approved</option>
                            <option value="Rejected">Rejected</option>
                        </select>
                    </div>

                    <div class="mb-2">
                        <label class="form-label fw-bold small mb-1">Updated Risk Level</label>
                        <select name="risk_level" class="form-select form-select-sm" required>
                            <option value="Low" {{ $c->risk_level === 'Low' ? 'selected' : '' }}>Low</option>
                            <option value="Medium" {{ $c->risk_level === 'Medium' ? 'selected' : '' }}>Medium</option>
                            <option value="High" {{ $c->risk_level === 'High' ? 'selected' : '' }}>High</option>
                            <option value="Critical" {{ $c->risk_level === 'Critical' ? 'selected' : '' }}>Critical</option>
                        </select>
                    </div>

                    <div class="mb-2">
                        <label class="form-label fw-bold small mb-1">IRERC Comments <span class="text-danger">*</span></label>
                        <textarea name="comments" class="form-control form-control-sm" rows="2" required placeholder="Enter ethics review comments..."></textarea>
                    </div>
                </div>
                <div class="modal-footer py-2">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-dark fw-bold btn-sm" id="irercSubmitBtn{{ $c->id }}" disabled>
                        <i class="bi bi-check-circle me-1"></i> Submit Decision
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

<script>
function toggleIrercSubmitBtn(id) {
    const decision = document.getElementById('irercDecision' + id).value;
    document.getElementById('irercSubmitBtn' + id).disabled = decision === '';
}
</script>
@endsection
