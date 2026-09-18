@extends('layouts.app')

@section('content')
<h3 class="fw-bold mb-4"><i class="bi bi-clipboard-check me-2 text-primary"></i>My Review Assignments</h3>

<h5 class="fw-bold mb-3"><i class="bi bi-hourglass-split me-2 text-warning"></i>Pending Reviews ({{ $pending->count() }})</h5>
<div class="card card-custom p-4 mb-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Eval ID</th>
                    <th>Project ID</th>
                    <th>Proposal Title</th>
                    <th>Assigned Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pending as $eval)
                <tr>
                    <td>#{{ $eval->eval_id }}</td>
                    <td>#{{ $eval->project_id }}</td>
                    <td class="fw-bold">{{ $eval->project->title ?? 'N/A' }}</td>
                    <td>{{ $eval->created_at ? $eval->created_at->format('M d, Y') : 'N/A' }}</td>
                    <td>
                        <a href="{{ route('evaluations.show', $eval->eval_id) }}" class="btn btn-sm btn-primary">Open Evaluation</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center text-muted py-4">
                        <i class="bi bi-check2-circle fs-3 d-block text-secondary mb-2"></i>
                        No pending review assignments.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<h5 class="fw-bold mb-3"><i class="bi bi-check-circle me-2 text-success"></i>Completed Reviews ({{ $completed->count() }})</h5>
<div class="card card-custom p-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Eval ID</th>
                    <th>Project ID</th>
                    <th>Proposal Title</th>
                    <th>Score</th>
                    <th>Decision</th>
                    <th>Evaluated At</th>
                </tr>
            </thead>
            <tbody>
                @forelse($completed as $eval)
                <tr>
                    <td>#{{ $eval->eval_id }}</td>
                    <td>#{{ $eval->project_id }}</td>
                    <td>{{ $eval->project->title ?? 'N/A' }}</td>
                    <td><span class="badge bg-primary">{{ $eval->score }}</span></td>
                    <td><span class="badge bg-secondary">{{ $eval->decision }}</span></td>
                    <td>{{ $eval->evaluated_at ? $eval->evaluated_at->format('M d, Y H:i') : 'N/A' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted py-4">
                        <i class="bi bi-clipboard-data fs-3 d-block text-secondary mb-2"></i>
                        No completed reviews yet.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
