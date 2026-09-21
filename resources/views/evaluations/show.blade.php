@extends('layouts.app')

@section('content')
<h3 class="fw-bold mb-4"><i class="bi bi-clipboard-check me-2 text-dark"></i>Blind Peer Review</h3>

@if($evaluation->is_blind_masked)
<div class="alert alert-secondary border-start border-4 border-dark mb-4">
    <i class="bi bi-eye-slash me-2 text-dark"></i>
    <strong>Double-Blind Review Active:</strong> PI identity is hidden to ensure impartial evaluation.
</div>
@endif

<div class="row">
    <div class="col-lg-6">
        <div class="card shadow-sm border-0 p-4 mb-4">
            <h5 class="fw-bold mb-3">Proposal Details</h5>
            <p><strong>Project ID:</strong> #{{ $evaluation->project_id }}</p>
            <p><strong>Title:</strong> {{ $evaluation->project->title ?? 'N/A' }}</p>
            <p><strong>Abstract:</strong></p>
            <div class="bg-light p-3 rounded">{{ $evaluation->project->abstract_text ?? 'N/A' }}</div>
            @if($evaluation->project->proposal_document_url)
            <p class="mt-3"><a href="{{ Storage::url($evaluation->project->proposal_document_url) }}" target="_blank" class="btn btn-outline-dark btn-sm"><i class="bi bi-file-pdf me-1"></i>View Proposal PDF</a></p>
            @endif
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card shadow-sm border-0 p-4">
            <h5 class="fw-bold mb-3">Evaluation Rubric</h5>

            @if($evaluation->decision !== 'Pending')
            <div class="p-3 bg-light rounded border mb-3">
                <div class="d-flex justify-content-between mb-2">
                    <span class="small text-muted">Score:</span>
                    <span class="fw-bold fs-5">{{ $evaluation->score }} / 100</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="small text-muted">Decision:</span>
                    <span class="badge bg-secondary">{{ $evaluation->decision }}</span>
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
            <form action="{{ route('evaluations.submit', $evaluation->eval_id) }}" method="POST" id="rubricEvaluationFormStandAlone">
                @csrf

                {{-- 5-Dimension Rubric Inputs --}}
                <div class="p-3 bg-light rounded-3 border mb-3">
                    <div class="row g-2">
                        <div class="col-12">
                            <label class="form-label small fw-bold mb-1 d-flex justify-content-between">
                                <span>1. Methodology &amp; Design</span>
                                <span class="text-muted">(Max 25)</span>
                            </label>
                            <input type="number" step="0.5" min="0" max="25" name="rubric_methodology" id="rubric_m_sa"
                                   class="form-control form-control-sm" required placeholder="0 - 25" oninput="calculateTotalScoreSA()">
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold mb-1 d-flex justify-content-between">
                                <span>2. Literature Review</span>
                                <span class="text-muted">(Max 20)</span>
                            </label>
                            <input type="number" step="0.5" min="0" max="20" name="rubric_literature" id="rubric_l_sa"
                                   class="form-control form-control-sm" required placeholder="0 - 20" oninput="calculateTotalScoreSA()">
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold mb-1 d-flex justify-content-between">
                                <span>3. Feasibility</span>
                                <span class="text-muted">(Max 20)</span>
                            </label>
                            <input type="number" step="0.5" min="0" max="20" name="rubric_feasibility" id="rubric_f_sa"
                                   class="form-control form-control-sm" required placeholder="0 - 20" oninput="calculateTotalScoreSA()">
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold mb-1 d-flex justify-content-between">
                                <span>4. Regional Relevance</span>
                                <span class="text-muted">(Max 20)</span>
                            </label>
                            <input type="number" step="0.5" min="0" max="20" name="rubric_relevance" id="rubric_r_sa"
                                   class="form-control form-control-sm" required placeholder="0 - 20" oninput="calculateTotalScoreSA()">
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold mb-1 d-flex justify-content-between">
                                <span>5. Budget Justification</span>
                                <span class="text-muted">(Max 15)</span>
                            </label>
                            <input type="number" step="0.5" min="0" max="15" name="rubric_budget" id="rubric_b_sa"
                                   class="form-control form-control-sm" required placeholder="0 - 15" oninput="calculateTotalScoreSA()">
                        </div>
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold mb-1">Total Score (0 - 100) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" min="0" max="100" name="score" id="totalScoreInputSA"
                               class="form-control fw-bold bg-white text-dark" required placeholder="0.00" readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold mb-1">Verdict <span class="text-danger">*</span></label>
                        <select name="decision" class="form-select fw-bold" required>
                            <option value="Accepted">Accepted (Fund)</option>
                            <option value="AcceptedWithMinorMods">Accepted (Minor Mods)</option>
                            <option value="AcceptedWithMajorMods">Accepted (Major Mods)</option>
                            <option value="Rejected">Rejected</option>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold">Constructive Comments &amp; Critique <span class="text-danger">*</span></label>
                    <textarea name="comments" rows="4" class="form-control" required placeholder="Provide technical feedback, strengths, and areas of improvement..."></textarea>
                </div>

                <button type="button" class="btn btn-dark w-100 fw-bold confirm-btn" data-confirm-title="Submit Evaluation" data-confirm-message="This action cannot be undone. Your score and recommendation will be final." data-confirm-icon="bi-send-check" data-confirm-color="text-dark" data-confirm-btn-text="Yes, Submit" data-confirm-btn-class="btn-dark">
                    <i class="bi bi-send-check me-1"></i> Submit Evaluation Score
                </button>
            </form>

            <script>
            function calculateTotalScoreSA() {
                const m = parseFloat(document.getElementById('rubric_m_sa').value) || 0;
                const l = parseFloat(document.getElementById('rubric_l_sa').value) || 0;
                const f = parseFloat(document.getElementById('rubric_f_sa').value) || 0;
                const r = parseFloat(document.getElementById('rubric_r_sa').value) || 0;
                const b = parseFloat(document.getElementById('rubric_b_sa').value) || 0;
                const total = Math.min(100, Math.max(0, m + l + f + r + b));
                document.getElementById('totalScoreInputSA').value = total.toFixed(2);
            }
            </script>
            @endif
        </div>
    </div>
</div>

<div class="mt-3">
    <a href="{{ route('evaluations.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Back to Assignments</a>
</div>
@endsection
