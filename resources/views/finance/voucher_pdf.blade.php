<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Gambella University - Payment Disbursement Voucher</title>
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'Helvetica Neue', Arial, sans-serif; font-size: 11px; color: #1e293b; padding: 30px; }
    .voucher-box { border: 2px solid #0f3e2e; padding: 25px; border-radius: 8px; position: relative; }
    .header { border-bottom: 2px solid #0f3e2e; padding-bottom: 12px; margin-bottom: 20px; text-align: center; }
    .header h2 { color: #0f3e2e; font-size: 20px; text-transform: uppercase; letter-spacing: 1.5px; margin-bottom: 3px; }
    .header h4 { color: #475569; font-size: 13px; font-weight: normal; margin-bottom: 4px; }
    .doc-title { font-size: 15px; font-weight: bold; color: #b45309; text-transform: uppercase; margin-top: 6px; letter-spacing: 1px; }

    .voucher-meta { width: 100%; margin-bottom: 20px; border-collapse: collapse; }
    .voucher-meta td { padding: 4px 6px; font-size: 11px; }

    .section-title { font-size: 12px; font-weight: bold; color: #0f3e2e; background: #f1f5f9; padding: 6px 10px; margin-bottom: 12px; text-transform: uppercase; border-left: 4px solid #0f3e2e; }

    table.details-table { width: 100%; border-collapse: collapse; margin-bottom: 25px; }
    table.details-table th, table.details-table td { border: 1px solid #cbd5e1; padding: 8px 10px; font-size: 11px; }
    table.details-table th { background: #f8fafc; font-weight: bold; color: #334155; text-align: left; }
    table.details-table td.amount-cell { font-size: 14px; font-weight: bold; color: #0f3e2e; text-align: right; }

    .signatures-table { width: 100%; margin-top: 40px; border-collapse: collapse; }
    .signatures-table td { width: 33.33%; text-align: center; padding: 10px 15px; vertical-align: bottom; }
    .sign-line { border-top: 1px solid #334155; margin-top: 50px; padding-top: 5px; font-size: 10px; font-weight: bold; color: #334155; }

    .footer { margin-top: 30px; border-top: 1px dashed #cbd5e1; padding-top: 8px; font-size: 9px; color: #64748b; text-align: center; }
</style>
</head>
<body>

<div class="voucher-box">
    <div class="header">
        <h2>Gambella University</h2>
        <h4>Finance &amp; Grant Disbursement Directorate</h4>
        <div class="doc-title">Official Grant Disbursement Voucher</div>
    </div>

    <table class="voucher-meta">
        <tr>
            <td style="width: 50%;"><strong>Voucher / Ref No:</strong> #VOUCH-{{ $request->request_id }}-{{ date('Y') }}</td>
            <td style="width: 50%; text-align: right;"><strong>Disbursement Date:</strong> {{ $request->disbursed_at ? $request->disbursed_at->format('M d, Y H:i') : now()->format('M d, Y') }}</td>
        </tr>
        <tr>
            <td><strong>Project Code:</strong> {{ $request->project->project_code ?? ('GMU-PRJ-' . $request->project->project_id) }}</td>
            <td style="text-align: right;"><strong>Governance Tier:</strong> {{ $request->approval_tier ?? 'University Tier' }}</td>
        </tr>
    </table>

    <div class="section-title">1. Beneficiary &amp; Project Information</div>
    <table class="details-table">
        <tr>
            <th style="width: 30%;">Principal Investigator:</th>
            <td style="width: 70%; font-weight: bold;">{{ $request->project->pi->name ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th>PI Staff ID / Email:</th>
            <td>{{ $request->project->pi->staff_id ?? 'GMU-STAFF' }} &bull; {{ $request->project->pi->email ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th>Project Title:</th>
            <td style="font-weight: bold; color: #0f172a;">{{ $request->project->title }}</td>
        </tr>
        <tr>
            <th>Thematic Priority Area:</th>
            <td>{{ $request->project->thematicArea->title ?? 'General' }}</td>
        </tr>
        <tr>
            <th>Department / College:</th>
            <td>{{ $request->project->department->name ?? 'Academic Directorate' }}</td>
        </tr>
    </table>

    <div class="section-title">2. Financial Release Breakdown</div>
    <table class="details-table">
        <tr>
            <th style="width: 30%;">Total Approved Grant Budget:</th>
            <td style="width: 70%; font-weight: bold;">{{ number_format($request->project->approved_budget ?: $request->project->requested_budget, 2) }} ETB</td>
        </tr>
        <tr>
            <th>Disbursement Tranche Phase:</th>
            <td style="font-weight: bold;">{{ $request->milestone_phase ?? 'Tranche Disbursement' }}</td>
        </tr>
        <tr>
            <th>Payment Method:</th>
            <td><strong>{{ $request->payment_method ?? 'Bank Transfer' }}</strong></td>
        </tr>
        <tr>
            <th>Reference Notes / Check No:</th>
            <td>{{ $request->notes ?? 'Standard milestone grant disbursement' }}</td>
        </tr>
        <tr>
            <th style="background: #e2e8f0; font-size: 12px;">Net Disbursed Amount:</th>
            <td class="amount-cell" style="background: #f8fafc; font-size: 15px;">
                ETB {{ number_format($request->approved_amount, 2) }}
            </td>
        </tr>
    </table>

    <div class="section-title">3. Authorization &amp; Acknowledgement Signatures</div>
    <table class="signatures-table">
        <tr>
            <td>
                <div class="sign-line">
                    Prepared By<br>
                    <span style="font-weight: normal; font-size: 9px;">Finance Grant Officer</span>
                </div>
            </td>
            <td>
                <div class="sign-line">
                    Authorized By<br>
                    <span style="font-weight: normal; font-size: 9px;">Director of Finance / VP</span>
                </div>
            </td>
            <td>
                <div class="sign-line">
                    Received By (PI)<br>
                    <span style="font-weight: normal; font-size: 9px;">Principal Investigator Signature</span>
                </div>
            </td>
        </tr>
    </table>

    <div class="footer">
        This is an official financial disbursement document issued by the Gambella University Research Project Management System (GMU-RPMS).
    </div>
</div>

</body>
</html>
