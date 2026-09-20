<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'Times New Roman', serif; padding: 30px; font-size: 13px; color: #222; }
    .contract-container { width: 100%; border: 2px solid #1a5632; padding: 30px; position: relative; background: #fff; }
    .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #1a5632; padding-bottom: 15px; }
    .uni-name { font-size: 20px; font-weight: bold; color: #1a5632; text-transform: uppercase; letter-spacing: 1px; }
    .uni-office { font-size: 13px; color: #444; font-weight: bold; margin-top: 4px; }
    .contract-title { font-size: 17px; font-weight: bold; color: #111; text-transform: uppercase; margin-top: 15px; letter-spacing: 1px; }
    .section-title { font-size: 13px; font-weight: bold; color: #1a5632; text-transform: uppercase; margin: 15px 0 8px; border-bottom: 1px solid #ccc; padding-bottom: 3px; }
    table { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
    table.info-table td { padding: 6px 8px; vertical-align: top; }
    table.info-table td.label { width: 30%; font-weight: bold; color: #555; }
    table.info-table td.value { width: 70%; }
    .terms-list { margin-left: 20px; margin-bottom: 15px; line-height: 1.5; }
    .terms-list li { margin-bottom: 6px; }
    .signatures { margin-top: 30px; width: 100%; }
    .sig-col { width: 50%; float: left; text-align: center; padding: 10px; }
    .sig-line { width: 80%; margin: 40px auto 5px; border-top: 1px solid #222; }
    .clear { clear: both; }
</style>
</head>
<body>
<div class="contract-container">
    <div class="header">
        <div class="uni-name">Gambella University</div>
        <div class="uni-office">Office of the Vice President for Academic, Research, Technology Transfer &amp; Community Service (ARTTCS)</div>
        <div class="contract-title">Institutional Research Grant Agreement</div>
    </div>

    <div class="section-title">1. Project &amp; Principal Investigator Details</div>
    <table class="info-table">
        <tr>
            <td class="label">Project ID:</td>
            <td class="value">#{{ $project->project_id }}</td>
        </tr>
        <tr>
            <td class="label">Research Title:</td>
            <td class="value"><strong>{{ $project->title }}</strong></td>
        </tr>
        <tr>
            <td class="label">Principal Investigator:</td>
            <td class="value">{{ $project->pi->name ?? 'N/A' }} ({{ $project->pi->staff_id ?? 'N/A' }})</td>
        </tr>
        <tr>
            <td class="label">Department / College:</td>
            <td class="value">{{ $project->department->name ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td class="label">Thematic Area:</td>
            <td class="value">{{ $project->thematicArea->title ?? 'General' }}</td>
        </tr>
    </table>

    <div class="section-title">2. Financial Allocation &amp; Tranche Structure</div>
    <table class="info-table">
        <tr>
            <td class="label">Approved Budget:</td>
            <td class="value"><strong>{{ number_format($project->approved_budget ?? $project->requested_budget, 2) }} ETB</strong></td>
        </tr>
        <tr>
            <td class="label">Disbursement Schedule:</td>
            <td class="value">
                Tranche 1 (30% Advance) &mdash; Tranche 2 (40% Mid-term upon 50% progress) &mdash; Tranche 3 (30% Final completion)
            </td>
        </tr>
    </table>

    <div class="section-title">3. Institutional Obligations &amp; Compliance</div>
    <ul class="terms-list">
        <li>The Principal Investigator agrees to conduct the research in accordance with Gambella University research guidelines and ethics codes.</li>
        <li>Progress reports and financial receipts must be submitted before subsequent budget tranches are disbursed.</li>
        <li>All research publications and deliverables generated under this grant remain institutional property subject to GMU IP policy.</li>
    </ul>

    <div class="section-title">4. Contract Execution Signatures</div>
    <div class="signatures">
        <div class="sig-col">
            <div class="sig-line"></div>
            <strong>{{ $project->pi->name ?? 'Principal Investigator' }}</strong><br>
            <span>Principal Investigator (Grantee)</span><br>
            @if($project->pi_signature_date)
                <div style="margin-top: 6px; color: #1a5632; font-weight: bold; font-size: 11px;">
                    &#10003; DIGITALLY SIGNED<br>
                    <span style="font-weight: normal; color: #555;">{{ \Carbon\Carbon::parse($project->pi_signature_date)->format('M d, Y \a\t H:i') }}</span>
                </div>
            @else
                <div style="margin-top: 6px; color: #c67a00; font-style: italic; font-size: 11px;">
                    [ Signature Pending ]
                </div>
            @endif
        </div>
        <div class="sig-col">
            <div class="sig-line"></div>
            <strong>Prof. Kassahun Zewdie</strong><br>
            <span>Vice President for ARTTCS (Grantor)</span><br>
            @if($project->vp_signature_date)
                <div style="margin-top: 6px; color: #1a5632; font-weight: bold; font-size: 11px;">
                    &#10003; COUNTERSIGNED &amp; APPROVED<br>
                    <span style="font-weight: normal; color: #555;">{{ \Carbon\Carbon::parse($project->vp_signature_date)->format('M d, Y \a\t H:i') }}</span>
                </div>
            @else
                <div style="margin-top: 6px; color: #c67a00; font-style: italic; font-size: 11px;">
                    [ Awaiting VP Countersignature ]
                </div>
            @endif
        </div>
        <div class="clear"></div>
    </div>

    @if($project->pi_signature_date && $project->vp_signature_date)
    <div style="margin-top: 25px; padding: 10px; background: #e8f5e9; border: 1px solid #2e7d32; text-align: center; color: #1b5e20; font-size: 11px; font-weight: bold; letter-spacing: 0.5px;">
        &#10003; FULLY EXECUTED &amp; ACTIVE INSTITUTIONAL RESEARCH CONTRACT &mdash; GAMBELLA UNIVERSITY
    </div>
    @elseif($project->pi_signature_date)
    <div style="margin-top: 25px; padding: 8px; background: #fff8e1; border: 1px dashed #f57f17; text-align: center; color: #b78103; font-size: 11px; font-weight: bold;">
        PARTIALLY EXECUTED &mdash; Signed by PI &bull; Awaiting VP-ARTTCS Countersignature to Activate
    </div>
    @endif
</div>
</body>
</html>

