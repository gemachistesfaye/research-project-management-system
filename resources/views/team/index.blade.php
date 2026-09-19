@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-0"><i class="bi bi-people me-2 text-info"></i> Team Members (SCR-23)</h3>
        <span class="text-muted">{{ $project->title }}</span>
        @if($project->proposal_document_url)
        <a href="{{ Storage::url($project->proposal_document_url) }}" target="_blank" class="btn btn-sm btn-outline-danger ms-2" title="View Proposal Document">
            <i class="bi bi-file-pdf me-1"></i>Proposal
        </a>
        @endif
    </div>
    <a href="{{ route('projects.show', $project->project_id) }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Back to Project
    </a>
</div>

<div class="row g-4">
    {{-- Add New Member --}}
    <div class="col-lg-4">
        <div class="card card-custom">
            <div class="card-header bg-success text-white fw-bold">
                <i class="bi bi-person-plus me-2"></i> Add Team Member
            </div>
            <div class="card-body">
                @if($availableUsers->isEmpty())
                    <p class="text-muted small">No available users to add.</p>
                @else
                    <form action="{{ route('team.store', $project->project_id) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-bold small">Select User</label>
                            <select name="user_id" class="form-select form-select-sm @error('user_id') is-invalid @enderror" required>
                                <option value="">-- Choose --</option>
                                @foreach($availableUsers as $u)
                                    <option value="{{ $u->id }}">{{ $u->name }} ({{ strtoupper($u->role) }})</option>
                                @endforeach
                            </select>
                            @error('user_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small">Role in Project</label>
                            <select name="role_in_project" class="form-select form-select-sm" required>
                                <option value="Co-Investigator">Co-Investigator — Senior researcher sharing leadership</option>
                                <option value="Research Assistant">Research Assistant — Junior researcher supporting data collection</option>
                                <option value="Technical Specialist">Technical Specialist — Expert providing technical guidance</option>
                                <option value="Data Analyst">Data Analyst — Specialist analyzing research data</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small">Contribution (%)</label>
                            <input type="number" name="contribution_percentage" class="form-control form-control-sm"
                                   min="0" max="100" value="0" required>
                        </div>

                        <button type="submit" class="btn btn-success btn-sm w-100">
                            <i class="bi bi-plus-lg me-1"></i> Add Member
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    {{-- Current Members --}}
    <div class="col-lg-8">
        <div class="card card-custom">
            <div class="card-header bg-white border-bottom fw-bold">
                <i class="bi bi-person-badge me-2 text-info"></i> Current Team
                <span class="badge bg-info rounded-pill ms-2">{{ $project->members->count() }}</span>
            </div>
            <div class="card-body p-0">
                {{-- PI always listed first --}}
                <div class="border-bottom p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="badge bg-primary me-2">PI</span>
                            <strong>{{ $project->pi->name ?? 'N/A' }}</strong>
                        </div>
                        <span class="badge bg-success">100% Lead</span>
                    </div>
                    <small class="text-muted">Principal Investigator &mdash; Permanent staff member</small>
                </div>

                @forelse($project->members as $member)
                <div class="border-bottom p-3 {{ $loop->last ? 'border-0' : '' }}">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <span class="badge bg-secondary me-2">{{ $member->role_in_project }}</span>
                            <strong>{{ $member->user->name ?? 'User #' . $member->user_id }}</strong>
                            <br>
                            <small class="text-muted">
                                {{ $member->user->email ?? '' }}
                            </small>
                            <div class="mt-1" style="max-width: 200px;">
                                <small class="text-muted d-block mb-1">Contribution: {{ $member->contribution_percentage }}%</small>
                                <div class="progress" style="height: 6px;">
                                    <div class="progress-bar bg-info" style="width: {{ $member->contribution_percentage }}%"></div>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex gap-1">
                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                                    data-bs-target="#editMember{{ $member->id }}">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal"
                                    data-bs-target="#confirmRemove{{ $member->id }}">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>

                    {{-- Edit Modal --}}
                    <div class="modal fade" id="editMember{{ $member->id }}" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form action="{{ route('team.update', $member->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-header">
                                        <h6 class="modal-title fw-bold">Edit Team Member Role</h6>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Role in Project</label>
                                            <select name="role_in_project" class="form-select" required>
                                                @foreach([
                                                    'Co-Investigator' => 'Senior researcher sharing leadership',
                                                    'Research Assistant' => 'Junior researcher supporting data collection',
                                                    'Technical Specialist' => 'Expert providing technical guidance',
                                                    'Data Analyst' => 'Specialist analyzing research data'
                                                ] as $role => $desc)
                                                    <option value="{{ $role }}" {{ $member->role_in_project === $role ? 'selected' : '' }}>{{ $role }} — {{ $desc }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Contribution (%)</label>
                                            <input type="number" name="contribution_percentage" class="form-control"
                                                   min="0" max="100" value="{{ $member->contribution_percentage }}" required>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                                        <button type="button" class="btn btn-primary confirm-btn" data-confirm-title="Save Changes" data-confirm-message="Save changes to this team member?" data-confirm-icon="bi-check-circle" data-confirm-color="text-primary" data-confirm-btn-text="Yes, Save" data-confirm-btn-class="btn-primary">Save Changes</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    {{-- Remove Confirmation Modal --}}
                    <div class="modal fade" id="confirmRemove{{ $member->id }}" tabindex="-1">
                        <div class="modal-dialog modal-sm modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header border-0 pb-0">
                                    <h6 class="modal-title fw-bold text-danger"><i class="bi bi-exclamation-triangle me-1"></i> Remove Member</h6>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body text-center">
                                    <p class="mb-1">Are you sure you want to remove</p>
                                    <strong>{{ $member->user->name ?? 'this member' }}</strong>
                                    <p class="text-muted small mb-0">from this project?</p>
                                </div>
                                <div class="modal-footer border-0 justify-content-center">
                                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <form action="{{ route('team.destroy', $member->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger confirm-btn" data-confirm-title="Remove Team Member" data-confirm-message="Remove this team member from the project?" data-confirm-icon="bi-person-dash" data-confirm-color="text-danger" data-confirm-btn-text="Yes, Remove" data-confirm-btn-class="btn-danger">Remove</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center py-4 text-muted">
                    <i class="bi bi-people fs-3 d-block mb-2"></i>
                    No team members added yet.
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
