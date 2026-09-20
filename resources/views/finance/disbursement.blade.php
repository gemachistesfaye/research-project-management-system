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
    <div class="card-header bg-white border-bottom fw-bold d-flex justify-content-between align-items-center">
        <span><i class="bi bi-hourglass-split me-2 text-dark"></i> Pending Disbursements ({{ $pendingRequests->count() }})</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Request ID</th>
                        <th>Project Name</th>
                        <th>PI</th>
                        <th>Tranche Phase</th>
                        <th>Approved Amount</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pendingRequests as $req)
                    <tr>
                        <td>#REQ-{{ $req->request_id }}</td>
                        <td class="fw-bold">{{ $req->project->title ?? 'N/A' }}</td>
                        <td>{{ $req->project->pi->name ?? 'N/A' }}</td>
                        <td>
                            @if($req->milestone_phase === 'Tranche 1')
                                <span class="badge bg-dark text-white px-2 py-1"><i class="bi bi-1-circle me-1"></i>Tranche 1 (30%)</span>
                            @elseif($req->milestone_phase === 'Tranche 2')
                                <span class="badge bg-primary text-white px-2 py-1"><i class="bi bi-2-circle me-1"></i>Tranche 2 (40%)</span>
                            @elseif($req->milestone_phase === 'Tranche 3')
                                <span class="badge bg-info text-dark px-2 py-1"><i class="bi bi-3-circle me-1"></i>Tranche 3 (30%)</span>
                            @else
                                <span class="badge bg-secondary text-white px-2 py-1">{{ $req->milestone_phase }}</span>
                            @endif
                        </td>
                        <td class="fw-bold text-dark">{{ number_format($req->approved_amount ?? 0, 2) }} ETB</td>
                        <td><span class="badge bg-dark">{{ $req->status }}</span></td>
                        <td>
                            <button class="btn btn-sm btn-success fw-bold" data-bs-toggle="modal"
                                    data-bs-target="#disburseModal{{ $req->request_id }}">
                                <i class="bi bi-cash me-1"></i> Disburse
                            </button>
                        </td>
                    </tr>

                    {{-- Disbursement Modal --}}
                    <div class="modal fade" id="disburseModal{{ $req->request_id }}" tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered" style="max-width: 460px;">
                            <div class="modal-content border-0 shadow">
                                <form action="{{ route('finance.process-disbursement', $req->request_id) }}" method="POST">
                                    @csrf
                                    <div class="modal-header py-2 px-3 bg-dark text-white border-bottom">
                                        <h6 class="modal-title fw-bold text-white small mb-0">
                                            <i class="bi bi-cash me-1 text-success"></i> Release Disbursement &bull; {{ $req->milestone_phase ?? 'Tranche 1' }}
                                        </h6>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body p-3">
                                        {{-- Project Summary Snippet --}}
                                        <div class="p-2 mb-2 bg-light rounded border small">
                                            <div class="fw-bold text-dark text-truncate">{{ $req->project->title ?? 'N/A' }}</div>
                                            <div class="d-flex justify-content-between text-muted mt-1" style="font-size: 0.78rem;">
                                                <span><i class="bi bi-person me-1"></i>{{ $req->project->pi->name ?? 'N/A' }}</span>
                                                <span class="fw-bold text-dark">{{ number_format($req->approved_amount ?? 0, 2) }} ETB</span>
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
                                                <label class="form-label small fw-bold mb-1">Method <span class="text-danger">*</span></label>
                                                <select name="payment_method" class="form-select form-select-sm @error('payment_method') is-invalid @enderror" required>
                                                    <option value="Bank Transfer" selected>Bank Transfer</option>
                                                    <option value="Check">Check</option>
                                                    <option value="Cash">Cash</option>
                                                </select>
                                                @error('payment_method')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            </div>
                                        </div>

                                        <div class="mb-2">
                                            <label class="form-label small fw-bold mb-1">Reference / Note</label>
                                            <input type="text" name="notes" class="form-control form-control-sm @error('notes') is-invalid @enderror"
                                                   placeholder="e.g. CBE Ref #GMU-2026-9812" value="{{ old('notes') }}">
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
                        <td colspan="7" class="text-center text-muted py-4">
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
    <div class="card-header bg-white border-bottom fw-bold">
        <i class="bi bi-clock-history me-2 text-dark"></i> Disbursement History ({{ $disbursedHistory->count() }})
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Request ID</th>
                        <th>Project</th>
                        <th>PI</th>
                        <th>Approved Amount Released</th>
                        <th>Payment Method</th>
                        <th>Date</th>
                        <th>Notes</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($disbursedHistory as $hist)
                    <tr>
                        <td>#REQ-{{ $hist->request_id }}</td>
                        <td class="fw-bold">{{ $hist->project->title ?? 'N/A' }}</td>
                        <td>{{ $hist->project->pi->name ?? 'N/A' }}</td>
                        <td class="fw-bold text-dark">{{ number_format($hist->approved_amount ?? 0, 2) }} ETB</td>
                        <td><span class="badge bg-info text-dark">{{ $hist->payment_method ?? 'N/A' }}</span></td>
                        <td>{{ $hist->disbursed_at ? $hist->disbursed_at->format('M d, Y') : 'N/A' }}</td>
                        <td>{{ $hist->notes ?: 'N/A' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
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
@endsection
