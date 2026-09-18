@extends('layouts.app')

@section('content')
<h3 class="fw-bold mb-4"><i class="bi bi-hdd-network me-2 text-info"></i>HRMS & External Integration Monitor</h3>
<div class="card card-custom p-4">
    <div class="row g-3">
        <div class="col-md-6">
            <div class="p-3 border rounded bg-light">
                <div class="fw-bold text-success"><i class="bi bi-check-circle-fill me-1"></i>GMU HRMS Staff API Connection</div>
                <div class="small text-muted mt-1">Status: Active (TLS 1.3 encrypted REST bridge)</div>
                <div class="small text-muted">Last sync: {{ now()->format('M d, Y H:i') }}</div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="p-3 border rounded bg-light">
                <div class="fw-bold text-success"><i class="bi bi-check-circle-fill me-1"></i>GMU Procurement Gateway</div>
                <div class="small text-muted mt-1">Status: Active</div>
                <div class="small text-muted">Last sync: {{ now()->format('M d, Y H:i') }}</div>
            </div>
        </div>
    </div>
</div>
@endsection
