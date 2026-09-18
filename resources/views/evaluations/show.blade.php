@extends('layouts.app')

@section('content')
<h3 class="fw-bold mb-4"><i class="bi bi-clipboard-check me-2 text-primary"></i>Blind Peer Review</h3>

@if($evaluation->is_blind_masked)
<div class="alert alert-info mb-4">
    <i class="bi bi-eye-slash me-2"></i>
    <strong>Double-Blind Review Active:</strong> PI identity is hidden to ensure impartial evaluation.
</div>
@endif

<div class="row">
    <div class="col-lg-6">
        <div class="card card-custom p-4 mb-4">
            <h5 class="fw-bold mb-3">Proposal Details</h5>
            <p><strong>Project ID:</strong> #{{ $evaluation->project_id }}</p>
            <p><strong>Title:</strong> {{ $evaluation->project->title ?? 'N/A' }}</p>
            <p><strong>Abstract:</strong></p>
            <div class="bg-light p-3 rounded">{{ $evaluation->project->abstract_text ?? 'N/A' }}</div>
            @if($evaluation->project->proposal_document_url)
            <p class="mt-3"><a href="{{ Storage::url($evaluation->project->proposal_document_url) }}" target="_blank" class="btn btn-outline-primary btn-sm"><i class="bi bi-file-pdf me-1"></i>View Proposal PDF</a></p>
            @endif
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card card-custom p-4">
            <h5 class="fw-bold mb-3">Evaluation Rubric</h5>

            @if($evaluation->decision !== 'Pending')
            <div class="p-3 bg-light rounded border mb-3">
                <div class="d-flex justify-content-between mb-2">
                    <span class="small text-muted">Score:</span>
                    <span class="fw-bold fs-5">{{ $evaluation->score }} / 100</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="small text-muted">Decision:</span>
                    <span class="badge bg-info text-dark">{{ $evaluation->decision }}</span>
                </div>
                <div class="mb-2">
                    <span class="small text-muted d-block mb-1">Comments:</span>
                    <p class="small mb-0">{{ $evaluation->comments }}</p>
                </div>
                <div class="d-flex justify-content-between">
                    <span class="small text-muted">Evaluated:</span>
                    <span class="small">{{ $evaluation->evaluated_at ? $evaluation->evaluated_at->format('M d, Y H:i') : 'N/A' }}</span>
                </div>
            </div>
            @else
            <form action="{{ route('evaluations.submit', $evaluation->eval_id) }}" method="POST">
                @csrf
                <div class="card mb-3 border-0 shadow-sm">
                    <div class="card-body">
                        <h6 class="fw-bold mb-3"><i class="bi bi-speedometer me-2 text-primary"></i>Score</h6>
                        <div class="d-flex align-items-center mb-2">
                            <input type="range" name="score" id="scoreSlider" class="form-range me-3" min="0" max="100" step="1" value="{{ old('score', '50') }}" oninput="updateScorePreview(this.value)">
                            <span id="scorePreview" class="badge bg-primary fs-6" style="min-width: 60px;">50</span>
                        </div>
                        <div class="d-flex justify-content-between small text-muted mb-2">
                            <span>0</span>
                            <span>25</span>
                            <span>50</span>
                            <span>75</span>
                            <span>100</span>
                        </div>
                        <div class="mt-2 p-2 bg-light rounded small">
                            <strong>Score Guide:</strong>
                            <span class="ms-2" id="scoreGuide">Average</span>
                        </div>
                    </div>
                </div>

                <div class="card mb-3 border-0 shadow-sm">
                    <div class="card-body">
                        <h6 class="fw-bold mb-3"><i class="bi bi-journal-check me-2 text-primary"></i>Decision</h6>
                        <select name="decision" id="decisionSelect" class="form-select" required onchange="updateDecisionExplanation()">
                            <option value="Accepted">Accepted</option>
                            <option value="AcceptedWithMinorMods">Accepted with Minor Modifications</option>
                            <option value="AcceptedWithMajorMods">Accepted with Major Modifications</option>
                            <option value="Rejected">Rejected</option>
                        </select>
                        <div id="decisionExplanation" class="mt-2 p-2 bg-light rounded small text-muted">
                            Strong proposal, recommended for full funding.
                        </div>
                    </div>
                </div>

                <div class="card mb-3 border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="fw-bold mb-0"><i class="bi bi-chat-left-text me-2 text-primary"></i>Technical Comments</h6>
                            <small class="text-muted"><span id="wordCount">0</span> words</small>
                        </div>
                        <textarea name="comments" id="commentsField" class="form-control" rows="5" required placeholder="Provide detailed feedback on methodology, feasibility, impact..." oninput="updateWordCount()">{{ old('comments') }}</textarea>
                    </div>
                </div>

                <button type="button" class="btn btn-success w-100 py-2 confirm-btn" data-confirm-title="Submit Evaluation" data-confirm-message="Submit evaluation? This cannot be undone." data-confirm-icon="bi-check-circle" data-confirm-color="text-success" data-confirm-btn-text="Yes, Submit" data-confirm-btn-class="btn-success">
                    <i class="bi bi-check-circle me-2"></i>Submit Evaluation
                </button>
            </form>
            @endif
        </div>
    </div>
</div>

<div class="mt-3">
    <a href="{{ route('evaluations.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Back to Assignments</a>
</div>

<script>
function updateScorePreview(value) {
    document.getElementById('scorePreview').textContent = value;
    let guide = '';
    if (value >= 90) guide = 'Excellent';
    else if (value >= 70) guide = 'Good';
    else if (value >= 50) guide = 'Average';
    else guide = 'Below expectations';
    document.getElementById('scoreGuide').textContent = guide;
}

function updateDecisionExplanation() {
    const select = document.getElementById('decisionSelect');
    const explanation = document.getElementById('decisionExplanation');
    const explanations = {
        'Accepted': 'Strong proposal, recommended for full funding.',
        'AcceptedWithMinorMods': 'Good proposal, minor changes needed.',
        'AcceptedWithMajorMods': 'Potential proposal, significant revisions required.',
        'Rejected': 'Does not meet institutional standards.'
    };
    explanation.textContent = explanations[select.value] || '';
}

function updateWordCount() {
    const text = document.getElementById('commentsField').value.trim();
    const count = text ? text.split(/\s+/).length : 0;
    document.getElementById('wordCount').textContent = count;
}

document.addEventListener('DOMContentLoaded', function() {
    updateScorePreview(document.getElementById('scoreSlider').value);
    updateDecisionExplanation();
    updateWordCount();
});
</script>
@endsection
