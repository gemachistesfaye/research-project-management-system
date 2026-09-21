@extends('layouts.app')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
    <div>
        <h3 class="fw-bold mb-0"><i class="bi bi-arrow-left-right me-2 text-dark"></i> PI Transfer</h3>
        <span class="text-muted">Transfer Principal Investigator role to another staff member</span>
    </div>
    <div class="w-100 w-md-auto d-flex justify-content-start justify-content-md-end">
        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Back to Dashboard
        </a>
    </div>
</div>

<div class="row g-4">
    {{-- New Transfer Request Form --}}
    <div class="col-lg-5">
        <div class="card card-custom">
            <div class="card-header bg-warning text-dark fw-bold">
                <i class="bi bi-person-add me-2"></i> Initiate PI Transfer
            </div>
            <div class="card-body">
                @if($myProjects->isEmpty())
                    <div class="text-center text-muted py-3">
                        <i class="bi bi-exclamation-circle fs-3 d-block mb-2"></i>
                        You have no active projects eligible for PI transfer.
                    </div>
                @else
                    <form action="{{ route('pitransfer.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label fw-bold small">Select Project <span class="text-danger">*</span></label>
                            <select name="project_id" class="form-select form-select-sm @error('project_id') is-invalid @enderror" required>
                                <option value="">-- Select Project --</option>
                                @foreach($myProjects as $p)
                                    <option value="{{ $p->project_id }}" {{ old('project_id') == $p->project_id ? 'selected' : '' }}>
                                        {{ Str::limit($p->title, 45) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('project_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small">New PI (Staff Member) <span class="text-danger">*</span></label>
                            <select name="new_pi_id" class="form-select form-select-sm @error('new_pi_id') is-invalid @enderror" required>
                                <option value="">-- Select New PI --</option>
                                @foreach($eligibleUsers as $u)
                                    <option value="{{ $u->id }}" {{ old('new_pi_id') == $u->id ? 'selected' : '' }}>
                                        {{ $u->name }} ({{ strtoupper($u->role) }})
                                    </option>
                                @endforeach
                            </select>
                            @error('new_pi_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small">Reason for Transfer <span class="text-danger">*</span></label>
                            <textarea name="reason" rows="4" class="form-control form-control-sm @error('reason') is-invalid @enderror"
                                      required placeholder="Provide justification for the PI transfer...">{{ old('reason') }}</textarea>
                            @error('reason')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small">
                                <i class="bi bi-file-earmark me-1"></i> Supporting Document
                                <span class="text-muted fw-normal">(Optional)</span>
                            </label>
                            <input type="file" name="consent_document" accept=".pdf,.doc,.docx"
                                   class="form-control form-control-sm @error('consent_document') is-invalid @enderror">
                            <div class="form-text">PDF or Word document, max 5MB</div>
                            @error('consent_document')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <button type="button" class="btn btn-warning w-100 fw-bold confirm-btn" data-confirm-title="Submit Transfer" data-confirm-message="Submit PI transfer request for approval?" data-confirm-icon="bi-arrow-left-right" data-confirm-color="text-warning" data-confirm-btn-text="Yes, Submit" data-confirm-btn-class="btn-warning">
                            <i class="bi bi-send me-1"></i> Submit Transfer Request
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    {{-- Pending Transfers (Coordinator/VP Approval) --}}
    <div class="col-lg-7">
        <div class="card card-custom">
            <div class="card-header bg-white border-bottom fw-bold d-flex justify-content-between align-items-center">
                <span><i class="bi bi-hourglass-split me-2 text-warning"></i> Pending Transfer Requests</span>
                <span class="badge bg-warning text-dark">{{ $pendingTransfers->count() }}</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Project</th>
                                <th>Current PI</th>
                                <th>New PI</th>
                                <th>Reason</th>
                                <th>Status</th>
                                @if(in_array(Auth::user()->role, ['coordinator', 'vparttcs']))
                                <th>Action</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pendingTransfers as $t)
                            <tr>
                                <td>#TR-{{ $t->id }}</td>
                                <td class="fw-bold">{{ Str::limit($t->project->title ?? 'N/A', 30) }}
                                    @if($t->project && $t->project->proposal_document_url)
                                    <a href="{{ Storage::url($t->project->proposal_document_url) }}" target="_blank" class="text-decoration-none ms-1" title="View Proposal Document">
                                        <i class="bi bi-file-pdf text-danger small"></i>
                                    </a>
                                    @endif
                                </td>
                                <td>{{ $t->oldPi->name ?? 'N/A' }}</td>
                                <td>{{ $t->newPi->name ?? 'N/A' }}</td>
                                <td>{{ Str::limit($t->reason, 30) }}</td>
                                <td><span class="badge bg-warning text-dark">{{ $t->status }}</span></td>
                                @if(in_array(Auth::user()->role, ['coordinator', 'vparttcs']))
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <form action="{{ route('pitransfer.approve', $t->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="button" class="btn btn-success btn-sm confirm-btn" title="Approve" data-confirm-title="Approve Transfer" data-confirm-message="Approve this PI transfer?" data-confirm-icon="bi-check-circle" data-confirm-color="text-success" data-confirm-btn-text="Yes, Approve" data-confirm-btn-class="btn-success">
                                                <i class="bi bi-check-lg"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('pitransfer.reject', $t->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="button" class="btn btn-danger btn-sm confirm-btn" title="Reject" data-confirm-title="Reject Transfer" data-confirm-message="Reject this PI transfer?" data-confirm-icon="bi-x-circle" data-confirm-color="text-danger" data-confirm-btn-text="Yes, Reject" data-confirm-btn-class="btn-danger">
                                                <i class="bi bi-x-lg"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                                @endif
                            </tr>
                            @empty
                            <tr>
                                <td colspan="{{ in_array(Auth::user()->role, ['coordinator', 'vparttcs']) ? 7 : 6 }}" class="text-center text-muted py-4">
                                    <i class="bi bi-check-circle fs-3 d-block mb-2 text-success"></i>
                                    No pending PI transfer requests.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Transfer History --}}
        <div class="card card-custom mt-4">
            <div class="card-header bg-white border-bottom fw-bold">
                <i class="bi bi-clock-history me-2 text-primary"></i> Transfer History
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Project</th>
                                <th>From PI</th>
                                <th>To PI</th>
                                <th>Status</th>
                                <th>Processed</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($completedTransfers as $ct)
                            <tr>
                                <td>#TR-{{ $ct->id }}</td>
                                <td class="fw-bold">{{ Str::limit($ct->project->title ?? 'N/A', 30) }}
                                    @if($ct->project && $ct->project->proposal_document_url)
                                    <a href="{{ Storage::url($ct->project->proposal_document_url) }}" target="_blank" class="text-decoration-none ms-1" title="View Proposal Document">
                                        <i class="bi bi-file-pdf text-danger small"></i>
                                    </a>
                                    @endif
                                </td>
                                <td>{{ $ct->oldPi->name ?? 'N/A' }}</td>
                                <td>{{ $ct->newPi->name ?? 'N/A' }}</td>
                                <td>
                                    @if($ct->status === 'Approved')
                                        <span class="badge bg-success">{{ $ct->status }}</span>
                                    @else
                                        <span class="badge bg-danger">{{ $ct->status }}</span>
                                    @endif
                                </td>
                                <td>{{ $ct->updated_at->format('M d, Y') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                                    No completed transfers found.
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
@endsection
