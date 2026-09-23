@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
    <div>
        <h3 class="fw-bold mb-0"><i class="bi bi-cash-stack me-2 text-dark"></i> Finance &amp; Grant Disbursement</h3>
        <span class="text-muted small">Process approved budget requests and track System releases</span>
    </div>
    <div>
        <a href="{{ route('finance.export.csv') }}" class="btn btn-outline-dark btn-sm fw-bold shadow-sm">
            <i class="bi bi-file-earmark-spreadsheet me-1 text-success"></i> Export Audit (CSV)
        </a>
    </div>
</div>

{{-- Total Disbursed Summary & Compact Tranche Guide --}}
@php
    $totalApproved = $pendingRequests->sum('approved_amount') + $disbursedHistory->sum('approved_amount');
    $totalDisbursed = $disbursedHistory->sum('approved_amount');
    $disbursementPercentage = $totalApproved > 0 ? round(($totalDisbursed / $totalApproved) * 100, 1) : 0;
@endphp

<div class="row g-2 g-md-3 mb-4">
    <div class="col-4 col-md-4">
        <div class="card card-custom p-2 p-md-3 border-0 shadow-sm h-100">
            <div class="text-muted small text-uppercase fw-bold text-truncate d-none d-md-block">Total Disbursed</div>
            <div class="text-muted fw-bold text-truncate d-md-none" style="font-size: 0.7rem;">DISBURSED</div>
            
            <div class="fs-2 fw-bold text-dark my-1 text-nowrap d-none d-md-block">
                {{ number_format($totalDisbursed, 2) }} <span class="fs-6 text-muted fw-normal">ETB</span>
            </div>
            <div class="fw-bold text-dark my-1 text-nowrap d-md-none" style="font-size: 0.95rem;">
                {{ number_format($totalDisbursed, 0) }} <span class="fw-normal text-muted" style="font-size: 0.65rem;">ETB</span>
            </div>

            <div class="small text-muted d-none d-md-block">Of {{ number_format($totalApproved, 2) }} ETB approved</div>
            <div class="text-muted text-truncate d-md-none" style="font-size: 0.65rem;">Of {{ number_format($totalApproved, 0) }} ETB</div>
        </div>
    </div>
    <div class="col-4 col-md-4">
        <div class="card card-custom p-2 p-md-3 border-0 shadow-sm h-100">
            <div class="text-muted small text-uppercase fw-bold text-truncate d-none d-md-block">Pending Requests</div>
            <div class="text-muted fw-bold text-truncate d-md-none" style="font-size: 0.7rem;">PENDING</div>

            <div class="fs-2 fw-bold text-dark my-1 d-none d-md-block">
                {{ $pendingRequests->count() }}
            </div>
            <div class="fw-bold text-dark my-1 d-md-none" style="font-size: 0.95rem;">
                {{ $pendingRequests->count() }}
            </div>

            <div class="small text-muted d-none d-md-block">Awaiting fund transfer</div>
            <div class="text-muted text-truncate d-md-none" style="font-size: 0.65rem;">Awaiting transfer</div>
        </div>
    </div>
    <div class="col-4 col-md-4">
        <div class="card card-custom p-2 p-md-3 border-0 shadow-sm h-100">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <span class="text-muted small text-uppercase fw-bold text-truncate d-none d-md-block">Fund Release Progress</span>
                <span class="text-muted fw-bold text-truncate d-md-none" style="font-size: 0.7rem;">PROGRESS</span>
                <span class="badge bg-dark rounded-pill">{{ $disbursementPercentage }}%</span>
            </div>
            <div class="progress my-2" style="height: 8px; border-radius: 4px;">
                <div class="progress-bar bg-dark" style="width: {{ $disbursementPercentage }}%;"></div>
            </div>
            <div class="d-flex justify-content-between text-muted small d-none d-md-flex">
                <span>0 ETB</span>
                <span>{{ number_format($totalApproved, 2) }} ETB</span>
            </div>
            <div class="d-flex justify-content-between text-muted d-md-none" style="font-size: 0.65rem;">
                <span>0</span>
                <span>{{ number_format($totalApproved, 0) }}</span>
            </div>
        </div>
    </div>
</div>

{{-- Compact Horizontal Tranche Guide Bar (Hidden on Mobile) --}}
<div class="card card-custom p-2 mb-4 bg-light border-0 d-none d-md-block">
    <div class="d-flex justify-content-between align-items-center flex-wrap px-2 py-1 gap-2">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-info-circle text-dark"></i>
            <span class="small text-dark fw-bold">Standard Tranche Structure:</span>
        </div>
        <div class="d-flex gap-2 align-items-center flex-wrap" style="font-size: 0.8rem;">
            <span class="badge bg-white text-dark border"><strong class="text-dark">Tranche 1:</strong> 30% Advance (On VP Sign)</span>
            <span class="badge bg-white text-dark border"><strong class="text-dark">Tranche 2:</strong> 40% Mid-Term (&ge;40% Milestone)</span>
            <span class="badge bg-white text-dark border"><strong class="text-dark">Tranche 3:</strong> 30% Final (100% Completion)</span>
        </div>
    </div>
</div>

{{-- Pending Disbursements --}}
<div class="card card-custom mb-4">
    <div class="card-header bg-white border-bottom py-3">
        <div class="row g-2 align-items-center justify-content-between">
            <div class="col-12 col-md-6">
                <h5 class="fw-bold mb-0 text-dark">
                    <i class="bi bi-hourglass-split me-2 text-warning"></i> Pending Disbursements
                    <span class="badge bg-dark rounded-pill ms-1">{{ $pendingRequests->count() }}</span>
                </h5>
                <small class="text-muted">Approved tranches awaiting fund release</small>
            </div>
            <div class="col-12 col-md-5">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" id="pendingSearchInput" class="form-control bg-light border-start-0"
                           placeholder="Search pending by Project, PI..." onkeyup="filterPendingTable()">
                </div>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="pendingDisbursementsTable">
                <thead class="table-light">
                    <tr>
                        <th style="width: 90px;">Req #</th>
                        <th>Project Proposal</th>
                        <th>Principal Investigator</th>
                        <th class="text-center">Tranche</th>
                        <th class="text-end">Amount</th>
                        <th class="text-center" style="width: 110px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pendingRequests as $req)
                    <tr>
                        <td>
                            <span class="font-monospace fw-bold text-dark">#{{ $req->request_id }}</span>
                        </td>
                        <td>
                            <div class="fw-bold text-dark text-truncate" style="max-width: 320px;" title="{{ $req->project->title ?? 'N/A' }}">
                                {{ $req->project->title ?? 'N/A' }}
                            </div>
                            <small class="text-muted">{{ $req->project->thematicArea->title ?? 'General' }}</small>
                        </td>
                        <td>
                            <div class="fw-semibold text-dark">
                                {{ preg_replace('/\s*\([^)]*\)/', '', $req->project->pi->name ?? 'N/A') }}
                            </div>
                            <small class="text-muted" style="font-size: 0.72rem;">{{ $req->project->department->name ?? '' }}</small>
                        </td>
                        <td class="text-center">
                            @if($req->milestone_phase === 'Tranche 1')
                                <span class="badge bg-dark text-white px-2 py-1"><i class="bi bi-1-circle me-1"></i>Tranche 1 (30%)</span>
                            @elseif($req->milestone_phase === 'Tranche 2')
                                <span class="badge bg-dark text-white px-2 py-1"><i class="bi bi-2-circle me-1"></i>Tranche 2 (40%)</span>
                            @elseif($req->milestone_phase === 'Tranche 3')
                                <span class="badge bg-dark text-white px-2 py-1"><i class="bi bi-3-circle me-1"></i>Tranche 3 (30%)</span>
                            @else
                                <span class="badge bg-secondary text-white px-2 py-1">{{ $req->milestone_phase }}</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <span class="fw-bold text-dark">{{ number_format($req->approved_amount ?? 0, 2) }} ETB</span>
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
                                                   placeholder="e.g. CBE Ref #UNI-2026-9812 / Voucher #4421" value="{{ old('notes') }}">
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
            <div class="col-12 col-md-6">
                <h5 class="fw-bold mb-0 text-dark">
                    <i class="bi bi-clock-history me-2 text-dark"></i> Disbursement History
                    <span class="badge bg-secondary rounded-pill ms-1">{{ $disbursedHistory->count() }}</span>
                </h5>
                <small class="text-muted">Audit trail of completed releases</small>
            </div>
            <div class="col-12 col-md-5">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" id="historySearchInput" class="form-control bg-light border-start-0"
                           placeholder="Search history by Project, PI, Method..." onkeyup="filterHistoryTable()">
                </div>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="disbursementHistoryTable">
                <thead class="table-light">
                    <tr>
                        <th style="width: 90px;">Req #</th>
                        <th>Project Proposal</th>
                        <th>Principal Investigator</th>
                        <th class="text-center">Tranche</th>
                        <th class="text-end">Amount</th>
                        <th>Method</th>
                        <th>Date</th>
                        <th>Reference</th>
                        <th class="text-center" style="width: 130px;">Voucher</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($disbursedHistory as $hist)
                    <tr>
                        <td>
                            <span class="font-monospace fw-bold text-dark">#{{ $hist->request_id }}</span>
                        </td>
                        <td>
                            <div class="fw-bold text-dark text-truncate" style="max-width: 300px;" title="{{ $hist->project->title ?? 'N/A' }}">
                                {{ $hist->project->title ?? 'N/A' }}
                            </div>
                            <small class="text-muted">{{ $hist->project->thematicArea->title ?? 'General' }}</small>
                        </td>
                        <td>
                            <div class="fw-semibold text-dark">
                                {{ preg_replace('/\s*\([^)]*\)/', '', $hist->project->pi->name ?? 'N/A') }}
                            </div>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-light text-dark border">{{ $hist->milestone_phase ?? 'Tranche' }}</span>
                        </td>
                        <td class="text-end">
                            <span class="fw-bold text-dark">{{ number_format($hist->approved_amount ?? 0, 2) }} ETB</span>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border">{{ $hist->payment_method ?? 'N/A' }}</span>
                        </td>
                        <td>
                            <span class="small text-dark">{{ $hist->disbursed_at ? $hist->disbursed_at->format('M d, Y') : 'N/A' }}</span>
                        </td>
                        <td>
                            <span class="small text-muted text-truncate d-inline-block" style="max-width: 140px;" title="{{ $hist->notes ?? 'N/A' }}">
                                {{ $hist->notes ?? 'N/A' }}
                            </span>
                        </td>
                        <td class="text-center">
                            <div class="d-flex gap-1 justify-content-center align-items-center">
                                <a href="{{ route('finance.voucher.view', $hist->request_id) }}" target="_blank" class="btn btn-xs btn-outline-dark py-1 px-2" title="View Official Payment Voucher in Browser">
                                    <i class="bi bi-eye me-1"></i>View
                                </a>
                                <a href="{{ route('finance.voucher', $hist->request_id) }}" class="btn btn-xs btn-dark py-1 px-2" title="Download Official Payment Voucher (PDF)">
                                    <i class="bi bi-download me-1"></i>PDF
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center text-muted py-4">
                            <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                            No disbursement history recorded yet.
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
