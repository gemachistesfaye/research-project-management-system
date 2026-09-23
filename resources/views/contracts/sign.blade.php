@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-0"><i class="bi bi-pen me-2 text-dark"></i> Contract Signing</h3>
        <span class="text-muted">Sign the research contract to activate the project</span>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('contracts.download', $project->project_id) }}" class="btn btn-outline-primary btn-sm" title="Download Contract">
            <i class="bi bi-download me-1"></i> Download Contract
        </a>
        <a href="{{ route('projects.show', $project->project_id) }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Back to Project
        </a>
    </div>
</div>

<div class="row g-4">
    {{-- Project Details Card --}}
    <div class="col-lg-6">
        <div class="card card-custom">
            <div class="card-header bg-white border-bottom fw-bold">
                <i class="bi bi-folder2-open me-2 text-primary"></i> Project Details
            </div>
            <div class="card-body">
                <table class="table table-borderless mb-0">
                    <tr>
                        <td class="text-muted fw-bold" style="width:40%">Project ID</td>
                        <td>#{{ $project->project_id }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted fw-bold">Title</td>
                        <td>{{ $project->title }}
                            @if($project->proposal_document_url)
                            <a href="{{ Storage::url($project->proposal_document_url) }}" target="_blank" class="text-decoration-none ms-2" title="View Proposal Document">
                                <i class="bi bi-file-pdf text-danger small"></i>
                            </a>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted fw-bold">Status</td>
                        <td>
                            @if($project->status === 'Approved' || $project->status === 'Completed')
                                <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>{{ $project->status }}</span>
                            @elseif($project->status === 'Active')
                                <span class="badge bg-primary"><i class="bi bi-play-circle me-1"></i>{{ $project->status }}</span>
                            @elseif(in_array($project->status, ['Submitted', 'DH_Screened', 'UnderReview']))
                                <span class="badge bg-warning text-dark"><i class="bi bi-hourglass-split me-1"></i>{{ $project->status }}</span>
                            @elseif($project->status === 'Rejected' || $project->status === 'Terminated')
                                <span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i>{{ $project->status }}</span>
                            @else
                                <span class="badge bg-secondary">{{ $project->status }}</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted fw-bold">Thematic Area</td>
                        <td>{{ $project->thematicArea->title ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted fw-bold">Requested Budget</td>
                        <td class="fw-bold text-success">{{ number_format($project->requested_budget, 2) }} ETB</td>
                    </tr>
                    <tr>
                        <td class="text-muted fw-bold">Approved Budget</td>
                        <td class="fw-bold text-primary">{{ $project->approved_budget ? number_format($project->approved_budget, 2) . ' ETB' : 'Pending' }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    {{-- PI Information Card --}}
    <div class="col-lg-6">
        <div class="card card-custom">
            <div class="card-header bg-white border-bottom fw-bold">
                <i class="bi bi-person-badge me-2 text-info"></i> Principal Investigator
            </div>
            <div class="card-body">
                <table class="table table-borderless mb-0">
                    <tr>
                        <td class="text-muted fw-bold" style="width:40%">Name</td>
                        <td>{{ $project->pi->name ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted fw-bold">Staff ID</td>
                        <td>{{ $project->pi->staff_id ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted fw-bold">Email</td>
                        <td>{{ $project->pi->email ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted fw-bold">Department</td>
                        <td>{{ $project->pi->department->name ?? 'N/A' }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Contract Text Section --}}
<div class="card card-custom mt-4">
    <div class="card-header bg-white border-bottom fw-bold">
        <i class="bi bi-file-earmark-text me-2 text-secondary"></i> Research Contract
        @if($project->contract_signed_at)
            <span class="badge bg-success float-end"><i class="bi bi-check-circle me-1"></i> Signed</span>
        @else
            <span class="badge bg-warning text-dark float-end"><i class="bi bi-exclamation-triangle me-1"></i> Pending Signature</span>
        @endif
    </div>
    <div class="card-body">
        <div class="border rounded p-3 mb-4" style="max-height: 400px; overflow-y: auto; background-color: #fafbfc;">
            <div class="text-center mb-4">
                <h5 class="fw-bold">Research Project Management System</h5>
                <h6 class="text-muted">Office of the Vice President for ARTTCS</h6>
                <hr class="my-3">
                <h6 class="fw-bold">System RESEARCH GRANT AGREEMENT</h6>
                <p class="text-muted small">Contract Reference: SCR-{{ $project->project_id }}</p>
            </div>

            <div class="mb-4">
                <h6 class="fw-bold text-primary"><i class="bi bi-bullseye me-2"></i>1. Research Objectives</h6>
                <p>
                    This Research Grant Agreement ("Agreement") is entered into for the research project titled 
                    <strong>"{{ $project->title }}"</strong>. The primary objectives of this research are to advance 
                    scientific knowledge in the thematic area of <strong>{{ $project->thematicArea->title ?? 'N/A' }}</strong>, 
                    develop innovative solutions, and contribute to the academic and technological advancement of the Research Project Management System. 
                    The Principal Investigator shall conduct the research in accordance with the approved research proposal 
                    and adhere to the highest standards of academic integrity and ethical research practices.
                </p>
            </div>

            <div class="mb-4">
                <h6 class="fw-bold text-primary"><i class="bi bi-cash-stack me-2"></i>2. Budget Allocation</h6>
                <p>
                    The total budget for this research project is <strong>{{ number_format($project->requested_budget, 2) }} ETB</strong> 
                    ({{ $project->approved_budget ? 'approved: ' . number_format($project->approved_budget, 2) . ' ETB' : 'pending approval' }}). 
                    The funds shall be allocated in accordance with the Research Project Management System's financial regulations and the approved 
                    budget breakdown. The Principal Investigator is responsible for the prudent management of funds and must 
                    submit financial reports as required. Any budget modifications exceeding 10% of the approved amount 
                    must receive prior written approval from the Research Directorate.
                </p>
            </div>

            <div class="mb-4">
                <h6 class="fw-bold text-primary"><i class="bi bi-journal-text me-2"></i>3. Reporting Requirements</h6>
                <p>
                    The Principal Investigator shall submit progress reports on a quarterly basis and a final comprehensive 
                    report upon completion of the research. All reports must comply with the Research Project Management System's research reporting 
                    format and guidelines. The final report must include:
                </p>
                <ul>
                    <li>Executive summary of research findings</li>
                    <li>Detailed methodology and results</li>
                    <li>Publications and conference presentations arising from the research</li>
                    <li>Financial accounting and expenditure report</li>
                    <li>Recommendations for future research directions</li>
                </ul>
            </div>

            <div class="mb-4">
                <h6 class="fw-bold text-primary"><i class="bi bi-shield-lock me-2"></i>4. Intellectual Property</h6>
                <p>
                    All intellectual property rights arising from this research, including but not limited to inventions, 
                    discoveries, software, publications, and other scholarly works, shall be jointly owned by the 
                    Principal Investigator and Research Project Management System, in accordance with the 
                    Research Project Management System's Intellectual Property Policy. Neither party shall commercialize or license any 
                    intellectual property without the prior written consent of the other party. The Research Project Management System reserves 
                    the right to use research findings for academic and non-commercial purposes.
                </p>
            </div>

            <div class="mb-4">
                <h6 class="fw-bold text-primary"><i class="bi bi-x-octagon me-2"></i>5. Termination Conditions</h6>
                <p>
                    This Agreement may be terminated under the following circumstances:
                </p>
                <ul>
                    <li>Mutual written consent of both parties</li>
                    <li>Breach of any material term of this Agreement, subject to a 30-day cure period</li>
                    <li>Failure to submit required reports for two consecutive reporting periods</li>
                    <li>Misuse of funds or violation of research ethics</li>
                    <li>Conviction of the Principal Investigator for academic misconduct or fraud</li>
                </ul>
                <p>
                    Upon termination, the Principal Investigator shall return any unspent funds and submit a final report 
                    detailing all research activities conducted up to the date of termination.
                </p>
            </div>

            <div class="mb-4">
                <h6 class="fw-bold text-primary"><i class="bi bi-building me-2"></i>6. General Provisions</h6>
                <p>
                    This Agreement shall be governed by the laws of the Federal Democratic Republic of Ethiopia and 
                    the regulations of Research Project Management System. Any disputes arising from this 
                    Agreement shall be resolved through the Research Project Management System's conflict resolution mechanisms. The parties 
                    agree to act in good faith and cooperate fully to achieve the objectives of this research project.
                </p>
            </div>

            <div class="text-center border-top pt-3 mt-4">
                <p class="text-muted small mb-0">
                    <i class="bi bi-info-circle me-1"></i>
                    By signing below, you acknowledge that you have read, understood, and agree to all terms and conditions of this Research Collaboration Agreement.
                </p>
            </div>
        </div>
    </div>
</div>

{{-- Signature Section --}}
<div class="card card-custom mt-4">
    <div class="card-header bg-white border-bottom fw-bold">
        <i class="bi bi-signature me-2 text-warning"></i> Contract Signatures
    </div>
    <div class="card-body">
        @if($project->contract_signed_at)
            <div class="alert alert-success mb-0">
                <i class="bi bi-check-circle-fill me-2"></i>
                <strong>Contract Fully Signed</strong> &mdash; Signed on {{ $project->contract_signed_at->format('M d, Y \a\t h:i A') }}
            </div>
        @elseif($project->status !== 'Approved')
            <div class="alert alert-warning border-start border-4 border-warning mb-4">
                <div class="d-flex align-items-center">
                    <i class="bi bi-bank fs-3 text-warning me-3"></i>
                    <div>
                        <h6 class="fw-bold mb-1">Financial Budget Ratification Pending</h6>
                        <p class="small text-muted mb-0">
                            This project is currently in <strong>{{ $project->status === 'Dean_Review' ? 'Dean Review (<500k ETB)' : ($project->status === 'RCSC_Review' ? 'RCSC / VP Review (>=500k ETB)' : $project->status) }}</strong>. Contract signing is strictly locked until the financial budget is officially ratified and approved by the {{ $project->requested_budget >= 500000 ? 'RCSC / VP' : 'College Dean' }}.
                        </p>
                    </div>
                </div>
            </div>
        @elseif($project->irercClearance && $project->irercClearance->status !== 'Approved')
            <div class="alert alert-warning border-start border-4 border-warning mb-4">
                <div class="d-flex align-items-center">
                    <i class="bi bi-shield-exclamation fs-3 text-warning me-3"></i>
                    <div>
                        <h6 class="fw-bold mb-1">Ethics Clearance Pending (IRERC)</h6>
                        <p class="small text-muted mb-0">
                            This project was routed for System Research Ethics Review. The contract cannot be signed or activated until the IRERC panel grants formal approval.
                        </p>
                    </div>
                </div>
            </div>
        @else
            {{-- Agreement Checkbox --}}
            <div class="mb-4 p-3 bg-light rounded">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="agreeTerms" style="cursor: pointer;">
                    <label class="form-check-label fw-bold" for="agreeTerms" style="cursor: pointer;">
                        I have read and agree to the terms of this Research Collaboration Agreement
                    </label>
                </div>
            </div>

            <div class="row g-4">
                {{-- PI Signature --}}
                <div class="col-md-6">
                    <div class="border border-primary rounded p-4 h-100">
                        <div class="d-flex align-items-center mb-3">
                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-2" style="width: 35px; height: 35px;">
                                <i class="bi bi-person"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-0">PI Signature</h6>
                                <small class="text-muted">Principal Investigator</small>
                            </div>
                        </div>
                        @if($pi_signed)
                            <div class="text-center py-3">
                                <i class="bi bi-check-circle-fill text-success fs-1 d-block mb-2"></i>
                                <p class="text-success fw-bold mb-1">Signed by {{ $project->pi->name }}</p>
                                <small class="text-muted">Date: {{ $pi_signed_date->format('M d, Y \a\t h:i A') }}</small>
                            </div>
                        @elseif(in_array(Auth::user()->role, ['pi', 'admin']) && (int)Auth::id() === (int)$project->pi_id || Auth::user()->role === 'admin')
                            <form method="POST" action="{{ route('contracts.sign-pi', $project->project_id) }}">
                                @csrf
                                <p class="text-muted small mb-3">
                                    By clicking "Sign as PI", you confirm that you have reviewed and agree to the terms of this research contract.
                                </p>
                                @error('pi_signature')<div class="alert alert-danger small py-1">{{ $message }}</div>@enderror
                                <button type="submit" id="piSignBtn" class="btn btn-primary w-100 fw-bold confirm-btn" disabled data-confirm-title="Sign Contract" data-confirm-message="Sign contract as Principal Investigator? This confirms you agree to all terms." data-confirm-icon="bi-pen" data-confirm-color="text-primary" data-confirm-btn-text="Yes, Sign" data-confirm-btn-class="btn-primary">
                                    <i class="bi bi-pen me-1"></i> Sign as PI
                                </button>
                            </form>
                        @else
                            <div class="p-3 bg-light rounded text-center text-muted">
                                <i class="bi bi-lock me-1"></i>
                                Awaiting Principal Investigator signature (<strong>{{ $project->pi->name }}</strong>).
                            </div>
                        @endif
                    </div>
                </div>

                {{-- VP Signature --}}
                <div class="col-md-6">
                    <div class="border border-danger rounded p-4 h-100">
                        <div class="d-flex align-items-center mb-3">
                            <div class="rounded-circle bg-danger text-white d-flex align-items-center justify-content-center me-2" style="width: 35px; height: 35px;">
                                <i class="bi bi-person-badge"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-0">VP for ARTTCS Signature</h6>
                                <small class="text-muted">Vice President</small>
                            </div>
                        </div>
                        @if($vp_signed)
                            <div class="text-center py-3">
                                <i class="bi bi-check-circle-fill text-success fs-1 d-block mb-2"></i>
                                <p class="text-success fw-bold mb-1">Signed by VP</p>
                                <small class="text-muted">Date: {{ $vp_signed_date->format('M d, Y \a\t h:i A') }}</small>
                            </div>
                        @elseif(!$pi_signed)
                            <div class="p-3 bg-light rounded text-center text-muted">
                                <i class="bi bi-hourglass-split me-1"></i>
                                <span>PI must sign first before VP authorization.</span>
                            </div>
                        @elseif(in_array(Auth::user()->role, ['vparttcs', 'admin']))
                            <form method="POST" action="{{ route('contracts.sign-vp', $project->project_id) }}">
                                @csrf
                                <p class="text-muted small mb-3">
                                    By clicking "Sign as VP", you authorize the activation of this research project and approve the allocated budget.
                                </p>
                                @error('vp_signature')<div class="alert alert-danger small py-1">{{ $message }}</div>@enderror
                                <button type="submit" id="vpSignBtn" class="btn btn-danger w-100 fw-bold confirm-btn" disabled data-confirm-title="Sign Contract" data-confirm-message="Sign contract as VP for ARTTCS? This authorizes project activation and budget allocation." data-confirm-icon="bi-pen" data-confirm-color="text-danger" data-confirm-btn-text="Yes, Sign" data-confirm-btn-class="btn-danger">
                                    <i class="bi bi-pen me-1"></i> Sign as VP
                                </button>
                            </form>
                        @else
                            <div class="p-3 bg-light rounded text-center text-muted">
                                <i class="bi bi-lock me-1"></i>
                                Awaiting Vice President authorization signature.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const agreeCheckbox = document.getElementById('agreeTerms');
        const piSignBtn = document.getElementById('piSignBtn');
        const vpSignBtn = document.getElementById('vpSignBtn');

        function toggleButtons() {
            const isChecked = agreeCheckbox ? agreeCheckbox.checked : false;
            if (piSignBtn) {
                piSignBtn.disabled = !isChecked;
            }
            if (vpSignBtn) {
                vpSignBtn.disabled = !isChecked;
            }
        }

        if (agreeCheckbox) {
            agreeCheckbox.addEventListener('change', toggleButtons);
            // Run on load in case browser restored checkbox state
            toggleButtons();
        }
    });
</script>
@endpush
@endsection
