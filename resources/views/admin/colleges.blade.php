@extends('layouts.app')

@section('content')
<h3 class="fw-bold mb-4"><i class="bi bi-bank me-2 text-primary"></i>College Management</h3>
<div class="d-flex justify-content-between align-items-center mb-3">
    <span class="text-muted small">Manage academic colleges and view their departments</span>
    <button type="button" class="btn btn-primary fw-bold" data-bs-toggle="modal" data-bs-target="#createCollegeModal">
        <i class="bi bi-plus-circle me-1"></i> Create New College
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
                    <th>College Name</th>
                    <th>Departments</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($colleges as $c)
                <tr>
                    <td>#{{ $c->id }}</td>
                    <td><code>{{ $c->code }}</code></td>
                    <td class="fw-bold">{{ $c->name }}</td>
                    <td>
                        @foreach($c->departments as $dept)
                            <span class="badge bg-secondary me-1">{{ $dept->name }}</span>
                        @endforeach
                        @if($c->departments->isEmpty())
                            <span class="text-muted small">No departments</span>
                        @endif
                    </td>
                    <td>
                        <div class="btn-group btn-group-sm">
                            <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editCollegeModal{{ $c->id }}">
                                <i class="bi bi-pencil"></i> Edit
                            </button>
                            <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteCollegeModal{{ $c->id }}">
                                <i class="bi bi-trash"></i> Delete
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center text-muted py-4">
                        <i class="bi bi-bank fs-3 d-block mb-2"></i>
                        No colleges configured yet.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Create College Modal --}}
<div class="modal fade" id="createCollegeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.colleges.store') }}">
                @csrf
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold"><i class="bi bi-bank me-2"></i>Create New College</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">College Name</label>
                        <input type="text" name="name" class="form-control" required placeholder="e.g. College of Engineering & Technology">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">College Code</label>
                        <input type="text" name="code" class="form-control" required placeholder="e.g. CET" maxlength="20">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary fw-bold">Create College</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Edit/Delete Modals --}}
@foreach($colleges as $c)
<!-- Edit Modal -->
<div class="modal fade" id="editCollegeModal{{ $c->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.colleges.update', $c->id) }}">
                @csrf
                @method('PUT')
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold"><i class="bi bi-pencil me-2"></i>Edit College</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">College Name</label>
                        <input type="text" name="name" class="form-control" required value="{{ $c->name }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">College Code</label>
                        <input type="text" name="code" class="form-control" required value="{{ $c->code }}" maxlength="20">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary fw-bold">Update College</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteCollegeModal{{ $c->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.colleges.destroy', $c->id) }}">
                @csrf
                @method('DELETE')
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title fw-bold"><i class="bi bi-trash me-2"></i>Delete College</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete <strong>{{ $c->name }}</strong>?</p>
                    <p class="text-danger small">This action cannot be undone. All departments in this college will also be deleted.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger fw-bold">Delete College</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach
@endsection
