@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <a href="{{ route('papers.index') }}" class="text-decoration-none small text-muted mb-2 d-inline-block">
            <i class="bi bi-arrow-left me-1"></i> Back to Paper Registry
        </a>

        @php
            $canApprove = in_array(auth()->user()->role, ['vparttcs', 'rcsc', 'admin']) && $paper->status === 'UnderEvaluation';
            $canAward   = in_array(auth()->user()->role, ['vparttcs', 'rcsc', 'admin']) && $paper->status === 'Approved';
            $canRecord  = in_array(auth()->user()->role, ['coordinator', 'admin']) && $paper->status === 'Approved';
            $canReject  = in_array(auth()->user()->role, ['vparttcs', 'rcsc', 'admin', 'coordinator']) && in_array($paper->status, ['Submitted', 'UnderEvaluation']);
            $recipients = \App\Models\User::where('status', 'active')->orderBy('name')->get();
        @endphp

        <div class="card card-custom p-4 mb-4">
            <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                <div>
                    <h3 class="fw-bold mb-1">{{ $paper->title }}</h3>
                    <div class="text-muted small">
                        <i class="bi bi-person me-1"></i>{{ $paper->submitter->name ?? '—' }}
                        &nbsp;·&nbsp;<i class="bi bi-calendar3 me-1"></i>{{ $paper->created_at->format('d M Y') }}
                    </div>
                </div>
                <div class="text-end">
                    <span class="badge fs-6 {{ $paper->status === 'Approved' ? 'bg-success' : ($paper->status === 'Rejected' ? 'bg-danger' : ($paper->status === 'Recorded' ? 'bg-dark' : 'bg-warning text-dark')) }}">{{ $paper->status }}</span>
                    @if($paper->award_status === 'Awarded')
                        <div class="mt-1"><span class="badge bg-success"><i class="bi bi-award me-1"></i>Awarded</span></div>
                    @endif
                </div>
            </div>

            <hr>
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="text-muted small fw-bold text-uppercase">Authors</div>
                    <div>{{ $paper->authors }}</div>
                </div>
                <div class="col-md-3">
                    <div class="text-muted small fw-bold text-uppercase">Journal</div>
                    <div>{{ $paper->journal_name ?? '—' }}</div>
                </div>
                <div class="col-md-3">
                    <div class="text-muted small fw-bold text-uppercase">Journal Index</div>
                    <div><span class="badge bg-secondary">{{ $paper->journal_index ?? '—' }}</span></div>
                </div>
                <div class="col-md-6">
                    <div class="text-muted small fw-bold text-uppercase">Linked Project</div>
                    <div>{{ $paper->project->title ?? '— Not linked —' }}</div>
                </div>
                <div class="col-md-3">
                    <div class="text-muted small fw-bold text-uppercase">Publication Date</div>
                    <div>{{ $paper->publication_date ? $paper->publication_date->format('d M Y') : '—' }}</div>
                </div>
                <div class="col-md-3">
                    <div class="text-muted small fw-bold text-uppercase">Average Score</div>
                    <div>{{ $paper->average_score !== null ? $paper->average_score . ' / 100' : '—' }}</div>
                </div>
                <div class="col-12">
                    <div class="text-muted small fw-bold text-uppercase">Abstract</div>
                    <p class="mb-0">{{ $paper->abstract_text }}</p>
                </div>
                @if($paper->paper_file_path)
                <div class="col-12">
                    <a href="{{ asset('storage/' . $paper->paper_file_path) }}" target="_blank" class="btn btn-sm btn-outline-dark">
                        <i class="bi bi-file-earmark-pdf me-1"></i> View Attached Paper
                    </a>
                </div>
                @endif
            </div>

            @if($paper->evaluation_summary)
            <div class="alert alert-light border mt-3 mb-0">
                <div class="fw-bold small text-uppercase text-muted mb-1">Evaluation Summary</div>
                {{ $paper->evaluation_summary }}
            </div>
            @endif

            {{-- Officer actions --}}
            @if($canApprove || $canAward || $canRecord || $canReject)
            <div class="d-flex flex-wrap gap-2 mt-3 pt-3 border-top">
                @if($canApprove)
                <form method="POST" action="{{ route('papers.approve', $paper->id) }}">
                    @csrf
                    <button type="button" class="btn btn-success fw-bold confirm-btn" data-confirm-title="Approve Paper" data-confirm-message="Approve this paper for recording and awards?" data-confirm-icon="bi-check-circle" data-confirm-color="text-success" data-confirm-btn-text="Yes, Approve" data-confirm-btn-class="btn-success">
                        <i class="bi bi-check-circle me-1"></i> Approve Paper
                    </button>
                </form>
                @endif
                @if($canRecord)
                <form method="POST" action="{{ route('papers.record', $paper->id) }}">
                    @csrf
                    <button type="button" class="btn btn-dark fw-bold confirm-btn" data-confirm-title="Record Paper" data-confirm-message="Record this paper in the publication registry?" data-confirm-icon="bi-journal-check" data-confirm-color="text-dark" data-confirm-btn-text="Yes, Record" data-confirm-btn-class="btn-dark">
                        <i class="bi bi-journal-check me-1"></i> Record Paper
                    </button>
                </form>
                @endif
                @if($canAward)
                <button type="button" class="btn btn-outline-success fw-bold" data-bs-toggle="modal" data-bs-target="#awardModal">
                    <i class="bi bi-award me-1"></i> Record Award
                </button>
                @endif
                @if($canReject)
                <button type="button" class="btn btn-outline-danger fw-bold" data-bs-toggle="modal" data-bs-target="#rejectPaperModal">
                    <i class="bi bi-x-circle me-1"></i> Reject
                </button>
                @endif
            </div>
            @endif
        </div>

        {{-- Evaluations --}}
        <div class="card card-custom p-4 mb-4">
            <h5 class="fw-bold mb-3"><i class="bi bi-clipboard-data me-2 text-dark"></i>Evaluations</h5>
            <div class="table-responsive">
                <table class="table table-sm align-middle">
                    <thead class="table-light">
                        <tr><th>Evaluator</th><th>Decision</th><th>Score</th><th>Comments</th><th>Date</th></tr>
                    </thead>
                    <tbody>
                        @forelse($paper->evaluations as $eval)
                        <tr>
                            <td>{{ $eval->evaluator->name ?? '—' }}</td>
                            <td><span class="badge {{ $eval->decision === 'Reject' ? 'bg-danger' : ($eval->decision === 'RecommendAccept' ? 'bg-success' : 'bg-warning text-dark') }}">{{ str_replace('Recommend', '', $eval->decision) }}</span></td>
                            <td>{{ $eval->score }}</td>
                            <td class="small">{{ $eval->comments }}</td>
                            <td class="small">{{ $eval->created_at->format('d M Y') }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center text-muted py-3">No evaluations recorded yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($canEvaluate && !in_array($paper->status, ['Approved', 'Rejected', 'Recorded']))
            <hr>
            <h6 class="fw-bold">Your Evaluation</h6>
            @if($hasEvaluated)
                <div class="alert alert-success small mb-0"><i class="bi bi-check-circle me-1"></i>You have already submitted your evaluation. Submitting again will update it.</div>
            @endif
            <form method="POST" action="{{ route('papers.evaluate', $paper->id) }}" class="row g-2 mt-1">
                @csrf
                <div class="col-md-4">
                    <label class="form-label small fw-bold">Decision <span class="text-danger">*</span></label>
                    <select name="decision" class="form-select form-select-sm" required>
                        <option value="">-- Select --</option>
                        <option value="RecommendAccept">Recommend Accept</option>
                        <option value="RecommendAcceptWithRevisions">Accept with Revisions</option>
                        <option value="Reject">Reject</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold">Score (0-100) <span class="text-danger">*</span></label>
                    <input type="number" name="score" class="form-control form-control-sm" min="0" max="100" step="0.01" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-bold">Comments <span class="text-danger">*</span></label>
                    <textarea name="comments" class="form-control form-control-sm" rows="1" required></textarea>
                </div>
                <div class="col-12 text-end">
                    <button class="btn btn-warning btn-sm fw-bold"><i class="bi bi-send me-1"></i> Submit Evaluation</button>
                </div>
            </form>
            @endif
        </div>

        {{-- Awards history --}}
        @if($paper->awards->count())
        <div class="card card-custom p-4">
            <h5 class="fw-bold mb-3"><i class="bi bi-trophy me-2 text-success"></i>Awards</h5>
            @foreach($paper->awards as $award)
            <div class="border rounded p-3 mb-2">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="fw-bold">{{ $award->award_name }}</div>
                        <div class="small text-muted">Recipient: {{ $award->recipient->name ?? '—' }} · {{ $award->awarded_at->format('d M Y') }}</div>
                        @if($award->citation)<div class="small mt-1">{{ $award->citation }}</div>@endif
                    </div>
                    <div class="text-success fw-bold">ETB {{ number_format($award->award_amount, 0) }}</div>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>

{{-- Award Modal --}}
@if($canAward)
<div class="modal fade" id="awardModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('papers.award', $paper->id) }}">
                @csrf
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title fw-bold"><i class="bi bi-award me-2"></i>Record Award</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Recipient <span class="text-danger">*</span></label>
                        <select name="recipient_id" class="form-select" required>
                            <option value="">-- Select Staff --</option>
                            @foreach($recipients as $u)
                                <option value="{{ $u->id }}" {{ (int)$u->id === (int)$paper->submitter_id ? 'selected' : '' }}>{{ $u->name }} ({{ strtoupper($u->role) }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Award Name <span class="text-danger">*</span></label>
                        <input type="text" name="award_name" class="form-control" required placeholder="e.g. Institutional Best Research Publication 2026">
                    </div>
                    <div class="row g-2">
                        <div class="col-6">
                            <label class="form-label fw-bold">Amount (ETB) <span class="text-danger">*</span></label>
                            <input type="number" name="award_amount" class="form-control" min="0" step="0.01" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-bold">Award Date <span class="text-danger">*</span></label>
                            <input type="date" name="awarded_at" class="form-control" value="{{ now()->format('Y-m-d') }}" required>
                        </div>
                    </div>
                    <div class="mt-3">
                        <label class="form-label fw-bold">Citation / Reason</label>
                        <textarea name="citation" class="form-control" rows="2" placeholder="Reason for this award..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success fw-bold px-4">Record Award</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

{{-- Reject Modal --}}
@if($canReject)
<div class="modal fade" id="rejectPaperModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('papers.reject', $paper->id) }}">
                @csrf
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title fw-bold"><i class="bi bi-x-circle me-2"></i>Reject Paper</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <label class="form-label fw-bold">Reason for Rejection <span class="text-danger">*</span></label>
                    <textarea name="reason" class="form-control" rows="3" required placeholder="Explain why this paper is rejected..."></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger fw-bold px-4">Reject Paper</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection