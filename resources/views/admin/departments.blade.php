@extends('layouts.app')

@section('content')
<h3 class="fw-bold mb-4"><i class="bi bi-building me-2 text-primary"></i>Department Management</h3>
<div class="d-flex justify-content-between align-items-center mb-3">
    <span class="text-muted small">Manage academic departments and their college assignments</span>
    <button type="button" class="btn btn-primary fw-bold" data-bs-toggle="modal" data-bs-target="#createDeptModal">
        <i class="bi bi-plus-circle me-1"></i> Create New Department
    </button>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-1"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="card card-custom p-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Code</th>
                    <th>Department Name</th>
                    <th>College</th>
                    <th>Staff Count</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($departments as $d)
                <tr>
                    <td>#{{ $d->id }}</td>
                    <td><code>{{ $d->code }}</code></td>
                    <td class="fw-bold">{{ $d->name }}</td>
                    <td><span class="badge bg-primary">{{ $d->college->name ?? 'N/A' }}</span></td>
                    <td><span class="badge bg-secondary">{{ $d->users->count() ?? 0 }} staff</span></td>
                    <td>
                        <div class="btn-group btn-group-sm">
                            <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editDeptModal{{ $d->id }}">
                                <i class="bi bi-pencil"></i> Edit
                            </button>
                            <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteDeptModal{{ $d->id }}">
                                <i class="bi bi-trash"></i> Delete
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted py-4">
                        <i class="bi bi-building fs-3 d-block mb-2"></i>
                        No departments configured yet.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Create Department Modal --}}
<div class="modal fade" id="createDeptModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.departments.store') }}">
                @csrf
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold"><i class="bi bi-building me-2"></i>Create New Department</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Department Name</label>
                        <input type="text" name="name" class="form-control" required placeholder="e.g. Computer Science & IT">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Department Code</label>
                        <input type="text" name="code" class="form-control" required placeholder="e.g. CS" maxlength="20">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">College</label>
                        <select name="college_id" class="form-select" required>
                            <option value="">-- Select College --</option>
                            @foreach($colleges as $c)
                                <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->code }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary fw-bold confirm-btn" data-confirm-title="Create Department" data-confirm-message="Create this new department?" data-confirm-icon="bi-building" data-confirm-color="text-primary" data-confirm-btn-text="Yes, Create" data-confirm-btn-class="btn-primary">Create Department</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Edit/Delete Modals --}}
@foreach($departments as $d)
<!-- Edit Modal -->
<div class="modal fade" id="editDeptModal{{ $d->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.departments.update', $d->id) }}">
                @csrf
                @method('PUT')
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold"><i class="bi bi-pencil me-2"></i>Edit Department</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Department Name</label>
                        <input type="text" name="name" class="form-control" required value="{{ $d->name }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Department Code</label>
                        <input type="text" name="code" class="form-control" required value="{{ $d->code }}" maxlength="20">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">College</label>
                        <select name="college_id" class="form-select" required>
                            @foreach($colleges as $c)
                                <option value="{{ $c->id }}" {{ $d->college_id == $c->id ? 'selected' : '' }}>{{ $c->name }} ({{ $c->code }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary fw-bold confirm-btn" data-confirm-title="Update Department" data-confirm-message="Save changes to this department?" data-confirm-icon="bi-check-circle" data-confirm-color="text-primary" data-confirm-btn-text="Yes, Save" data-confirm-btn-class="btn-primary">Update Department</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteDeptModal{{ $d->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.departments.destroy', $d->id) }}">
                @csrf
                @method('DELETE')
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title fw-bold"><i class="bi bi-trash me-2"></i>Delete Department</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete <strong>{{ $d->name }}</strong>?</p>
                    <p class="text-danger small">This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger fw-bold confirm-btn" data-confirm-title="Delete Department" data-confirm-message="Permanently delete this department? This action cannot be undone." data-confirm-icon="bi-trash" data-confirm-color="text-danger" data-confirm-btn-text="Yes, Delete" data-confirm-btn-class="btn-danger">Delete Department</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach
@endsection
