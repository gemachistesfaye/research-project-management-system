@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-0"><i class="bi bi-graph-up me-2 text-dark"></i> Progress Reports</h3>
        <span class="text-muted">Submit and track milestone progress for your projects</span>
    </div>
</div>

@if($projects->isEmpty())
    <div class="alert alert-secondary">
        <i class="bi bi-info-circle me-2"></i>No active projects found. Submit a proposal first.
    </div>
@else
    <div class="row g-3">
        @foreach($projects as $project)
        <div class="col-md-6">
            <div class="card card-custom h-100">
                <div class="card-header bg-white border-bottom">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="fw-bold mb-0">{{ Str::limit($project->title, 40) }}</h6>
                        <div class="d-flex align-items-center gap-2">
                            @if($project->proposal_document_url)
                            <a href="{{ Storage::url($project->proposal_document_url) }}" target="_blank" class="text-decoration-none" title="View Proposal Document">
                                <i class="bi bi-file-pdf text-danger small"></i>
                            </a>
                            @endif
                            <span class="badge bg-dark">{{ $project->status }}</span>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <p class="small text-muted mb-2">
                        <strong>PI:</strong> {{ $project->pi->name ?? 'N/A' }} |
                        <strong>Budget:</strong> ETB {{ number_format($project->requested_budget, 2) }}
                    </p>

                    @php
                        $latestReport = $project->milestoneReports->first();
                        $progress = $latestReport ? $latestReport->progress_percentage : 0;
                        $reportCount = $project->milestoneReports->count();
                    @endphp

                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="small fw-semibold text-muted">Overall Progress</span>
                            <span class="badge bg-dark rounded-pill">{{ $progress }}%</span>
                        </div>
                        <div class="progress" style="height: 12px; border-radius: 6px; background: #e9ecef;">
                            <div class="progress-bar bg-dark" 
                                 style="width: {{ $progress }}%; border-radius: 6px;"
                                 role="progressbar" 
                                 aria-valuenow="{{ $progress }}" 
                                 aria-valuemin="0" 
                                 aria-valuemax="100">
                            </div>
                        </div>
                    </div>

                    <div class="d-flex align-items-center text-muted small">
                        <i class="bi bi-file-earmark-text me-2"></i>
                        <span>{{ $reportCount }} {{ Str::plural('report', $reportCount) }} submitted</span>
                    </div>
                </div>
                <div class="card-footer bg-white border-top">
                    <a href="{{ route('progress.show', $project->project_id) }}" class="btn btn-sm btn-dark w-100">
                        <i class="bi bi-eye me-1"></i> View & Submit Reports
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
@endif
@endsection
