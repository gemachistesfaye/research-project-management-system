@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-0"><i class="bi bi-cash-stack me-2 text-dark"></i> Finance Disbursement (SCR-14)</h3>
        <span class="text-muted">Process approved budget requests and track disbursements</span>
    </div>
</div>

{{-- Total Disbursed Summary --}}
@php
    $totalApproved = $pendingRequests->sum('approved_amount') + $disbursedHistory->sum('approved_amount');
    $totalDisbursed = $disbursedHistory->sum('approved_amount');
    $disbursementPercentage = $totalApproved > 0 ? round(($totalDisbursed / $totalApproved) * 100, 1) : 0;
@endphp

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-dark bg-opacity-10 p-3 me-3">
                        <i class="bi bi-cash-coin text-dark fs-4"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Total Disbursed</div>
                        <div class="fs-4 fw-bold text-dark">{{ number_format($totalDisbursed, 2) }} ETB</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-dark bg-opacity-10 p-3 me-3">
                        <i class="bi bi-hourglass-split text-dark fs-4"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Pending Disbursements</div>
                        <div class="fs-4 fw-bold text-dark">{{ $pendingRequests->count() }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-dark bg-opacity-10 p-3 me-3">
                        <i class="bi bi-graph-up text-dark fs-4"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Disbursement Progress</div>
                        <div class="fs-4 fw-bold text-dark">{{ $disbursementPercentage }}%</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Budget Progress Bar --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <div class="d-flex justify-content-between mb-2">
            <span class="fw-bold"><i class="bi bi-bar-chart-line me-2"></i> Overall Budget Disbursement Progress</span>
            <span class="text-muted small">{{ number_format($totalDisbursed, 2) }} ETB of {{ number_format($totalApproved, 2) }} ETB</span>
        </div>
        <div class="progress" style="height: 24px;">
            <div class="progress-bar bg-dark" role="progressbar" style="width: {{ $disbursementPercentage }}%"
                 aria-valuenow="{{ $disbursementPercentage }}" aria-valuemin="0" aria-valuemax="100">
                {{ $disbursementPercentage }}%
            </div>
        </div>
        <div class="d-flex justify-content-between mt-2">
            <small class="text-muted">0 ETB</small>
            <small class="text-muted">{{ number_format($totalApproved, 2) }} ETB</small>
        </div>
    </div>
</div>

{{-- Tranche Breakdown Overview --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white border-bottom fw-bold">
        <i class="bi bi-layers me-2 text-dark"></i> Tranche Breakdown (Standard Structure)
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-4">
                <div class="card border-dark h-100">
                    <div class="card-body text-center">
                        <div class="rounded-circle bg-dark text-white d-inline-flex align-items-center justify-content-center mb-2" style="width: 50px; height: 50px;">
                            <span class="fs-5 fw-bold">1</span>
                        </div>
                        <h6 class="fw-bold mb-1">Tranche 1</h6>
                        <div class="fs-3 fw-bold text-dark">30%</div>
                        <small class="text-muted">Initial Release</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-dark h-100">
                    <div class="card-body text-center">
                        <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center mb-2" style="width: 50px; height: 50px;">
                            <span class="fs-5 fw-bold">2</span>
                        </div>
                        <h6 class="fw-bold mb-1">Tranche 2</h6>
                        <div class="fs-3 fw-bold text-dark">40%</div>
                        <small class="text-muted">Progress-Based Release</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-dark h-100">
                    <div class="card-body text-center">
                        <div class="rounded-circle bg-info text-white d-inline-flex align-items-center justify-content-center mb-2" style="width: 50px; height: 50px;">
                            <span class="fs-5 fw-bold">3</span>
                        </div>
                        <h6 class="fw-bold mb-1">Tranche 3</h6>
                        <div class="fs-3 fw-bold text-dark">30%</div>
                        <small class="text-muted">Final Release</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-3 p-3 bg-light rounded">
            <div class="d-flex align-items-center">
                <i class="bi bi-info-circle me-2 text-dark"></i>
                <small class="text-muted">
                    <strong>Note:</strong> Tranches are released sequentially. Each tranche requires completion verification before the next release.
                    Tranche 1 (30%) is released on approval, Tranche 2 (40%) at 50% progress, and Tranche 3 (30%) upon final completion.
                </small>
            </div>
        </div>
    </div>
</div>

{{-- Pending Disbursements --}}
<div class="card card-custom mb-4">
    <div class="card-header bg-white border-bottom py-3">
        <div class="row g-2 align-items-center justify-content-between">
            <div class="col-md-5">
                <h5 class="fw-bold mb-0 text-dark">
                    <i class="bi bi-hourglass-split me-2 text-warning"></i> Pending Disbursements
                    <span class="badge bg-dark rounded-pill ms-1">{{ $pendingRequests->count() }}</span>
                </h5>
                <small class="text-muted">Approved tranches awaiting fund release</small>
            </div>
            <div class="col-md-4">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" id="pendingSearchInput" class="form-control bg-light border-start-0"
                           placeholder="Filter by Project ID, Title, PI..." onkeyup="filterPendingTable()">
                </div>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="pendingDisbursementsTable">
                <thead class="table-light">
                    <tr>
                        <th style="width: 110px;">Req &amp; Project</th>
                        <th>Research Proposal &amp; Category</th>
                        <th>Principal Investigator</th>
                        <th class="text-center">Tranche Phase</th>
                        <th class="text-end">Approved Amount</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pendingRequests as $req)
                    <tr>
                        <td>
                            <div class="font-monospace fw-bold text-dark">#REQ-{{ $req->request_id }}</div>
                            <span class="badge bg-secondary bg-opacity-10 text-secondary border mt-1" style="font-size: 0.72rem;">
                                PRJ #{{ $req->project->project_id ?? $req->project_id }}
                            </span>
                        </td>
                        <td>
                            <div class="fw-bold text-dark text-wrap" style="max-width: 360px;">
                                {{ $req->project->title ?? 'N/A' }}
                            </div>
                            <div class="d-flex gap-1 flex-wrap mt-1">
                                <span class="badge bg-light text-dark border" style="font-size: 0.7rem;">
                                    <i class="bi bi-tag me-1"></i>{{ $req->project->thematicArea->title ?? 'General' }}
                                </span>
                                @if($req->project->department)
                                <span class="badge bg-light text-secondary border" style="font-size: 0.7rem;">
                                    <i class="bi bi-building me-1"></i>{{ $req->project->department->name }}
                                </span>
                                @endif
                            </div>
                        </td>
                        <td>
                            <div class="fw-semibold text-dark">
                                <i class="bi bi-person-circle me-1 text-secondary"></i>
                                {{ preg_replace('/\s*\([^)]*\)/', '', $req->project->pi->name ?? 'N/A') }}
                            </div>
                            <small class="text-muted" style="font-size: 0.75rem;">{{ $req->project->pi->email ?? '' }}</small>
                        </td>
                        <td class="text-center">
                            @if($req->milestone_phase === 'Tranche 1')
                                <span class="badge bg-dark text-white px-2 py-1"><i class="bi bi-1-circle me-1"></i>Tranche 1 (30% Advance)</span>
                            @elseif($req->milestone_phase === 'Tranche 2')
                                <span class="badge bg-primary text-white px-2 py-1"><i class="bi bi-2-circle me-1"></i>Tranche 2 (40% Mid-Term)</span>
                            @elseif($req->milestone_phase === 'Tranche 3')
                                <span class="badge bg-info text-dark px-2 py-1"><i class="bi bi-3-circle me-1"></i>Tranche 3 (30% Final)</span>
                            @else
                                <span class="badge bg-secondary text-white px-2 py-1">{{ $req->milestone_phase }}</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <div class="fw-bold text-dark fs-6">{{ number_format($req->approved_amount ?? 0, 2) }} ETB</div>
                            <small class="text-muted" style="font-size:0.72rem;">Total: {{ number_format($req->project->approved_budget ?: $req->project->requested_budget, 2) }} ETB</small>
                        </td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-success fw-bold px-3 shadow-sm" data-bs-toggle="modal"
                                    data-bs-target="#disburseModal{{ $req->request_id }}">
                                <i class="bi bi-cash me-1"></i> Disburse
                            </button>
                        </td>
                    </tr>

                    {{-- Disbursement Modal --}}
                    <div class="modal fade" id="disburseModal{{ $req->request_id }}" tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered" style="max-width: 480px;">
                            <div class="modal-content border-0 shadow">
                                <form action="{{ route('finance.process-disbursement', $req->request_id) }}" method="POST">
                                    @csrf
                                    <div class="modal-header py-2 px-3 bg-dark text-white border-bottom">
                                        <h6 class="modal-title fw-bold text-white small mb-0">
                                            <i class="bi bi-cash me-1 text-success"></i> Release Disbursement &bull; {{ $req->milestone_phase ?? 'Tranche' }}
                                        </h6>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body p-3">
                                        {{-- Project Summary Snippet --}}
                                        <div class="p-3 mb-3 bg-light rounded border">
                                            <div class="d-flex justify-content-between text-muted small mb-1">
                                                <span>PRJ #{{ $req->project->project_id ?? $req->project_id }} &bull; REQ #{{ $req->request_id }}</span>
                                                <span class="badge bg-secondary text-white">{{ $req->project->thematicArea->title ?? 'General' }}</span>
                                            </div>
                                            <div class="fw-bold text-dark">{{ $req->project->title ?? 'N/A' }}</div>
                                            <hr class="my-2 opacity-25">
                                            <div class="d-flex justify-content-between text-muted" style="font-size: 0.78rem;">
                                                <span><i class="bi bi-person me-1"></i>{{ preg_replace('/\s*\([^)]*\)/', '', $req->project->pi->name ?? 'N/A') }}</span>
                                                <span class="fw-bold text-dark fs-6">{{ number_format($req->approved_amount ?? 0, 2) }} ETB</span>
                                            </div>
                                        </div>

                                        <div class="row g-2 mb-2">
                                            <div class="col-6">
                                                <label class="form-label small fw-bold mb-1">Approved Amount (ETB)</label>
                                                <div class="input-group input-group-sm">
                                                    <input type="text" class="form-control form-control-sm fw-bold bg-light"
                                                           value="{{ number_format($req->approved_amount, 2) }} ETB" readonly tabindex="-1">
                                                    <span class="input-group-text bg-light text-muted"><i class="bi bi-lock-fill"></i></span>
                                                </div>
                                                <input type="hidden" name="amount" value="{{ $req->approved_amount }}">
                                                <small class="text-muted" style="font-size:0.7rem;">Locked to ratified tranche budget</small>
                                            </div>
                                            <div class="col-6">
                                                <label class="form-label small fw-bold mb-1">Payment Method <span class="text-danger">*</span></label>
                                                <select name="payment_method" class="form-select form-select-sm @error('payment_method') is-invalid @enderror" required>
                                                    <option value="Bank Transfer" selected>Bank Transfer</option>
                                                    <option value="Check">Check</option>
                                                    <option value="Cash">Cash</option>
                                                </select>
                                                @error('payment_method')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            </div>
                                        </div>

                                        <div class="mb-2">
                                            <label class="form-label small fw-bold mb-1">Bank Voucher / Check Reference</label>
                                            <input type="text" name="notes" class="form-control form-control-sm @error('notes') is-invalid @enderror"
                                                   placeholder="e.g. CBE Ref #GMU-2026-9812 / Voucher #4421" value="{{ old('notes') }}">
                                            @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                    </div>
                                    <div class="modal-footer py-2 px-3 bg-light border-top d-flex justify-content-between">
                                        <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-sm btn-success fw-bold px-3">
                                            <i class="bi bi-check-lg me-1"></i> Release Funds
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            <i class="bi bi-check-circle fs-3 d-block mb-2 text-dark"></i>
                            No pending disbursements. All approved requests have been processed.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Disbursement History --}}
<div class="card card-custom">
    <div class="card-header bg-white border-bottom py-3">
        <div class="row g-2 align-items-center justify-content-between">
            <div class="col-md-5">
                <h5 class="fw-bold mb-0 text-dark">
                    <i class="bi bi-clock-history me-2 text-dark"></i> Disbursement History
                    <span class="badge bg-secondary rounded-pill ms-1">{{ $disbursedHistory->count() }}</span>
                </h5>
                <small class="text-muted">Audit trail of completed releases</small>
            </div>
            <div class="col-md-4">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" id="historySearchInput" class="form-control bg-light border-start-0"
                           placeholder="Filter history by Project, PI, Method..." onkeyup="filterHistoryTable()">
                </div>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="disbursementHistoryTable">
                <thead class="table-light">
                    <tr>
                        <th style="width: 110px;">Req &amp; Project</th>
                        <th>Research Proposal &amp; Category</th>
                        <th>Principal Investigator</th>
                        <th class="text-center">Tranche</th>
                        <th class="text-end">Disbursed Amount</th>
                        <th>Method</th>
                        <th>Date</th>
                        <th>Voucher / Ref</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($disbursedHistory as $hist)
                    <tr>
                        <td>
                            <div class="font-monospace fw-bold text-dark">#REQ-{{ $hist->request_id }}</div>
                            <span class="badge bg-secondary bg-opacity-10 text-secondary border mt-1" style="font-size: 0.72rem;">
                                PRJ #{{ $hist->project->project_id ?? $hist->project_id }}
                            </span>
                        </td>
                        <td>
                            <div class="fw-bold text-dark text-wrap" style="max-width: 320px;">
                                {{ $hist->project->title ?? 'N/A' }}
                            </div>
                            <div class="d-flex gap-1 flex-wrap mt-1">
                                <span class="badge bg-light text-dark border" style="font-size: 0.7rem;">
                                    {{ $hist->project->thematicArea->title ?? 'General' }}
                                </span>
                            </div>
                        </td>
                        <td>
                            <div class="fw-semibold text-dark">
                                {{ preg_replace('/\s*\([^)]*\)/', '', $hist->project->pi->name ?? 'N/A') }}
                            </div>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-light text-dark border px-2 py-1">{{ $hist->milestone_phase ?? 'Tranche' }}</span>
                        </td>
                        <td class="text-end">
                            <div class="fw-bold text-dark">{{ number_format($hist->approved_amount ?? 0, 2) }} ETB</div>
                        </td>
                        <td><span class="badge bg-info text-dark">{{ $hist->payment_method ?? 'N/A' }}</span></td>
                        <td>
                            <div class="small fw-semibold text-dark">{{ $hist->disbursed_at ? $hist->disbursed_at->format('M d, Y') : 'N/A' }}</div>
                            <small class="text-muted" style="font-size: 0.72rem;">{{ $hist->disbursed_at ? $hist->disbursed_at->format('h:i A') : '' }}</small>
                        </td>
                        <td>
                            @if($hist->notes)
                                <span class="badge bg-light text-dark border text-truncate" style="max-width: 140px;" title="{{ $hist->notes }}">
                                    <i class="bi bi-receipt me-1"></i>{{ $hist->notes }}
                                </span>
                            @else
                                <span class="text-muted small">N/A</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">
                            <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                            No disbursement history found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function filterPendingTable() {
    let input = document.getElementById("pendingSearchInput");
    let filter = input.value.toLowerCase();
    let table = document.getElementById("pendingDisbursementsTable");
    let trs = table.getElementsByTagName("tr");

    for (let i = 1; i < trs.length; i++) {
        let rowText = trs[i].innerText.toLowerCase();
        if (rowText.includes(filter)) {
            trs[i].style.display = "";
        } else {
            trs[i].style.display = "none";
        }
    }
}

function filterHistoryTable() {
    let input = document.getElementById("historySearchInput");
    let filter = input.value.toLowerCase();
    let table = document.getElementById("disbursementHistoryTable");
    let trs = table.getElementsByTagName("tr");

    for (let i = 1; i < trs.length; i++) {
        let rowText = trs[i].innerText.toLowerCase();
        if (rowText.includes(filter)) {
            trs[i].style.display = "";
        } else {
            trs[i].style.display = "none";
        }
    }
}
</script>
@endsection
