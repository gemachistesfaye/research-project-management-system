<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>RPMS - Coordinator Operational Report</title>
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'Helvetica Neue', Arial, sans-serif; font-size: 10px; color: #1e293b; padding: 25px; }
    .header { border-bottom: 2px solid #0f3e2e; padding-bottom: 10px; margin-bottom: 12px; text-align: center; }
    .header h1 { color: #0f3e2e; font-size: 16px; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 3px; }
    .header h3 { color: #475569; font-size: 11px; font-weight: normal; margin-bottom: 4px; }
    .doc-title { font-size: 13px; font-weight: bold; color: #047857; text-transform: uppercase; letter-spacing: 0.5px; }

    .meta-box { width: 100%; border: 1px solid #e2e8f0; background: #f8fafc; margin-bottom: 15px; border-collapse: collapse; font-size: 9px; }
    .meta-box td { padding: 5px 8px; }

    .kpi-cards { width: 100%; border-collapse: separate; border-spacing: 6px 0; margin-bottom: 15px; }
    .kpi-card { width: 25%; background: #f1f5f9; border: 1px solid #cbd5e1; border-radius: 4px; padding: 8px; text-align: center; }
    .kpi-label { font-size: 8px; text-transform: uppercase; color: #64748b; font-weight: bold; }
    .kpi-value { font-size: 14px; font-weight: bold; color: #0f3e2e; margin-top: 2px; }

    .section-title { font-size: 11px; font-weight: bold; color: #0f3e2e; background: #f1f5f9; padding: 5px 8px; margin: 12px 0 6px 0; border-left: 3px solid #0f3e2e; text-transform: uppercase; }

    table.report-table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
    table.report-table th, table.report-table td { border: 1px solid #cbd5e1; padding: 5px 6px; font-size: 8.5px; text-align: left; }
    table.report-table th { background: #0f3e2e; color: #fff; font-weight: bold; text-transform: uppercase; font-size: 8px; }
    table.report-table tr:nth-child(even) { background: #f8fafc; }

    .signatures { width: 100%; margin-top: 35px; border-collapse: collapse; }
    .signatures td { width: 50%; text-align: center; padding: 0 20px; vertical-align: top; }
    .sig-line { border-top: 1px solid #475569; padding-top: 4px; font-weight: bold; font-size: 9px; color: #1e293b; margin-top: 40px; }

    .footer { margin-top: 20px; border-top: 1px dashed #cbd5e1; padding-top: 6px; font-size: 8px; color: #64748b; text-align: center; }
</style>
</head>
<body>

<div class="header">
    <h1>Directorate of Research &amp; Community Service</h1>
    <h3>Research Project Management System (RPMS) &bull; College Operations Hub</h3>
    <div class="doc-title">{{ ucfirst($period) }} Operational &amp; Research Portfolio Performance Report</div>
</div>

<table class="meta-box">
    <tr>
        <td style="width: 25%;"><strong>Reference:</strong> {{ $refNumber }}</td>
        <td style="width: 25%;"><strong>Reporting Interval:</strong> {{ ucfirst($period) }} (Since {{ $startDate->format('M d, Y') }})</td>
        <td style="width: 25%;"><strong>Generated:</strong> {{ $generatedAt }}</td>
        <td style="width: 25%; text-align: right;"><strong>Coordinator:</strong> {{ $generatedBy }}</td>
    </tr>
</table>

<table class="kpi-cards">
    <tr>
        <td class="kpi-card">
            <div class="kpi-label">Active Portfolio</div>
            <div class="kpi-value">{{ $activeCount }}</div>
        </td>
        <td class="kpi-card">
            <div class="kpi-label">Completed Projects</div>
            <div class="kpi-value">{{ $completedCount }}</div>
        </td>
        <td class="kpi-card">
            <div class="kpi-label">Milestone Reports</div>
            <div class="kpi-value">{{ $milestonesCount }}</div>
        </td>
        <td class="kpi-card">
            <div class="kpi-label">Disbursed (ETB)</div>
            <div class="kpi-value">{{ number_format($totalDisbursed, 0) }}</div>
        </td>
    </tr>
</table>

<div class="section-title">1. Portfolio Overview &amp; Project Status Breakdown</div>
<table class="report-table">
    <thead>
        <tr>
            <th>Status Category</th>
            <th>Project Count</th>
            <th>Total Budget Allocation (ETB)</th>
            <th>Operational Health &amp; Next Stage</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td><strong>Active Projects</strong></td>
            <td>{{ $activeCount }}</td>
            <td>{{ number_format($activeBudget, 2) }}</td>
            <td>Ongoing research execution &amp; milestone submissions</td>
        </tr>
        <tr>
            <td><strong>Under Review / Ratification</strong></td>
            <td>{{ $reviewCount }}</td>
            <td>{{ number_format($reviewBudget, 2) }}</td>
            <td>Pending peer reviewer scoring or RCSC / Dean review</td>
        </tr>
        <tr>
            <td><strong>Completed / Closed</strong></td>
            <td>{{ $completedCount }}</td>
            <td>{{ number_format($completedBudget, 2) }}</td>
            <td>Deliverables verified &amp; final reports archived</td>
        </tr>
        <tr>
            <td><strong>Delayed / At Risk</strong></td>
            <td>{{ $delayedCount }}</td>
            <td>{{ number_format($delayedBudget, 2) }}</td>
            <td>Overdue milestones or pending challenges</td>
        </tr>
    </tbody>
</table>

<div class="section-title">2. Research Projects Detail Listing</div>
<table class="report-table">
    <thead>
        <tr>
            <th style="width: 8%;">ID</th>
            <th style="width: 32%;">Project Title</th>
            <th style="width: 20%;">PI &amp; Department</th>
            <th style="width: 15%;">Budget (ETB)</th>
            <th style="width: 12%;">Status</th>
            <th style="width: 13%;">Presentation Schedule</th>
        </tr>
    </thead>
    <tbody>
        @forelse($projects as $p)
        <tr>
            <td>#{{ $p->project_id }}</td>
            <td><strong>{{ $p->title }}</strong></td>
            <td>{{ $p->pi->name ?? 'N/A' }} ({{ $p->department->name ?? 'N/A' }})</td>
            <td>{{ number_format($p->approved_budget ?: $p->requested_budget, 2) }}</td>
            <td>{{ $p->status }}</td>
            <td>
                @php $nextSched = $p->presentationSchedules->where('status', 'Scheduled')->first(); @endphp
                {{ $nextSched ? $nextSched->type . ' (' . $nextSched->scheduled_at->format('M d') . ')' : 'None' }}
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="6" style="text-align: center; color: #64748b; padding: 10px;">No projects recorded for this reporting scope.</td>
        </tr>
        @endforelse
    </tbody>
</table>

<div class="section-title">3. Challenges &amp; Technical Bottlenecks Reported</div>
<table class="report-table">
    <thead>
        <tr>
            <th style="width: 10%;">Project</th>
            <th style="width: 15%;">Type</th>
            <th style="width: 45%;">Challenge Description</th>
            <th style="width: 15%;">Status</th>
            <th style="width: 15%;">Reported Date</th>
        </tr>
    </thead>
    <tbody>
        @forelse($challenges as $c)
        <tr>
            <td>#{{ $c->project_id }}</td>
            <td><strong>{{ $c->challenge_type }}</strong></td>
            <td>{{ Str::limit($c->description, 100) }}</td>
            <td>{{ $c->status }}</td>
            <td>{{ $c->created_at->format('M d, Y') }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="5" style="text-align: center; color: #64748b; padding: 8px;">No critical challenges recorded during this operational cycle.</td>
        </tr>
        @endforelse
    </tbody>
</table>

<table class="signatures">
    <tr>
        <td>
            <div class="sig-line">
                {{ $generatedBy }}<br>
                Research &amp; Community Service Coordinator
            </div>
        </td>
        <td>
            <div class="sig-line">
                Directorate of Research &amp; Technology Transfer<br>
                Research Project Management System
            </div>
        </td>
    </tr>
</table>

<div class="footer">
    Institutional Research Office &bull; RPMS Automated Operational Governance Archive &bull; Generated {{ $generatedAt }} &bull; Page 1 of 1
</div>

</body>
</html>
