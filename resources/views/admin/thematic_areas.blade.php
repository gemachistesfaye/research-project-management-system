@extends('layouts.app')

@section('content')
<div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2 mb-4">
    <div>
        <h3 class="fw-bold mb-0"><i class="bi bi-diagram-3 me-2 text-dark"></i>Thematic Priority Area Configuration</h3>
        <span class="text-muted small">Manage institutional thematic priority areas for research proposals</span>
    </div>
    <div>
        <button type="button" class="btn btn-dark fw-bold text-nowrap" data-bs-toggle="modal" data-bs-target="#createThematicModal">
            <i class="bi bi-plus-circle me-1"></i> Create Thematic Area
        </button>
    </div>
</div>
<div class="card card-custom p-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Thematic Priority Title</th>
                    <th>Category</th>
                    <th>Description</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($thematics as $t)
                <tr>
                    <td>#{{ $t->id }}</td>
                    <td class="fw-bold">{{ $t->title }}</td>
                    <td><span class="badge bg-secondary">{{ $t->category }}</span></td>
                    <td>{{ \Illuminate\Support\Str::limit($t->description ?? '', 60) }}</td>
                    <td><span class="badge {{ $t->is_active ? 'bg-success' : 'bg-secondary' }}">{{ $t->is_active ? 'Active' : 'Inactive' }}</span></td>
                    <td>
                        <div class="btn-group btn-group-sm">
                            <button type="button" class="btn btn-outline-dark" data-bs-toggle="modal" data-bs-target="#editThematicModal{{ $t->id }}">
                                <i class="bi bi-pencil"></i> Edit
                            </button>
                            <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteThematicModal{{ $t->id }}">
                                <i class="bi bi-trash"></i> Delete
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted py-4">
                        <i class="bi bi-diagram-3 fs-3 d-block mb-2"></i>
                        No thematic priority areas configured yet.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Create Modal -->
<div class="modal fade" id="createThematicModal" tabindex="-1" aria-labelledby="createThematicModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.thematic-areas.store') }}">
                @csrf
                @method('POST')
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title fw-bold" id="createThematicModalLabel">
                        <i class="bi bi-plus-circle me-2"></i>Create New Thematic Area
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" required placeholder="e.g. Climate-Resilient Agriculture">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Category <span class="text-danger">*</span></label>
                        <select name="category" class="form-select" required>
                            <option value="">-- Select Category --</option>
                            <option value="Natural Sciences">Natural Sciences</option>
                            <option value="Social Sciences">Social Sciences</option>
                            <option value="Engineering & Technology">Engineering & Technology</option>
                            <option value="Health Sciences">Health Sciences</option>
                            <option value="Humanities & Arts">Humanities & Arts</option>
                            <option value="Education">Education</option>
                            <option value="Agriculture">Agriculture</option>
                            <option value="Business & Economics">Business & Economics</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Description</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Brief description of this thematic priority area..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-dark fw-bold px-4 confirm-btn" data-confirm-title="Create Thematic Area" data-confirm-message="Create this thematic area?" data-confirm-icon="bi-plus-circle" data-confirm-color="text-dark" data-confirm-btn-text="Yes, Create" data-confirm-btn-class="btn-dark">
                        <i class="bi bi-check-lg me-1"></i> Create Thematic Area
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit & Delete Modals -->
@foreach($thematics as $t)
<!-- Edit Modal -->
<div class="modal fade" id="editThematicModal{{ $t->id }}" tabindex="-1" aria-labelledby="editThematicModalLabel{{ $t->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.thematic-areas.update', $t->id) }}">
                @csrf
                @method('PUT')
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title fw-bold" id="editThematicModalLabel{{ $t->id }}">
                        <i class="bi bi-pencil me-2"></i>Edit Thematic Area — {{ $t->title }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" value="{{ $t->title }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Category <span class="text-danger">*</span></label>
                        <select name="category" class="form-select" required>
                            <option value="Natural Sciences" {{ $t->category === 'Natural Sciences' ? 'selected' : '' }}>Natural Sciences</option>
                            <option value="Social Sciences" {{ $t->category === 'Social Sciences' ? 'selected' : '' }}>Social Sciences</option>
                            <option value="Engineering & Technology" {{ $t->category === 'Engineering & Technology' ? 'selected' : '' }}>Engineering & Technology</option>
                            <option value="Health Sciences" {{ $t->category === 'Health Sciences' ? 'selected' : '' }}>Health Sciences</option>
                            <option value="Humanities & Arts" {{ $t->category === 'Humanities & Arts' ? 'selected' : '' }}>Humanities & Arts</option>
                            <option value="Education" {{ $t->category === 'Education' ? 'selected' : '' }}>Education</option>
                            <option value="Agriculture" {{ $t->category === 'Agriculture' ? 'selected' : '' }}>Agriculture</option>
                            <option value="Business & Economics" {{ $t->category === 'Business & Economics' ? 'selected' : '' }}>Business & Economics</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Description</label>
                        <textarea name="description" class="form-control" rows="3">{{ $t->description }}</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-dark fw-bold px-4 confirm-btn" data-confirm-title="Save Changes" data-confirm-message="Save changes to this thematic area?" data-confirm-icon="bi-check-circle" data-confirm-color="text-dark" data-confirm-btn-text="Yes, Save" data-confirm-btn-class="btn-dark">
                        <i class="bi bi-check-lg me-1"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteThematicModal{{ $t->id }}" tabindex="-1" aria-labelledby="deleteThematicModalLabel{{ $t->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.thematic-areas.destroy', $t->id) }}">
                @csrf
                @method('DELETE')
                <div class="modal-header bg-danger-subtle">
                    <h5 class="modal-title fw-bold" id="deleteThematicModalLabel{{ $t->id }}">
                        <i class="bi bi-exclamation-triangle me-2 text-danger"></i>Delete Thematic Area
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-danger small py-2">
                        <i class="bi bi-exclamation-triangle-fill me-1"></i>
                        This action cannot be undone.
                    </div>
                    <p>Are you sure you want to permanently delete <strong>{{ $t->title }}</strong> ({{ $t->category }})?</p>
                    <p class="small text-muted mb-0">Any proposals linked to this thematic area will retain their existing associations.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger fw-bold px-4 confirm-btn" data-confirm-title="Delete Thematic Area" data-confirm-message="Permanently delete this thematic area? This action cannot be undone." data-confirm-icon="bi-trash" data-confirm-color="text-danger" data-confirm-btn-text="Yes, Delete" data-confirm-btn-class="btn-danger">
                        <i class="bi bi-trash me-1"></i> Delete Thematic Area
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach
@endsection
