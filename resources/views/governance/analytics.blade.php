@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-0"><i class="bi bi-bar-chart-line me-2 text-dark"></i>Executive Analytics & MOE Reporting</h3>
        <span class="text-muted small">Real-time research portfolio intelligence dashboard</span>
    </div>
    <button onclick="alert('Exporting MOE Data Report (Excel/PDF)...')" class="btn btn-outline-dark fw-bold">
        <i class="bi bi-download me-1"></i> Export MOE Report
    </button>
</div>

{{-- KPI Summary Cards --}}
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card card-custom p-3 text-center border-start border-dark border-4">
            <div class="text-muted small text-uppercase fw-bold">Total Grants</div>
            <div class="fs-1 fw-bold text-dark">{{ $totalProjects }}</div>
            <div class="small text-muted">University-wide</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-custom p-3 text-center border-start border-dark border-4">
            <div class="text-muted small text-uppercase fw-bold">Active Research</div>
            <div class="fs-1 fw-bold text-dark">{{ $activeProjects }}</div>
            <div class="small text-muted">Currently in field</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-custom p-3 text-center border-start border-dark border-4">
            <div class="text-muted small text-uppercase fw-bold">Completed</div>
            <div class="fs-1 fw-bold text-dark">{{ $completedProjects }}</div>
            <div class="small text-muted">Successfully closed</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-custom p-3 text-center border-start border-dark border-4">
            <div class="text-muted small text-uppercase fw-bold">Under Review</div>
            <div class="fs-1 fw-bold text-dark">{{ $underReviewProjects }}</div>
            <div class="small text-muted">Peer review stage</div>
        </div>
    </div>
</div>

{{-- Charts Row --}}
<div class="row g-4 mb-4">
    {{-- Pie Chart: Project Status Distribution --}}
    <div class="col-md-5">
        <div class="card card-custom p-4">
            <h6 class="fw-bold mb-3"><i class="bi bi-pie-chart-fill me-2 text-dark"></i>Project Status Distribution</h6>
            <canvas id="statusPieChart" height="260"></canvas>
        </div>
    </div>

    {{-- Bar Chart: Budget by Thematic Area --}}
    <div class="col-md-7">
        <div class="card card-custom p-4">
            <h6 class="fw-bold mb-3"><i class="bi bi-bar-chart-fill me-2 text-dark"></i>Requested Budget by Thematic Area (ETB)</h6>
            <canvas id="budgetBarChart" height="260"></canvas>
        </div>
    </div>
</div>

{{-- Budget Summary Table --}}
<div class="card card-custom p-4 mb-4">
    <h6 class="fw-bold mb-3"><i class="bi bi-table me-2 text-dark"></i>Financial Overview by Thematic Area</h6>
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Thematic Area</th>
                    <th>Projects</th>
                    <th>Total Requested (ETB)</th>
                    <th>Total Approved (ETB)</th>
                    <th>Governance Tier</th>
                </tr>
            </thead>
            <tbody>
                @forelse($budgetByThematic as $row)
                <tr>
                    <td class="fw-bold">{{ $row->thematic_title }}</td>
                    <td><span class="badge bg-dark">{{ $row->project_count }}</span></td>
                    <td class="text-dark fw-bold">{{ number_format($row->total_requested, 2) }}</td>
                    <td class="text-dark fw-bold">{{ number_format($row->total_approved ?? 0, 2) }}</td>
                    <td>
                        @if($row->total_requested >= 500000)
                            <span class="badge bg-dark">RCSC / VP Tier</span>
                        @else
                            <span class="badge bg-secondary">College Dean Tier</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center text-muted py-4"><i class="bi bi-inbox fs-3 d-block mb-2"></i>No submitted research proposals recorded for the active call yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Chart.js (local offline) + Script --}}
<script src="{{ asset('vendor/chart.js/chart.umd.min.js') }}"></script>
<script>
    const statusLabels = @json($statusLabels);
    const statusData   = @json($statusData);
    const statusColors = [
        '#212529','#343a40','#495057','#6c757d',
        '#adb5bd','#212529','#343a40','#495057','#6c757d','#adb5bd'
    ];

    new Chart(document.getElementById('statusPieChart'), {
        type: 'doughnut',
        data: {
            labels: statusLabels,
            datasets: [{
                data: statusData,
                backgroundColor: statusColors.slice(0, statusLabels.length),
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'bottom', labels: { font: { size: 11 } } }
            }
        }
    });

    const thematicLabels  = @json($thematicLabels);
    const thematicBudgets = @json($thematicBudgets);

    new Chart(document.getElementById('budgetBarChart'), {
        type: 'bar',
        data: {
            labels: thematicLabels,
            datasets: [{
                label: 'Total Requested Budget (ETB)',
                data: thematicBudgets,
                backgroundColor: 'rgba(33, 37, 41, 0.7)',
                borderColor: '#212529',
                borderWidth: 1,
                borderRadius: 5
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: val => 'ETB ' + Number(val).toLocaleString()
                    }
                },
                x: { ticks: { font: { size: 10 } } }
            }
        }
    });
</script>
@endsection
