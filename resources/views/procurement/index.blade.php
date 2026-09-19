@extends('layouts.app')

@section('content')
<style>
    .timeline-step { position: relative; text-align: center; flex: 1; }
    .timeline-step .step-circle {
        width: 28px; height: 28px; border-radius: 50%;
        display: inline-flex; align-items: center; justify-content: center;
        font-size: 12px; font-weight: 600; border: 2px solid #dee2e6; background: #fff; color: #6c757d; position: relative; z-index: 2;
    }
    .timeline-step.completed .step-circle { border-color: #198754; background: #198754; color: #fff; }
    .timeline-step.active .step-circle { border-color: #ffc107; background: #ffc107; color: #000; }
    .timeline-step .step-label { font-size: 10px; color: #6c757d; margin-top: 4px; display: block; }
    .timeline-step.completed .step-label { color: #198754; font-weight: 600; }
    .timeline-step.active .step-label { color: #856404; font-weight: 600; }
    .timeline-step::after {
        content: ''; position: absolute; top: 14px; left: 50%; width: 100%; height: 2px;
        background: #dee2e6; z-index: 1;
    }
    .timeline-step:last-child::after { display: none; }
    .timeline-step.completed::after { background: #198754; }
    .budget-bar { height: 8px; border-radius: 4px; background: #e9ecef; overflow: hidden; }
    .budget-bar .fill { height: 100%; border-radius: 4px; transition: width .4s ease; }
    .budget-bar .fill-ok { background: linear-gradient(90deg, #198754, #20c997); }
    .budget-bar .fill-warn { background: linear-gradient(90deg, #ffc107, #fd7e14); }
    .budget-bar .fill-danger { background: linear-gradient(90deg, #dc3545, #e83e8c); }
    .category-chip {
        display: inline-block; padding: 4px 12px; border-radius: 20px;
        font-size: 12px; font-weight: 500; cursor: pointer; border: 1px solid #dee2e6;
        background: #fff; color: #495057; transition: all .2s;
    }
    .category-chip:hover { background: #f8f9fa; }
    .category-chip.active { background: #0d6efd; color: #fff; border-color: #0d6efd; }
</style>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-0"><i class="bi bi-cart-check me-2 text-primary"></i> Procurement Tracker (SCR-20)</h3>
        <span class="text-muted">Submit and track purchase requests for active projects</span>
    </div>
</div>

@if($budgetInfo->isNotEmpty())
<div class="card card-custom mb-4">
    <div class="card-header bg-white border-bottom fw-bold">
        <i class="bi bi-wallet2 me-2 text-warning"></i> Budget Overview
    </div>
    <div class="card-body">
        <div class="row g-3">
            @foreach($budgetInfo as $b)
                @php
                    $pct = $b['approved_budget'] > 0 ? ($b['total_spent'] / $b['approved_budget']) * 100 : 0;
                    $barClass = $pct >= 90 ? 'fill-danger' : ($pct >= 70 ? 'fill-warn' : 'fill-ok');
                @endphp
                <div class="col-md-4">
                    <div class="border rounded p-3 h-100">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="fw-bold small">{{ Str::limit($b['title'], 35) }}</span>
                            <span class="badge {{ $pct >= 90 ? 'bg-danger' : ($pct >= 70 ? 'bg-warning text-dark' : 'bg-success') }}">
                                {{ number_format($pct, 1) }}%
                            </span>
                        </div>
                        <div class="budget-bar mb-2">
                            <div class="fill {{ $barClass }}" style="width: {{ min($pct, 100) }}%"></div>
                        </div>
                        <div class="d-flex justify-content-between text-muted" style="font-size:12px">
                            <span>Spent: <strong class="text-dark">{{ number_format($b['total_spent'], 2) }} ETB</strong></span>
                            <span>Budget: <strong class="text-dark">{{ number_format($b['approved_budget'], 2) }} ETB</strong></span>
                        </div>
                        <div class="d-flex justify-content-between text-muted mt-1" style="font-size:11px">
                            <span>Remaining: <strong class="{{ $b['remaining'] < 0 ? 'text-danger' : 'text-success' }}">{{ number_format($b['remaining'], 2) }} ETB</strong></span>
                            @if($b['pending_spend'] > 0)
                            <span class="text-warning">+ {{ number_format($b['pending_spend'], 2) }} ETB pending</span>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endif

<div class="row g-4">
    {{-- New Purchase Request Form --}}
    <div class="col-lg-4">
        <div class="card card-custom">
            <div class="card-header bg-success text-white fw-bold">
                <i class="bi bi-plus-circle me-2"></i> New Purchase Request
            </div>
            <div class="card-body">
                @if($activeProjects->isEmpty())
                    <div class="text-center text-muted py-3">
                        <i class="bi bi-folder2-open fs-3 d-block mb-2"></i>
                        <p class="mb-1 fw-semibold">No Active Projects</p>
                        <small>You need an approved project to submit procurement requests. Once your project is active, you can request equipment, reagents, and other supplies here.</small>
                    </div>
                @else
                    <form action="{{ route('procurement.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label fw-bold small">Item Name <span class="text-danger">*</span></label>
                            <input type="text" name="item_name" class="form-control form-control-sm @error('item_name') is-invalid @enderror"
                                   required placeholder="e.g. Laptop, Reagents, Stationery" value="{{ old('item_name') }}">
                            @error('item_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small">Category <span class="text-danger">*</span></label>
                            <select name="category" class="form-select form-select-sm @error('category') is-invalid @enderror" required>
                                <option value="">-- Select Category --</option>
                                @foreach(['Equipment', 'Reagents', 'Consumables', 'Other'] as $cat)
                                    <option value="{{ $cat }}" {{ old('category') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                                @endforeach
                            </select>
                            @error('category')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small">Description</label>
                            <textarea name="description" rows="3" class="form-control form-control-sm @error('description') is-invalid @enderror"
                                      placeholder="Detailed description of the requested item...">{{ old('description') }}</textarea>
                            @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small">Estimated Cost (ETB) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" min="0" name="estimated_cost" id="estimated_cost"
                                   class="form-control form-control-sm @error('estimated_cost') is-invalid @enderror"
                                   required placeholder="0.00" value="{{ old('estimated_cost') }}">
                            @error('estimated_cost')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small">Project <span class="text-danger">*</span></label>
                            <select name="project_id" id="project_select" class="form-select form-select-sm @error('project_id') is-invalid @enderror" required>
                                <option value="">-- Select Project --</option>
                                @foreach($activeProjects as $p)
                                    <option value="{{ $p->project_id }}" data-budget="{{ $p->approved_budget ?? 0 }}" data-spent="{{ $budgetInfo->where('project_id', $p->project_id)->first()['total_spent'] ?? 0 }}" {{ old('project_id') == $p->project_id ? 'selected' : '' }}>
                                        {{ Str::limit($p->title, 40) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('project_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div id="budget-warning" class="alert alert-danger d-none py-2 px-3 mb-3" style="font-size:12px">
                            <i class="bi bi-exclamation-triangle-fill me-1"></i>
                            <span id="budget-warning-text"></span>
                        </div>

                        <button type="submit" class="btn btn-success btn-sm w-100 fw-bold confirm-btn" data-confirm-title="Submit Procurement" data-confirm-message="Submit this purchase request?" data-confirm-icon="bi-cart-check" data-confirm-color="text-success" data-confirm-btn-text="Yes, Submit" data-confirm-btn-class="btn-success">
                            <i class="bi bi-send me-1"></i> Submit Request
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    {{-- Procurement Requests Table --}}
    <div class="col-lg-8">
        <div class="card card-custom">
            <div class="card-header bg-white border-bottom fw-bold">
                <div class="d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-list-check me-2 text-primary"></i> Purchase Requests</span>
                    <span class="badge bg-primary rounded-pill">{{ $requests->count() }}</span>
                </div>
                <div class="mt-2 d-flex flex-wrap gap-2">
                    @php $currentCategory = request('category', 'All'); @endphp
                    <a href="{{ route('procurement.index') }}" class="category-chip {{ $currentCategory === 'All' ? 'active' : '' }}">All</a>
                    @foreach(['Equipment', 'Reagents', 'Consumables', 'Other'] as $cat)
                        <a href="{{ route('procurement.index', ['category' => $cat]) }}" class="category-chip {{ $currentCategory === $cat ? 'active' : '' }}">{{ $cat }}</a>
                    @endforeach
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Item</th>
                                <th>Category</th>
                                <th>Est. Cost</th>
                                <th>Project</th>
                                <th>Status Timeline</th>
                                @if(Auth::user()->role === 'coordinator')
                                <th>Action</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($requests as $req)
                            <tr>
                                <td><span class="text-muted">#PR-{{ $req->id }}</span></td>
                                <td>
                                    <div class="fw-bold">{{ $req->item_name }}</div>
                                    @if($req->description)
                                        <small class="text-muted">{{ Str::limit($req->description, 50) }}</small>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $catColors = ['Equipment' => 'info', 'Reagents' => 'purple', 'Consumables' => 'success', 'Other' => 'secondary'];
                                        $catColor = $catColors[$req->category] ?? 'secondary';
                                    @endphp
                                    <span class="badge bg-{{ $catColor }}">{{ $req->category }}</span>
                                </td>
                                <td class="fw-bold">{{ number_format($req->estimated_cost, 2) }} ETB</td>
                                <td><span class="text-muted">{{ Str::limit($req->project->title ?? 'N/A', 25) }}</span>
                                    @if($req->project && $req->project->proposal_document_url)
                                    <a href="{{ Storage::url($req->project->proposal_document_url) }}" target="_blank" class="text-decoration-none ms-1" title="View Proposal Document">
                                        <i class="bi bi-file-pdf text-danger small"></i>
                                    </a>
                                    @endif
                                </td>
                                <td style="min-width: 220px;">
                                    @php
                                        $statuses = ['Pending', 'Approved', 'Purchased'];
                                        $currentIdx = array_search($req->status, $statuses);
                                        if ($currentIdx === false && $req->status === 'Rejected') $currentIdx = -1;
                                    @endphp
                                    <div class="d-flex align-items-center">
                                        @foreach($statuses as $idx => $s)
                                            <div class="timeline-step {{ $idx < $currentIdx ? 'completed' : '' }} {{ $idx === $currentIdx ? 'active' : '' }}">
                                                <span class="step-circle">
                                                    @if($idx < $currentIdx)
                                                        <i class="bi bi-check-lg"></i>
                                                    @else
                                                        {{ $idx + 1 }}
                                                    @endif
                                                </span>
                                                <span class="step-label">{{ $s }}</span>
                                            </div>
                                        @endforeach
                                        @if($req->status === 'Rejected')
                                            <span class="badge bg-danger ms-2">Rejected</span>
                                        @endif
                                    </div>
                                </td>
                                @if(Auth::user()->role === 'coordinator')
                                <td>
                                    @if($req->status === 'Pending')
                                        <div class="btn-group btn-group-sm">
                                            <form action="{{ route('procurement.approve', $req->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="button" class="btn btn-success btn-sm confirm-btn" title="Approve" data-confirm-title="Approve Purchase" data-confirm-message="Approve this purchase request?" data-confirm-icon="bi-check-circle" data-confirm-color="text-success" data-confirm-btn-text="Yes, Approve" data-confirm-btn-class="btn-success">
                                                    <i class="bi bi-check-lg"></i>
                                                </button>
                                            </form>
                                            <form action="{{ route('procurement.reject', $req->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="button" class="btn btn-danger btn-sm confirm-btn" title="Reject" data-confirm-title="Reject Purchase" data-confirm-message="Reject this purchase request?" data-confirm-icon="bi-x-circle" data-confirm-color="text-danger" data-confirm-btn-text="Yes, Reject" data-confirm-btn-class="btn-danger">
                                                    <i class="bi bi-x-lg"></i>
                                                </button>
                                            </form>
                                        </div>
                                    @else
                                        <span class="text-muted small">—</span>
                                    @endif
                                </td>
                                @endif
                            </tr>
                            @empty
                            <tr>
                                <td colspan="{{ Auth::user()->role === 'coordinator' ? 7 : 6 }}" class="text-center py-5">
                                    <i class="bi bi-cart3 fs-1 d-block mb-3 text-muted"></i>
                                    <h6 class="text-muted mb-1">No Purchase Requests Found</h6>
                                    <p class="text-muted mb-3" style="font-size:13px">
                                        @if(request('category'))
                                            No requests match the "{{ request('category') }}" category.
                                            <a href="{{ route('procurement.index') }}">Clear filter</a>
                                        @else
                                            Start by submitting a purchase request for one of your active projects.
                                        @endif
                                    </p>
                                    @if(!request('category') && $activeProjects->isNotEmpty())
                                        <a href="#" class="btn btn-success btn-sm" onclick="document.querySelector('form').scrollIntoView({behavior:'smooth'}); return false;">
                                            <i class="bi bi-plus-circle me-1"></i> Create First Request
                                        </a>
                                    @endif
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@if($activeProjects->isNotEmpty())
<script>
document.addEventListener('DOMContentLoaded', function() {
    const projectSelect = document.getElementById('project_select');
    const costInput = document.getElementById('estimated_cost');
    const warning = document.getElementById('budget-warning');
    const warningText = document.getElementById('budget-warning-text');

    function checkBudget() {
        if (!projectSelect.value || !costInput.value) {
            warning.classList.add('d-none');
            return;
        }
        const opt = projectSelect.options[projectSelect.selectedIndex];
        const budget = parseFloat(opt.dataset.budget) || 0;
        const spent = parseFloat(opt.dataset.spent) || 0;
        const cost = parseFloat(costInput.value) || 0;
        const remaining = budget - spent;

        if (budget === 0) {
            warning.classList.remove('d-none', 'alert-danger');
            warning.classList.add('alert-info');
            warningText.textContent = 'No approved budget set for this project. Proceed with caution.';
        } else if (cost > remaining) {
            warning.classList.remove('d-none', 'alert-info');
            warning.classList.add('alert-danger');
            warningText.textContent = 'Warning: This request (' + cost.toLocaleString() + ' ETB) exceeds the remaining budget (' + remaining.toLocaleString() + ' ETB). It will be flagged for review.';
        } else if (remaining - cost < budget * 0.1) {
            warning.classList.remove('d-none', 'alert-danger', 'alert-info');
            warning.classList.add('alert-warning');
            warningText.textContent = 'Note: This request will use over 90% of the remaining budget. Only ' + (remaining - cost).toLocaleString() + ' ETB will remain.';
        } else {
            warning.classList.add('d-none');
        }
    }

    projectSelect.addEventListener('change', checkBudget);
    costInput.addEventListener('input', checkBudget);
    checkBudget();
});
</script>
@endif
@endsection
