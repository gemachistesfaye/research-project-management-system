@extends('layouts.app')

@section('content')
<h3 class="fw-bold mb-4"><i class="bi bi-file-earmark-pdf me-2 text-success"></i>Completion Certificates & Journal Awards</h3>
<div class="d-flex justify-content-between align-items-center mb-3">
    <span class="text-muted small">Digital completion certificates and journal award letters issued to PIs</span>
    <button type="button" class="btn btn-success fw-bold" data-bs-toggle="modal" data-bs-target="#issueCertificateModal">
        <i class="bi bi-file-earmark-plus me-1"></i> Issue Certificate
    </button>
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
                            <select name="project_id" class="form-select form-select-sm" required>
                                <option value="">-- Select Completed Project --</option>
                                @foreach($completedProjects as $proj)
                                    <option value="{{ $proj->project_id }}">#{{ $proj->project_id }} — {{ $proj->title }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small mb-1">Certificate Type <span class="text-danger">*</span></label>
                            <select name="type" class="form-select form-select-sm" required>
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
