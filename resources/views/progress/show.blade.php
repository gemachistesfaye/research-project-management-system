@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-0"><i class="bi bi-graph-up me-2 text-dark"></i> {{ Str::limit($project->title, 50) }}</h3>
        <span class="text-muted">Progress Reports & Milestone Tracking</span>
        @if($project->proposal_document_url)
        <a href="{{ Storage::url($project->proposal_document_url) }}" target="_blank" class="btn btn-sm btn-outline-dark ms-2" title="View Proposal Document">
            <i class="bi bi-file-pdf me-1"></i>Proposal
        </a>
        @endif
    </div>
    <a href="{{ route('progress.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Back
    </a>
</div>

<div class="row g-4">
    {{-- Overall Project Progress --}}
    <div class="col-12">
        @php
            $overallProgress = $reports->count() > 0 ? (int) $reports->max('progress_percentage') : 0;
            $approvedCount = $reports->where('status', 'Approved')->count();
            $pendingCount = $reports->where('status', 'Submitted')->count();
            $revisionCount = $reports->where('status', 'Needs_Revision')->count();
        @endphp
        <div class="card card-custom">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-3 text-center border-end">
                        <div class="position-relative d-inline-block">
                            <svg width="100" height="100" viewBox="0 0 100 100">
                                <circle cx="50" cy="50" r="45" fill="none" stroke="#e9ecef" stroke-width="8"/>
                                <circle cx="50" cy="50" r="45" fill="none" 
                                        stroke="#212529" 
                                        stroke-width="8" stroke-linecap="round"
                                        stroke-dasharray="{{ $overallProgress * 2.83 }} 283"
                                        transform="rotate(-90 50 50)"/>
                                <text x="50" y="50" text-anchor="middle" dy=".3em" 
                                      class="fs-4 fw-bold" fill="#212529">
                                    {{ $overallProgress }}%
                                </text>
                            </svg>
                        </div>
                        <div class="mt-2 small fw-semibold text-muted">Overall Progress</div>
                    </div>
                    <div class="col-md-9">
                        <div class="row text-center g-3">
                            <div class="col">
                                <div class="border rounded p-3 bg-light">
                                    <div class="fs-3 fw-bold text-dark">{{ $reports->count() }}</div>
                                    <div class="small text-muted">Total Reports</div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="border rounded p-3 bg-light">
                                    <div class="fs-3 fw-bold text-dark">{{ $approvedCount }}</div>
                                    <div class="small text-muted">Approved</div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="border rounded p-3 bg-light">
                                    <div class="fs-3 fw-bold text-dark">{{ $pendingCount }}</div>
                                    <div class="small text-muted">Pending</div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="border rounded p-3 bg-light">
                                    <div class="fs-3 fw-bold text-dark">{{ $revisionCount }}</div>
                                    <div class="small text-muted">Needs Revision</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Financial Disbursement & Tranche Status (Visible to PI, TM, Coordinator, DH) --}}
    @php
        $ratifiedTotal = $project->approved_budget ?: $project->requested_budget;
        $totalReleased = $project->budgetRequests->where('status', 'Released')->sum('approved_amount');
        $tranches = $project->budgetRequests->filter(fn($r) => str_starts_with($r->milestone_phase, 'Tranche'));
    @endphp
    <div class="col-12">
        <div class="card card-custom border-start border-success border-4">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-cash-stack text-success fs-4 me-2"></i>
                        <div>
                            <span class="fw-bold text-dark">Finance Disbursement &amp; Tranche Status</span>
                            <small class="text-muted d-block" style="font-size:0.75rem;">Ratified Budget: {{ number_format($ratifiedTotal, 2) }} ETB &bull; Total Disbursed: <strong class="text-success">{{ number_format($totalReleased, 2) }} ETB</strong></small>
                        </div>
                    </div>
                    <div class="d-flex gap-2 align-items-center flex-wrap">
                        @foreach(['Tranche 1' => '30% Advance', 'Tranche 2' => '40% Mid-Term', 'Tranche 3' => '30% Final'] as $trancheName => $trancheDesc)
                            @php
                                $foundReq = $tranches->firstWhere('milestone_phase', $trancheName);
                            @endphp
                            <div class="border rounded px-2 py-1 bg-light d-flex align-items-center gap-1" style="font-size: 0.78rem;">
                                <strong>{{ $trancheName }}:</strong>
                                @if($foundReq && $foundReq->status === 'Released')
                                    <span class="badge bg-success text-white"><i class="bi bi-check-all me-1"></i>Disbursed ({{ number_format($foundReq->approved_amount, 0) }} ETB)</span>
                                @elseif($foundReq && $foundReq->status === 'Approved')
                                    <span class="badge bg-warning text-dark"><i class="bi bi-clock me-1"></i>Queued in Finance ({{ number_format($foundReq->approved_amount, 0) }} ETB)</span>
                                @else
                                    <span class="badge bg-secondary text-white">Pending Milestone</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(in_array(Auth::user()->role, ['pi', 'tm']))
    {{-- Submit New Report (PI & TM Only - Left Side) --}}
    <div class="col-lg-5">
        <div class="card card-custom h-100 shadow-sm">
            <div class="card-header bg-dark text-white fw-bold py-3">
                <i class="bi bi-plus-circle me-2"></i> Submit Progress Report
            </div>
            <div class="card-body">
                <form action="{{ route('progress.store', $project->project_id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted text-uppercase">Milestone Name *</label>
                        <input type="text" name="milestone_name" class="form-control @error('milestone_name') is-invalid @enderror"
                               placeholder="e.g. Phase 1: Literature Review" required>
                        @error('milestone_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted text-uppercase">Progress Percentage *</label>
                        <div class="input-group">
                            <input type="range" name="progress_percentage" id="progressSlider" 
                                   class="form-range" min="0" max="100" value="0" required
                                   oninput="document.getElementById('progressValue').textContent = this.value + '%'; updateSliderColor(this);">
                            <span class="input-group-text bg-light" style="min-width: 60px; justify-content: center;">
                                <span id="progressValue" class="fw-bold">0%</span>
                            </span>
                        </div>
                        <div class="progress mt-2" style="height: 8px;">
                            <div id="progressBarPreview" class="progress-bar bg-dark" style="width: 0%"></div>
                        </div>
                        <div class="form-text small">Drag the slider to indicate milestone completion (0-100%)</div>
                        @error('progress_percentage') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted text-uppercase">Progress Summary *</label>
                        <textarea name="summary_text" class="form-control @error('summary_text') is-invalid @enderror"
                                  rows="4" placeholder="Describe work completed, challenges, and next steps..." required></textarea>
                        @error('summary_text') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted text-uppercase">
                            <i class="bi bi-file-earmark-arrow-up me-1 text-dark"></i>Deliverable Document File (Optional)
                        </label>
                        <input type="file" name="deliverable_file" class="form-control form-control-sm @error('deliverable_file') is-invalid @enderror" accept=".pdf,.doc,.docx,.zip,.rar,.xlsx,.csv,.txt">
                        <div class="form-text small text-muted">Upload PDF, Word document, datasets, or ZIP archive (max 20MB)</div>
                        @error('deliverable_file') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted text-uppercase">
                            <i class="bi bi-link-45deg me-1 text-dark"></i>Deliverable / Repository URL Link (Optional)
                        </label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text"><i class="bi bi-link"></i></span>
                            <input type="url" name="deliverable_link_url" class="form-control @error('deliverable_link_url') is-invalid @enderror"
                                   placeholder="https://github.com/... or https://drive.google.com/..." value="{{ old('deliverable_link_url') }}">
                        </div>
                        <div class="form-text small text-muted">Link to external repository, cloud drive, or published paper</div>
                        @error('deliverable_link_url') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>

                    <button type="submit" class="btn btn-dark fw-bold w-100 confirm-btn py-2" data-confirm-title="Submit Report" data-confirm-message="Submit this progress report?" data-confirm-icon="bi-send" data-confirm-color="text-dark" data-confirm-btn-text="Yes, Submit" data-confirm-btn-class="btn-dark">
                        <i class="bi bi-send me-1"></i> Submit Progress Report
                    </button>
                </form>
            </div>
        </div>
    </div>
    @endif

    {{-- Report History & Timeline (Right Side for PI/TM, Full/Split for Coordinator/Admin) --}}
    <div class="{{ in_array(Auth::user()->role, ['pi', 'tm']) ? 'col-lg-7' : 'col-12' }}">
        <div class="row g-4">
            {{-- Milestone Timeline --}}
            @if($reports->count() > 0)
            <div class="{{ in_array(Auth::user()->role, ['pi', 'tm']) ? 'col-12' : 'col-lg-5' }}">
                <div class="card card-custom h-100 shadow-sm">
                    <div class="card-header bg-white border-bottom fw-bold py-3 d-flex justify-content-between align-items-center">
                        <div><i class="bi bi-signpost-split me-2 text-dark"></i> Milestone Timeline</div>
                        <span class="badge bg-light text-dark border">{{ $reports->count() }} Milestones</span>
                    </div>
                    <div class="card-body">
                        <div class="timeline">
                            @foreach($reports->sortBy('created_at') as $index => $report)
                            <div class="timeline-item mb-3 {{ $loop->last ? '' : 'pb-3 border-bottom' }}">
                                <div class="d-flex align-items-start">
                                    <div class="me-3">
                                        <div class="timeline-marker bg-dark" 
                                             style="width: 12px; height: 12px; border-radius: 50%; margin-top: 4px;">
                                        </div>
                                        @if(!$loop->last)
                                        <div class="timeline-line border-start border-2 border-dashed ms-1" style="height: calc(100% + 12px); margin-top: 4px;"></div>
                                        @endif
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <h6 class="fw-bold mb-0" style="font-size: 0.95rem;">{{ $report->milestone_name }}</h6>
                                                <small class="text-muted">
                                                    <i class="bi bi-calendar me-1"></i>{{ $report->created_at ? $report->created_at->diffForHumans() : 'N/A' }}
                                                </small>
                                            </div>
                                            @if($report->status === 'Approved')
                                                <span class="badge bg-success text-white"><i class="bi bi-check-circle me-1"></i>Approved</span>
                                            @elseif($report->status === 'Needs_Revision')
                                                <span class="badge bg-warning text-dark"><i class="bi bi-exclamation-triangle me-1"></i>Revision</span>
                                            @else
                                                <span class="badge bg-secondary text-white">{{ str_replace('_', ' ', $report->status) }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            @endif

            {{-- Detailed Report History --}}
            <div class="{{ in_array(Auth::user()->role, ['pi', 'tm']) ? 'col-12' : ($reports->count() > 0 ? 'col-lg-7' : 'col-12') }}">
                <div class="card card-custom h-100 shadow-sm">
                    <div class="card-header bg-white border-bottom fw-bold py-3 d-flex justify-content-between align-items-center">
                        <div>
                            <i class="bi bi-clock-history me-2 text-dark"></i> Detailed Report History
                        </div>
                        <span class="badge bg-dark rounded-pill">{{ $reports->count() }}</span>
                    </div>
                    <div class="card-body p-0">
                        @if($reports->isEmpty())
                        <div class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                            No reports submitted yet. Use the form to submit your first progress report.
                        </div>
                        @else
                        <div class="accordion accordion-flush" id="reportHistoryAccordion">
                            @foreach($reports as $report)
                            <div class="accordion-item border-bottom {{ $loop->last ? 'border-0' : '' }}">
                                <h2 class="accordion-header" id="headingReport_{{ $report->id }}">
                                    <button class="accordion-button collapsed py-3 px-3" type="button" data-bs-toggle="collapse" data-bs-target="#collapseReport_{{ $report->id }}" aria-expanded="false" aria-controls="collapseReport_{{ $report->id }}">
                                        <div class="d-flex justify-content-between align-items-center w-100 me-3 flex-wrap gap-2">
                                            <div>
                                                <span class="fw-bold text-dark me-2">{{ $report->milestone_name }}</span>
                                                <small class="text-muted">
                                                    <i class="bi bi-calendar me-1"></i>{{ $report->created_at ? $report->created_at->diffForHumans() : 'N/A' }}
                                                </small>
                                            </div>
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="badge bg-dark rounded-pill">{{ $report->progress_percentage }}%</span>
                                                @if($report->status === 'Approved')
                                                    <span class="badge bg-success text-white"><i class="bi bi-check-circle me-1"></i>Approved</span>
                                                @elseif($report->status === 'Needs_Revision')
                                                    <span class="badge bg-warning text-dark"><i class="bi bi-exclamation-triangle me-1"></i>Revision</span>
                                                @else
                                                    <span class="badge bg-secondary text-white">{{ str_replace('_', ' ', $report->status) }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </button>
                                </h2>
                                <div id="collapseReport_{{ $report->id }}" class="accordion-collapse collapse" aria-labelledby="headingReport_{{ $report->id }}" data-bs-parent="#reportHistoryAccordion">
                                    <div class="accordion-body bg-light bg-opacity-25 p-3">
                                        <div class="p-3 mb-3 bg-white border rounded">
                                            <span class="small fw-bold text-muted text-uppercase d-block mb-1">Progress Summary</span>
                                            <p class="small mb-0 text-secondary">{{ $report->summary_text }}</p>
                                        </div>

                                        <div class="card bg-white border mb-3">
                                            <div class="card-body py-2 px-3">
                                                <div class="d-flex justify-content-between align-items-center mb-1">
                                                    <span class="small fw-semibold text-muted">Progress Completion</span>
                                                    <span class="badge bg-dark rounded-pill">
                                                        {{ $report->progress_percentage }}%
                                                    </span>
                                                </div>
                                                <div class="progress" style="height: 8px; border-radius: 4px;">
                                                    <div class="progress-bar bg-dark" 
                                                         style="width: {{ $report->progress_percentage }}%; border-radius: 4px;"
                                                         role="progressbar" 
                                                         aria-valuenow="{{ $report->progress_percentage }}" 
                                                         aria-valuemin="0" 
                                                         aria-valuemax="100">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="d-flex flex-wrap gap-2 mb-3">
                                            @if($report->deliverable_file_path)
                                                <a href="{{ Storage::url($report->deliverable_file_path) }}" class="btn btn-dark btn-sm" target="_blank">
                                                    <i class="bi bi-file-earmark-arrow-down me-1"></i>Download Attached File
                                                </a>
                                            @endif

                                            @if($report->deliverable_link_url)
                                                <a href="{{ $report->deliverable_link_url }}" class="btn btn-outline-dark btn-sm" target="_blank">
                                                    <i class="bi bi-box-arrow-up-right me-1"></i>Open External Link
                                                </a>
                                            @elseif($report->deliverable_document_url && !$report->deliverable_file_path)
                                                <a href="{{ $report->deliverable_document_url }}" class="btn btn-outline-dark btn-sm" target="_blank">
                                                    <i class="bi bi-link-45deg me-1"></i>View Deliverable
                                                </a>
                                            @endif
                                        </div>

                                        @if($report->coordinator_feedback)
                                            <div class="alert alert-light border mb-3 py-2 px-3">
                                                <strong class="small text-muted"><i class="bi bi-chat-dots me-1"></i>Coordinator Feedback:</strong>
                                                <p class="small mb-0 mt-1">{{ $report->coordinator_feedback }}</p>
                                            </div>
                                        @endif

                                        {{-- Coordinator Review Form (strictly coordinator and admin) --}}
                                        @if(in_array(Auth::user()->role, ['coordinator', 'admin']))
                                        <div class="card border-dark border-opacity-25 mt-2">
                                            <div class="card-header bg-dark bg-opacity-10 py-2">
                                                <small class="fw-bold text-dark"><i class="bi bi-pencil-square me-1"></i>Coordinator Milestone Audit &amp; Review</small>
                                            </div>
                                            <div class="card-body py-3">
                                                <form action="{{ route('progress.update', $report->id) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="mb-3">
                                                        <label class="form-label small fw-bold">Coordinator Feedback</label>
                                                        <textarea name="coordinator_feedback" class="form-control form-control-sm" rows="2"
                                                                  placeholder="Add coordinator feedback...">{{ $report->coordinator_feedback }}</textarea>
                                                    </div>
                                                    <div class="d-flex gap-2 align-items-center">
                                                        <select name="status" class="form-select form-select-sm" style="width: auto;">
                                                            <option value="Coordinator_Audited" {{ $report->status === 'Coordinator_Audited' ? 'selected' : '' }}>Audited</option>
                                                            <option value="Approved" {{ $report->status === 'Approved' ? 'selected' : '' }}>Approved</option>
                                                            <option value="Needs_Revision" {{ $report->status === 'Needs_Revision' ? 'selected' : '' }}>Needs Revision</option>
                                                        </select>
                                                        <button type="button" class="btn btn-sm btn-dark confirm-btn" data-confirm-title="Update Report" data-confirm-message="Update this progress report status?" data-confirm-icon="bi-check-circle" data-confirm-color="text-dark" data-confirm-btn-text="Yes, Update" data-confirm-btn-class="btn-dark">
                                                            <i class="bi bi-check-lg me-1"></i> Update &amp; Ratify
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                        @elseif(Auth::user()->role === 'dh')
                                        <div class="mt-2 p-2 bg-white rounded border text-muted small">
                                            <i class="bi bi-eye me-1"></i><strong>Department Head Read-Only:</strong> Milestone review, audit and status updates are managed by the Research Coordinator.
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .timeline-marker {
        box-shadow: 0 0 0 3px rgba(33, 37, 41, 0.2);
    }
    .timeline-line {
        margin-left: 5px;
    }
    .form-range::-webkit-slider-thumb {
        width: 20px;
        height: 20px;
    }
    .form-range::-webkit-slider-thumb:hover {
        transform: scale(1.2);
    }
</style>
@endsection

@section('scripts')
<script>
    function updateSliderColor(slider) {
        const value = slider.value;
        const preview = document.getElementById('progressBarPreview');
        preview.style.width = value + '%';
    }
</script>
@endsection
