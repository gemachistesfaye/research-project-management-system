@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-0"><i class="bi bi-graph-up me-2 text-dark"></i> Progress Reports &amp; Milestones</h3>
        <span class="text-muted">
            @if(in_array(Auth::user()->role, ['coordinator', 'admin', 'dh', 'dean', 'vparttcs']))
                Audit, review, and track milestone progress for active research projects
            @else
                Submit and track milestone progress for your research projects
            @endif
        </span>
    </div>
</div>

@if($projects->isEmpty())
    <div class="alert alert-secondary">
        <i class="bi bi-info-circle me-2"></i>No active projects found.
    </div>
@else
    <div class="row g-3">
        @foreach($projects as $project)
        @php
            $pendingReportsCount = $project->milestoneReports->where('status', 'Submitted')->count();
            $latestReport = $project->milestoneReports->first();
            $progress = $latestReport ? $latestReport->progress_percentage : 0;
            $reportCount = $project->milestoneReports->count();
        @endphp
        <div class="col-md-6">
            <div class="card card-custom h-100 shadow-sm border {{ $pendingReportsCount > 0 && in_array(Auth::user()->role, ['coordinator', 'admin']) ? 'border-warning' : '' }}">
                <div class="card-header bg-white border-bottom py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="fw-bold mb-0 text-truncate" style="max-width: 70%;">{{ $project->title }}</h6>
                        <div class="d-flex align-items-center gap-1">
                            @if($project->proposal_document_url)
                            <a href="{{ Storage::url($project->proposal_document_url) }}" target="_blank" class="btn btn-xs btn-outline-danger py-0 px-1" title="View Proposal PDF">
                                <i class="bi bi-file-pdf"></i>
                            </a>
                            @endif
                            @if($pendingReportsCount > 0 && in_array(Auth::user()->role, ['coordinator', 'admin']))
                                <span class="badge bg-warning text-dark"><i class="bi bi-clock me-1"></i>{{ $pendingReportsCount }} Pending Audit</span>
                            @else
                                <span class="badge bg-dark">{{ $project->status }}</span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <p class="small text-muted mb-2">
                        <strong>PI:</strong> {{ $project->pi->name ?? 'N/A' }} |
                        <strong>Budget:</strong> ETB {{ number_format($project->approved_budget ?: $project->requested_budget, 2) }}
                    </p>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="small fw-semibold text-muted">Milestone Progress</span>
                            <span class="badge bg-dark rounded-pill">{{ $progress }}%</span>
                        </div>
                        <div class="progress" style="height: 10px; border-radius: 5px; background: #e9ecef;">
                            <div class="progress-bar bg-dark" 
                                 style="width: {{ $progress }}%; border-radius: 5px;"
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
                    <a href="{{ route('progress.show', $project->project_id) }}" class="btn btn-sm btn-dark w-100 py-2 fw-semibold">
                        @if(in_array(Auth::user()->role, ['coordinator', 'admin']))
                            <i class="bi bi-shield-check me-1"></i> Audit &amp; Review Reports
                        @else
                            <i class="bi bi-eye me-1"></i> View &amp; Submit Reports
                        @endif
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
@endif
@endsection
