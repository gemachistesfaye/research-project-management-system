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
            $overallProgress = $reports->count() > 0 ? round($reports->avg('progress_percentage')) : 0;
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

    {{-- Submit New Report --}}
    <div class="col-lg-5">
        <div class="card card-custom">
            <div class="card-header bg-dark text-white fw-bold">
                <i class="bi bi-plus-circle me-2"></i> Submit Progress Report
            </div>
            <div class="card-body">
                <form action="{{ route('progress.store', $project->project_id) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-bold">Milestone Name</label>
                        <input type="text" name="milestone_name" class="form-control @error('milestone_name') is-invalid @enderror"
                               placeholder="e.g. Phase 1: Literature Review" required>
                        @error('milestone_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Progress Percentage</label>
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
                        <div class="form-text">Drag the slider to indicate milestone completion (0-100%)</div>
                        @error('progress_percentage') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Progress Summary</label>
                        <textarea name="summary_text" class="form-control @error('summary_text') is-invalid @enderror"
                                  rows="4" placeholder="Describe work completed, challenges, and next steps..." required></textarea>
                        @error('summary_text') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Deliverable Document URL (Optional)</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-link-45deg"></i></span>
                            <input type="url" name="deliverable_document_url" class="form-control"
                                   placeholder="https://...">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-dark fw-bold w-100">
                        <i class="bi bi-send me-1"></i> Submit Report
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Report History & Timeline --}}
    <div class="col-lg-7">
        {{-- Milestone Timeline --}}
        @if($reports->count() > 0)
        <div class="card card-custom mb-4">
            <div class="card-header bg-white border-bottom fw-bold">
                <i class="bi bi-signpost-split me-2 text-dark"></i> Milestone Timeline
            </div>
            <div class="card-body">
                <div class="timeline">
                    @foreach($reports->sortBy('created_at') as $index => $report)
                    <div class="timeline-item mb-4 {{ $loop->last ? '' : 'pb-4 border-bottom' }}">
                        <div class="d-flex align-items-start">
                            <div class="me-3">
                                <div class="timeline-marker bg-dark" 
                                     style="width: 12px; height: 12px; border-radius: 50%; margin-top: 4px;">
                                </div>
                                @if(!$loop->last)
                                <div class="timeline-line border-start border-2 border-dashed ms-1" style="height: calc(100% + 16px); margin-top: 4px;"></div>
                                @endif
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="fw-bold mb-1">{{ $report->milestone_name }}</h6>
                                        <small class="text-muted">
                                            <i class="bi bi-calendar me-1"></i>{{ $report->created_at ? $report->created_at->diffForHumans() : 'N/A' }}
                                        </small>
                                    </div>
                                    <span class="badge bg-dark">{{ str_replace('_', ' ', $report->status) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        {{-- Detailed Report History --}}
        <div class="card card-custom">
            <div class="card-header bg-white border-bottom fw-bold">
                <i class="bi bi-clock-history me-2 text-dark"></i> Detailed Report History
                <span class="badge bg-dark rounded-pill ms-2">{{ $reports->count() }}</span>
            </div>
            <div class="card-body p-0">
                @forelse($reports as $report)
                <div class="border-bottom p-3 {{ $loop->last ? 'border-0' : '' }}">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <h6 class="fw-bold mb-1">{{ $report->milestone_name }}</h6>
                            <small class="text-muted">
                                <i class="bi bi-calendar me-1"></i>{{ $report->created_at ? $report->created_at->diffForHumans() : 'N/A' }}
                            </small>
                        </div>
                        <span class="badge bg-dark">{{ str_replace('_', ' ', $report->status) }}</span>
                    </div>

                    <p class="small mb-3">{{ $report->summary_text }}</p>

                    <div class="card bg-light border-0 mb-3">
                        <div class="card-body py-2 px-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="small fw-semibold text-muted">Progress</span>
                                <span class="badge bg-dark rounded-pill">
                                    {{ $report->progress_percentage }}%
                                </span>
                            </div>
                            <div class="progress" style="height: 10px; border-radius: 5px;">
                                <div class="progress-bar bg-dark" 
                                     style="width: {{ $report->progress_percentage }}%; border-radius: 5px;"
                                     role="progressbar" 
                                     aria-valuenow="{{ $report->progress_percentage }}" 
                                     aria-valuemin="0" 
                                     aria-valuemax="100">
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($report->deliverable_document_url)
                        <a href="{{ $report->deliverable_document_url }}" class="btn btn-outline-dark btn-sm mb-2" target="_blank">
                            <i class="bi bi-link-45deg me-1"></i>View Deliverable
                        </a>
                    @endif

                    @if($report->coordinator_feedback)
                        <div class="alert alert-light border mt-2 mb-0 py-2 px-3">
                            <strong class="small text-muted"><i class="bi bi-chat-dots me-1"></i>Coordinator Feedback:</strong>
                            <p class="small mb-0 mt-1">{{ $report->coordinator_feedback }}</p>
                        </div>
                    @endif

                    {{-- Coordinator Review Form (visible to coordinator/dh) --}}
                    @if(in_array(Auth::user()->role, ['coordinator', 'dh']))
                    <div class="card border-dark border-opacity-25 mt-3">
                        <div class="card-header bg-dark bg-opacity-10 py-2">
                            <small class="fw-bold text-dark"><i class="bi bi-pencil-square me-1"></i>Coordinator Review</small>
                        </div>
                        <div class="card-body py-3">
                            <form action="{{ route('progress.update', $report->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="mb-3">
                                    <label class="form-label small fw-bold">Coordinator Feedback</label>
                                    <textarea name="coordinator_feedback" class="form-control form-control-sm" rows="2"
                                              placeholder="Add feedback...">{{ $report->coordinator_feedback }}</textarea>
                                </div>
                                <div class="d-flex gap-2 align-items-center">
                                    <select name="status" class="form-select form-select-sm" style="width: auto;">
                                        <option value="Coordinator_Audited" {{ $report->status === 'Coordinator_Audited' ? 'selected' : '' }}>Audited</option>
                                        <option value="Approved" {{ $report->status === 'Approved' ? 'selected' : '' }}>Approved</option>
                                        <option value="Needs_Revision" {{ $report->status === 'Needs_Revision' ? 'selected' : '' }}>Needs Revision</option>
                                    </select>
                                    <button type="button" class="btn btn-sm btn-dark confirm-btn" data-confirm-title="Update Report" data-confirm-message="Update this progress report status?" data-confirm-icon="bi-check-circle" data-confirm-color="text-dark" data-confirm-btn-text="Yes, Update" data-confirm-btn-class="btn-dark">
                                        <i class="bi bi-check-lg me-1"></i> Update
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                    @endif
                </div>
                @empty
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                    No reports submitted yet. Use the form to submit your first progress report.
                </div>
                @endforelse
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
