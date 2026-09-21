@extends('layouts.app')

@section('styles')
<style>
    #issueCertificateModal .form-select {
        appearance: none !important;
        -webkit-appearance: none !important;
        -moz-appearance: none !important;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%230f3e2e' d='M6 8L1 3h10z'/%3E%3C/svg%3E") !important;
        background-repeat: no-repeat !important;
        background-position: right 0.75rem center !important;
        background-size: 10px 10px !important;
        padding-right: 2.25rem !important;
        border: 1.5px solid #cbd5e1 !important;
        border-radius: 8px !important;
        font-size: 0.85rem !important;
        font-weight: 500 !important;
        color: #0f172a !important;
        background-color: #ffffff !important;
        transition: all 0.2s ease !important;
        cursor: pointer !important;
        height: auto !important;
    }
    #issueCertificateModal .form-select:hover {
        border-color: #94a3b8 !important;
        box-shadow: 0 1px 4px rgba(0,0,0,0.08) !important;
    }
    #issueCertificateModal .form-select:focus {
        border-color: #0f3e2e !important;
        box-shadow: 0 0 0 3px rgba(15,62,46,0.12) !important;
        outline: none !important;
    }
    #issueCertificateModal .form-select option {
        padding: 8px 12px !important;
        font-size: 0.85rem !important;
        background: #ffffff !important;
        color: #0f172a !important;
    }
    #issueCertificateModal .form-select option:checked {
        background: #0f3e2e !important;
        color: #ffffff !important;
    }
</style>
@endsection

@section('content')
<h3 class="fw-bold mb-4"><i class="bi bi-file-earmark-pdf me-2 text-success"></i>Completion Certificates & Journal Awards</h3>
<div class="d-flex justify-content-between align-items-center mb-3">
    <span class="text-muted small">
        @if(in_array(Auth::user()->role, ['vparttcs', 'rcsc']))
            Executive registry &amp; validation of official university research completion certificates
        @else
            Digital completion certificates and journal award letters issued to PIs
        @endif
    </span>
    @if(in_array(Auth::user()->role, ['coordinator', 'admin']))
    <button type="button" class="btn btn-success fw-bold" data-bs-toggle="modal" data-bs-target="#issueCertificateModal">
        <i class="bi bi-file-earmark-plus me-1"></i> Issue Certificate
    </button>
    @endif
</div>
<div class="card card-custom p-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Certificate Code</th>
                    <th>Project Title</th>
                    <th>Issued To</th>
                    <th>Type</th>
                    <th>Date Issued</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($certificates as $c)
                <tr>
                    <td><code>{{ $c->certificate_code }}</code></td>
                    <td class="fw-bold">{{ $c->project->title }}</td>
                    <td>{{ $c->issued_to_name }}</td>
                    <td><span class="badge bg-success">{{ $c->type }}</span></td>
                    <td>{{ $c->issued_at ? $c->issued_at->format('M d, Y H:i') : 'N/A' }}</td>
                    <td>
                        <div class="d-flex gap-1 align-items-center flex-wrap">
                            <a href="{{ route('certificates.view', $c->id) }}" target="_blank" class="btn btn-sm btn-outline-dark" title="View Certificate in browser">
                                <i class="bi bi-eye me-1"></i>View
                            </a>
                            <a href="{{ route('certificates.download', $c->id) }}" class="btn btn-sm btn-dark" title="Download PDF">
                                <i class="bi bi-download me-1"></i>Download PDF
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted py-4">
                        <i class="bi bi-file-earmark-check fs-1 d-block mb-2"></i>
                        No digital completion certificates issued yet.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="issueCertificateModal" tabindex="-1" aria-labelledby="issueCertificateModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <form method="POST" action="{{ route('certificates.store') }}">
                @csrf
                @method('POST')
                <div class="modal-header bg-success-subtle py-2">
                    <h5 class="modal-title fw-bold" id="issueCertificateModalLabel" style="white-space:normal;">
                        <i class="bi bi-file-earmark-pdf me-2 text-success"></i>Issue New Completion Certificate
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body py-2">
                    <div class="alert alert-info small py-2 mb-2" style="white-space:normal; word-wrap:break-word;">
                        <i class="bi bi-info-circle me-1"></i>
                        Only projects with <span class="badge bg-success">Completed</span> status can have certificates issued.
                    </div>

                    <div class="row g-2 mb-2">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small mb-1">Project <span class="text-danger">*</span></label>
                            <select name="project_id" class="form-select form-select-sm" required style="appearance:none;-webkit-appearance:none;-moz-appearance:none;background-image:url(&quot;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%230f3e2e' d='M6 8L1 3h10z'/%3E%3C/svg%3E&quot;);background-repeat:no-repeat;background-position:right 0.75rem center;background-size:10px 10px;padding-right:2.25rem;border:1.5px solid #cbd5e1;border-radius:8px;font-weight:500;color:#0f172a;background-color:#fff;cursor:pointer;">
                                <option value="">-- Select Completed Project --</option>
                                @foreach($completedProjects as $proj)
                                    <option value="{{ $proj->project_id }}">#{{ $proj->project_id }} — {{ $proj->title }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small mb-1">Certificate Type <span class="text-danger">*</span></label>
                            <select name="type" class="form-select form-select-sm" required style="appearance:none;-webkit-appearance:none;-moz-appearance:none;background-image:url(&quot;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%230f3e2e' d='M6 8L1 3h10z'/%3E%3C/svg%3E&quot;);background-repeat:no-repeat;background-position:right 0.75rem center;background-size:10px 10px;padding-right:2.25rem;border:1.5px solid #cbd5e1;border-radius:8px;font-weight:500;color:#0f172a;background-color:#fff;cursor:pointer;">
                                <option value="">-- Select Type --</option>
                                <option value="Completion">Completion Certificate</option>
                                <option value="Award">Journal Award Letter</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label fw-bold small mb-1">Issued To (Full Name) <span class="text-danger">*</span></label>
                        <input type="text" name="issued_to_name" class="form-control form-control-sm" required placeholder="e.g. Dr. Alemayehu T. Feyissa">
                        <div class="form-text small">Enter the name of the PI as it should appear on the certificate.</div>
                    </div>
                </div>
                <div class="modal-footer py-2">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success fw-bold px-3 btn-sm confirm-btn" data-confirm-title="Issue Certificate" data-confirm-message="Issue this certificate? This action cannot be undone." data-confirm-icon="bi-file-earmark-check" data-confirm-color="text-success" data-confirm-btn-text="Yes, Issue" data-confirm-btn-class="btn-success">
                        <i class="bi bi-file-earmark-check me-1"></i> Issue Certificate
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
