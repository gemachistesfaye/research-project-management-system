@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold mb-0">All Research Projects & Proposals</h3>
    @if(Auth::user()->role === 'pi')
    <a href="{{ route('projects.create') }}" class="btn btn-success fw-bold">
        <i class="bi bi-plus-lg me-1"></i> New Proposal
    </a>
    @endif
</div>

<form method="GET" action="{{ route('projects.index') }}" id="filterForm">
    <div class="card card-custom p-3 mb-4">
        <div class="row g-3 align-items-center">
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control border-start-0 ps-0" placeholder="Search project title..." value="{{ request('search') }}" onchange="this.form.submit()">
                </div>
            </div>
            <div class="col-md-8">
                <label class="form-label fw-semibold small text-muted mb-1">Filter by Status</label>
                <div class="d-flex flex-wrap gap-2">
                    @php
                        $statuses = [
                            '' => ['label' => 'All', 'color' => 'dark', 'icon' => 'bi-grid'],
                            'Draft' => ['label' => 'Draft', 'color' => 'secondary', 'icon' => 'bi-pencil'],
                            'Submitted' => ['label' => 'Submitted', 'color' => 'info', 'icon' => 'bi-send'],
                            'DH_Screened' => ['label' => 'Screened', 'color' => 'primary', 'icon' => 'bi-eye'],
                            'UnderReview' => ['label' => 'Under Review', 'color' => 'warning', 'icon' => 'bi-hourglass-split'],
                            'Approved' => ['label' => 'Approved', 'color' => 'success', 'icon' => 'bi-check-circle'],
                            'Active' => ['label' => 'Active', 'color' => 'primary', 'icon' => 'bi-play-circle'],
                            'Completed' => ['label' => 'Completed', 'color' => 'success', 'icon' => 'bi-trophy'],
                            'Withdrawn' => ['label' => 'Withdrawn', 'color' => 'dark', 'icon' => 'bi-x-circle'],
                            'Terminated' => ['label' => 'Terminated', 'color' => 'danger', 'icon' => 'bi-x-octagon'],
                        ];
                        $current = request('status');
                    @endphp
                    @foreach($statuses as $val => $info)
                        <button type="submit" name="status" value="{{ $val }}"
                            class="btn btn-sm {{ $current === $val ? 'btn-dark' : 'btn-outline-secondary' }} fw-semibold rounded-pill px-3">
                            <i class="bi {{ $info['icon'] }} me-1"></i>{{ $info['label'] }}
                        </button>
                    @endforeach
                </div>
            </div>
        </div>
        <input type="hidden" name="thematic_area" value="{{ request('thematic_area') }}">
    </div>
</form>

<div class="card card-custom p-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Principal Investigator</th>
                    <th>Thematic Area</th>
                    <th>Budget</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($projects as $p)
                <tr>
                    <td class="font-monospace text-muted">#{{ $p->project_id }}</td>
                    <td class="fw-bold text-dark" style="max-width: 250px;" title="{{ $p->title }}">{{ Str::limit($p->title, 50) }}</td>
                    <td>
                        <span class="d-inline-flex align-items-center"><i class="bi bi-person-circle me-1 text-muted"></i> {{ $p->pi->name }}</span>
                    </td>
                    <td>
                        <span class="badge bg-light text-dark border">{{ $p->thematicArea ? $p->thematicArea->title : 'General' }}</span>
                    </td>
                    <td class="fw-semibold text-dark">{{ number_format($p->requested_budget, 2) }} ETB</td>
                    <td>
                        @if($p->status === 'Draft')
                            <span class="badge bg-secondary text-white px-2 py-1"><i class="bi bi-pencil me-1"></i>Draft</span>
                        @elseif($p->status === 'Withdrawn')
                            <span class="badge bg-dark text-white px-2 py-1"><i class="bi bi-x-circle me-1"></i>Withdrawn</span>
                        @elseif($p->status === 'Approved' || $p->status === 'Completed')
                            <span class="badge bg-success text-white px-2 py-1"><i class="bi bi-check-circle me-1"></i>{{ $p->status }}</span>
                        @elseif($p->status === 'Active')
                            <span class="badge bg-primary text-white px-2 py-1"><i class="bi bi-play-circle me-1"></i>Active</span>
                        @elseif(in_array($p->status, ['Submitted', 'DH_Screened', 'UnderReview']))
                            <span class="badge bg-warning text-dark px-2 py-1"><i class="bi bi-hourglass-split me-1"></i>{{ $p->status }}</span>
                        @elseif($p->status === 'Rejected' || $p->status === 'Terminated')
                            <span class="badge bg-danger text-white px-2 py-1"><i class="bi bi-x-circle me-1"></i>{{ $p->status }}</span>
                        @elseif($p->status === 'PendingCancellation')
                            <span class="badge bg-warning text-dark px-2 py-1"><i class="bi bi-hourglass-split me-1"></i>Pending Withdraw</span>
                        @else
                            <span class="badge bg-secondary text-white px-2 py-1">{{ $p->status }}</span>
                        @endif
                    </td>
                    <td>
                        <div class="d-flex gap-1">
                            @if(in_array($p->status, ['Draft', 'Withdrawn']) && Auth::user()->role === 'pi')
                                <form action="{{ route('projects.submit', $p->project_id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="button" class="btn btn-sm btn-dark confirm-btn" data-confirm-title="Submit Proposal" data-confirm-message="This proposal will be sent to the Department Head for initial screening." data-confirm-icon="bi-send" data-confirm-color="text-success" data-confirm-btn-text="Yes, Submit" data-confirm-btn-class="btn-success" style="min-width:80px"><i class="bi bi-send me-1"></i>Submit</button>
                                </form>
                            @endif
                            @if(Auth::user()->role === 'pi' && in_array($p->status, ['Submitted', 'DH_Screened', 'UnderReview']))
                                <form action="{{ route('projects.cancel', $p->project_id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="button" class="btn btn-sm btn-outline-danger confirm-btn" data-confirm-title="Withdraw Proposal" data-confirm-message="Are you sure you want to withdraw this proposal?" data-confirm-icon="bi-x-circle" data-confirm-color="text-danger" data-confirm-btn-text="Yes, Withdraw" data-confirm-btn-class="btn-danger" style="min-width:80px"><i class="bi bi-x-circle me-1"></i>Withdraw</button>
                                </form>
                            @endif
                            <a href="{{ route('projects.show', $p->project_id) }}" class="btn btn-sm btn-outline-dark" style="min-width:80px">
                                <i class="bi bi-eye me-1"></i>View
                            </a>
                            @if($p->proposal_document_url)
                            <a href="{{ Storage::url($p->proposal_document_url) }}" target="_blank" class="btn btn-sm btn-outline-danger" title="View Document">
                                <i class="bi bi-file-pdf"></i>
                            </a>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">
                        <i class="bi bi-folder2-open fs-3 d-block text-secondary mb-2"></i>
                        No research projects found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-3">
        {{ $projects->links() }}
    </div>
</div>

@endsection

