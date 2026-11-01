@extends('layouts.app')

@section('content')
<div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2 mb-4">
    <div>
        <h3 class="fw-bold mb-0"><i class="bi bi-megaphone me-2 text-dark"></i>Calls for Research Proposals &amp; Abstracts</h3>
        <span class="text-muted small">Seasonal and urgent research calls posted by coordinators and department heads</span>
    </div>
    @if(auth()->user()->role === 'coordinator' || auth()->user()->role === 'dh' || auth()->user()->role === 'admin')
    <div>
        <button type="button" class="btn btn-dark fw-bold text-nowrap" data-bs-toggle="modal" data-bs-target="#createCallModal">
            <i class="bi bi-plus-circle me-1"></i> Post New Call
        </button>
    </div>
    @endif
</div>

{{-- Active calls banner for PIs / TMs --}}
@if(in_array(auth()->user()->role, ['pi', 'tm']))
@php $nowOpen = \App\Models\ResearchCall::active()->count(); @endphp
@if($nowOpen > 0)
<div class="alert alert-success d-flex align-items-center shadow-sm mb-4">
    <i class="bi bi-bullhorn fs-4 me-3"></i>
    <div class="flex-grow-1">
        <strong>{{ $nowOpen }} open research call(s)</strong> currently accepting submissions.
    </div>
    <a href="#openCallsSection" class="btn btn-success btn-sm fw-bold">View Open Calls</a>
</div>
@endif
@endif

{{-- Filters --}}
<div class="card card-custom p-3 mb-4">
    <form method="GET" class="row g-2 align-items-end">
        <div class="col-12 col-md-4">
            <label class="form-label small fw-bold mb-1">Call Type</label>
            <select name="call_type" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">All Types</option>
                <option value="project" {{ request('call_type') === 'project' ? 'selected' : '' }}>Project Proposal</option>
                <option value="abstract" {{ request('call_type') === 'abstract' ? 'selected' : '' }}>Abstract</option>
                <option value="community_service" {{ request('call_type') === 'community_service' ? 'selected' : '' }}>Community Service</option>
            </select>
        </div>
        <div class="col-12 col-md-3">
            <label class="form-label small fw-bold mb-1">Status</label>
            <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">Active &amp; Closed</option>
                <option value="Open" {{ request('status') === 'Open' ? 'selected' : '' }}>Open Only</option>
                <option value="Closed" {{ request('status') === 'Closed' ? 'selected' : '' }}>Closed Only</option>
                <option value="Draft" {{ request('status') === 'Draft' ? 'selected' : '' }}>Drafts</option>
            </select>
        </div>
        <div class="col-12 col-md-auto">
            <a href="{{ route('calls.index') }}" class="btn btn-outline-secondary btn-sm w-100 w-md-auto">
                <i class="bi bi-x-circle me-1"></i> Reset
            </a>
        </div>
    </form>
</div>

{{-- Open calls highlight --}}
<div id="openCallsSection" class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-3 mb-4">
    @forelse($calls->getCollection()->where('status', 'Open') as $call)
    <div class="col">
        <div class="card card-custom h-100 p-3">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <span class="badge {{ $call->call_type === 'abstract' ? 'bg-info' : ($call->call_type === 'community_service' ? 'bg-success' : 'bg-dark') }}">
                    {{ ucwords(str_replace('_', ' ', $call->call_type)) }}
                </span>
                <span class="badge bg-success"><i class="bi bi-circle-fill me-1" style="font-size:0.5rem;"></i>Open</span>
            </div>
            <h6 class="fw-bold mb-1">{{ $call->title }}</h6>
            <p class="text-muted small mb-2">{{ \Illuminate\Support\Str::limit($call->description, 140) }}</p>
            <div class="small text-muted mb-2">
                @if($call->thematicArea)<span class="badge bg-light text-dark border me-1"><i class="bi bi-diagram-3"></i> {{ $call->thematicArea->title }}</span>@endif
                @if($call->funding_organization)<span class="badge bg-light text-dark border"><i class="bi bi-cash-coin"></i> {{ $call->funding_organization }}</span>@endif
            </div>
            <div class="d-flex justify-content-between align-items-center mt-auto pt-2 border-top">
                <span class="small text-muted">
                    @if($call->submission_deadline)
                        <i class="bi bi-calendar-event me-1"></i>Deadline: {{ $call->submission_deadline->format('d M Y') }}
                    @else
                        <i class="bi bi-calendar-event me-1"></i>Rolling submission
                    @endif
                </span>
                @if($call->budget_ceiling)
                    <span class="small fw-bold text-success">≤ ETB {{ number_format($call->budget_ceiling, 0) }}</span>
                @endif
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="text-center text-muted py-4">
            <i class="bi bi-megaphone fs-3 d-block mb-2"></i>
            No open research calls right now. Check back after the next posting cycle.
        </div>
    </div>
    @endforelse
</div>

{{-- All calls table --}}
@if(auth()->user()->role === 'coordinator' || auth()->user()->role === 'dh' || auth()->user()->role === 'admin')
<div class="card card-custom p-4">
    <h5 class="fw-bold mb-3"><i class="bi bi-list-check me-2 text-dark"></i>All Posted Calls</h5>
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Title</th>
                    <th>Type</th>
                    <th>Department</th>
                    <th>Deadline</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($calls as $call)
                <tr>
                    <td>#{{ $call->id }}</td>
                    <td class="fw-bold">{{ $call->title }}</td>
                    <td><span class="badge bg-secondary">{{ ucwords(str_replace('_', ' ', $call->call_type)) }}</span></td>
                    <td>{{ $call->department->name ?? 'University-wide' }}</td>
                    <td class="small">{{ $call->submission_deadline ? $call->submission_deadline->format('d M Y') : 'Rolling' }}</td>
                    <td>
                        @if($call->status === 'Open')
                            <span class="badge bg-success">Open</span>
                        @elseif($call->status === 'Closed')
                            <span class="badge bg-secondary">Closed</span>
                        @else
                            <span class="badge bg-warning text-dark">Draft</span>
                        @endif
                    </td>
                    <td>
                        <div class="btn-group btn-group-sm">
                            <button type="button" class="btn btn-outline-dark" data-bs-toggle="modal" data-bs-target="#editCallModal{{ $call->id }}">
                                <i class="bi bi-pencil"></i> Edit
                            </button>
                            @if($call->status === 'Open')
                            <form method="POST" action="{{ route('calls.status', $call->id) }}">
                                @csrf
                                <input type="hidden" name="status" value="Closed">
                                <button type="submit" class="btn btn-outline-warning confirm-btn" data-confirm-title="Close Call" data-confirm-message="Close this call for new submissions?" data-confirm-icon="bi-pause-circle" data-confirm-color="text-warning" data-confirm-btn-text="Yes, Close" data-confirm-btn-class="btn-warning">
                                    <i class="bi bi-pause-circle"></i> Close
                                </button>
                            </form>
                            @else
                            <form method="POST" action="{{ route('calls.status', $call->id) }}">
                                @csrf
                                <input type="hidden" name="status" value="Open">
                                <button type="submit" class="btn btn-outline-success confirm-btn" data-confirm-title="Reopen Call" data-confirm-message="Reopen this call for submissions?" data-confirm-icon="bi-play-circle" data-confirm-color="text-success" data-confirm-btn-text="Yes, Reopen" data-confirm-btn-class="btn-success">
                                    <i class="bi bi-play-circle"></i> Reopen
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">
                        <i class="bi bi-megaphone fs-3 d-block mb-2"></i>
                        No calls posted yet.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="d-flex justify-content-center mt-3">
        {{ $calls->links() }}
    </div>
</div>
@endif

{{-- Create Modal --}}
<div class="modal fade" id="createCallModal" tabindex="-1" aria-labelledby="createCallModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" action="{{ route('calls.store') }}">
                @csrf
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title fw-bold" id="createCallModalLabel">
                        <i class="bi bi-plus-circle me-2"></i>Post New Research Call
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-bold">Call Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control" required placeholder="e.g. 2026 Call for Interdisciplinary Climate Research Proposals">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold">Description <span class="text-danger">*</span></label>
                            <textarea name="description" class="form-control" rows="3" required placeholder="Guidelines, scope, deliverables..."></textarea>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Call Type <span class="text-danger">*</span></label>
                            <select name="call_type" class="form-select" required>
                                <option value="project">Project Proposal</option>
                                <option value="abstract">Abstract</option>
                                <option value="community_service">Community Service</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Department</label>
                            <select name="dept_id" class="form-select">
                                <option value="">University-wide</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Thematic Area</label>
                            <select name="thematic_id" class="form-select">
                                <option value="">Any</option>
                                @foreach($thematicAreas as $ta)
                                    <option value="{{ $ta->id }}">{{ $ta->title }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Funding Organization</label>
                            <input type="text" name="funding_organization" class="form-control" placeholder="e.g. Institutional Research Fund">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Budget Ceiling (ETB)</label>
                            <input type="number" name="budget_ceiling" class="form-control" min="0" step="0.01" placeholder="e.g. 500000">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Submission Deadline</label>
                            <input type="date" name="submission_deadline" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Status</label>
                            <select name="status" class="form-select">
                                <option value="Open" selected>Open (immediately)</option>
                                <option value="Draft">Save as Draft</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-dark fw-bold px-4 confirm-btn" data-confirm-title="Publish Research Call" data-confirm-message="Publish this call for research submissions?" data-confirm-icon="bi-megaphone" data-confirm-color="text-dark" data-confirm-btn-text="Yes, Publish" data-confirm-btn-class="btn-dark">
                        <i class="bi bi-check-lg me-1"></i> Publish Call
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Edit Modals --}}
@foreach($calls as $call)
<div class="modal fade" id="editCallModal{{ $call->id }}" tabindex="-1" aria-labelledby="editCallModalLabel{{ $call->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" action="{{ route('calls.update', $call->id) }}">
                @csrf
                @method('PUT')
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title fw-bold" id="editCallModalLabel{{ $call->id }}">
                        <i class="bi bi-pencil me-2"></i>Edit Call — {{ $call->title }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-bold">Call Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control" value="{{ $call->title }}" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold">Description <span class="text-danger">*</span></label>
                            <textarea name="description" class="form-control" rows="3" required>{{ $call->description }}</textarea>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Call Type</label>
                            <select name="call_type" class="form-select">
                                <option value="project" {{ $call->call_type === 'project' ? 'selected' : '' }}>Project Proposal</option>
                                <option value="abstract" {{ $call->call_type === 'abstract' ? 'selected' : '' }}>Abstract</option>
                                <option value="community_service" {{ $call->call_type === 'community_service' ? 'selected' : '' }}>Community Service</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Department</label>
                            <select name="dept_id" class="form-select">
                                <option value="">University-wide</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->id }}" {{ $call->dept_id == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Thematic Area</label>
                            <select name="thematic_id" class="form-select">
                                <option value="">Any</option>
                                @foreach($thematicAreas as $ta)
                                    <option value="{{ $ta->id }}" {{ $call->thematic_id == $ta->id ? 'selected' : '' }}>{{ $ta->title }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Funding Organization</label>
                            <input type="text" name="funding_organization" class="form-control" value="{{ $call->funding_organization }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Budget Ceiling (ETB)</label>
                            <input type="number" name="budget_ceiling" class="form-control" min="0" step="0.01" value="{{ $call->budget_ceiling }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Submission Deadline</label>
                            <input type="date" name="submission_deadline" class="form-control" value="{{ $call->submission_deadline ? $call->submission_deadline->format('Y-m-d') : '' }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Status</label>
                            <select name="status" class="form-select">
                                <option value="Open" {{ $call->status === 'Open' ? 'selected' : '' }}>Open</option>
                                <option value="Closed" {{ $call->status === 'Closed' ? 'selected' : '' }}>Closed</option>
                                <option value="Draft" {{ $call->status === 'Draft' ? 'selected' : '' }}>Draft</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-dark fw-bold px-4 confirm-btn" data-confirm-title="Save Changes" data-confirm-message="Save changes to this call?" data-confirm-icon="bi-check-circle" data-confirm-color="text-dark" data-confirm-btn-text="Yes, Save" data-confirm-btn-class="btn-dark">
                        <i class="bi bi-check-lg me-1"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

@if(auth()->user()->role === 'coordinator' || auth()->user()->role === 'dh' || auth()->user()->role === 'admin')
@foreach($calls as $call)
<div class="modal fade" id="deleteCallModal{{ $call->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="{{ route('calls.destroy', $call->id) }}">
                @csrf
                @method('DELETE')
                <div class="modal-header bg-danger-subtle">
                    <h5 class="modal-title fw-bold"><i class="bi bi-trash me-2 text-danger"></i>Delete Call</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body small">Delete "<strong>{{ $call->title }}</strong>" permanently?</div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger btn-sm fw-bold">Delete</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach
@endif
@endsection