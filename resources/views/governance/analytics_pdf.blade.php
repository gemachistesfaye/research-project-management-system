<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Research Project Management System - System Research Portfolio Report</title>
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'Helvetica Neue', Arial, sans-serif; font-size: 11px; color: #212529; padding: 25px; }
    .header { border-bottom: 2px solid #0f3e2e; padding-bottom: 12px; margin-bottom: 18px; }
    .header h2 { color: #0f3e2e; font-size: 18px; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 2px; }
    .header h4 { color: #475569; font-size: 12px; font-weight: normal; margin-bottom: 6px; }
    .meta-table { width: 100%; margin-bottom: 15px; font-size: 10px; color: #475569; }
    .meta-table td { padding: 2px 0; }
    
    .kpi-row { width: 100%; margin-bottom: 20px; border-collapse: separate; border-spacing: 8px 0; }
    .kpi-box { width: 25%; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 10px; text-align: center; }
    .kpi-title { font-size: 9px; text-transform: uppercase; color: #64748b; font-weight: bold; margin-bottom: 4px; }
    .kpi-val { font-size: 18px; font-weight: bold; color: #0f3e2e; }
    .kpi-sub { font-size: 8px; color: #94a3b8; margin-top: 2px; }

    .section-title { font-size: 12px; font-weight: bold; color: #0f3e2e; border-bottom: 1px solid #cbd5e1; padding-bottom: 4px; margin-bottom: 10px; text-transform: uppercase; }

    table.data-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
    table.data-table th, table.data-table td { border: 1px solid #e2e8f0; padding: 7px 8px; text-align: left; }
    table.data-table th { background: #f1f5f9; color: #334155; font-size: 10px; text-transform: uppercase; font-weight: bold; }
    table.data-table tr:nth-child(even) { background: #f8fafc; }
    table.data-table td.text-right { text-align: right; }
    table.data-table td.text-center { text-align: center; }

    .footer { margin-top: 30px; border-top: 1px solid #e2e8f0; padding-top: 10px; font-size: 9px; color: #94a3b8; text-align: center; }
</style>
</head>
<body>

    <div class="header">
        <h2>Research Project Management System</h2>
        <h4>Research &amp; Community Service Directorate (RPMS)</h4>
        <div style="font-size: 13px; font-weight: bold; color: #1a5632; margin-top: 4px;">
            System Research Portfolio &amp; Financial Governance Report
        </div>
    </div>

    <table class="meta-table">
        <tr>
            <td style="width: 50%;"><strong>Academic Call Cycle:</strong> AY 2026/2027 (Call #1 Active)</td>
            <td style="width: 50%; text-align: right;"><strong>Generated Date:</strong> {{ $generatedAt }}</td>
        </tr>
        <tr>
            <td><strong>Export Authority:</strong> {{ $generatedBy }} (VP-ARTTCS / RCSC Governance)</td>
            <td style="text-align: right;"><strong>Reporting Standard:</strong> Research Project Management System Research Governance Standards</td>
        </tr>
    </table>

    <table class="kpi-row">
        <tr>
            <td class="kpi-box">
                <div class="kpi-title">Total Grants</div>
                <div class="kpi-val">{{ $totalProjects }}</div>
                <div class="kpi-sub">Research Project Management System-wide</div>
            </td>
            <td class="kpi-box">
                <div class="kpi-title">Active Research</div>
                <div class="kpi-val">{{ $activeProjects }}</div>
                <div class="kpi-sub">Currently in field</div>
            </td>
            <td class="kpi-box">
                <div class="kpi-title">Completed</div>
                <div class="kpi-val">{{ $completedProjects }}</div>
                <div class="kpi-sub">Successfully closed</div>
            </td>
            <td class="kpi-box">
                <div class="kpi-title">Under Review</div>
                <div class="kpi-val">{{ $underReviewProjects }}</div>
                <div class="kpi-sub">Peer review stage</div>
            </td>
        </tr>
    </table>

    <div class="section-title">1. Financial Overview &amp; Governance Allocation by Thematic Area</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 45%;">Thematic Area</th>
                <th class="text-center" style="width: 12%;">Projects</th>
                <th class="text-right" style="width: 18%;">Total Requested (ETB)</th>
                <th class="text-right" style="width: 18%;">Total Approved (ETB)</th>
                <th class="text-center" style="width: 17%;">Governance Tier</th>
            </tr>
        </thead>
        <tbody>
            @forelse($budgetByThematic as $row)
            <tr>
                <td style="font-weight: bold; color: #1e293b;">{{ $row->thematic_title }}</td>
                <td class="text-center">{{ $row->project_count }}</td>
                <td class="text-right">{{ number_format($row->total_requested, 2) }}</td>
                <td class="text-right">{{ number_format($row->total_approved ?? 0, 2) }}</td>
                <td class="text-center">
                    {{ $row->total_requested >= 500000 ? 'RCSC / VP Tier' : 'College Dean Tier' }}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center" style="color: #64748b; padding: 15px;">No submitted research proposals recorded for the active call yet.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="section-title">2. Research Project Status Distribution</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 50%;">Project Workflow Status</th>
                <th class="text-center" style="width: 25%;">Number of Projects</th>
                <th class="text-right" style="width: 25%;">Percentage</th>
            </tr>
        </thead>
        <tbody>
            @forelse($statusCounts as $sc)
            <tr>
                <td>{{ str_replace('_', ' ', $sc->status) }}</td>
                <td class="text-center" style="font-weight: bold;">{{ $sc->total }}</td>
                <td class="text-right">{{ $totalProjects > 0 ? number_format(($sc->total / $totalProjects) * 100, 1) . '%' : '0.0%' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="3" class="text-center" style="color: #64748b; padding: 10px;">No project status distribution data recorded yet.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Official Document &bull; Research Project Management System Research Project Management System (RPMS) &bull; Verified by Office of the Vice President for ARTTCS
    </div>

</body>
</html>

