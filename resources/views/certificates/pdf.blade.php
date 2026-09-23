<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'Times New Roman', serif; }
    .certificate {
        width: 100%;
        min-height: 750px;
        border: 8px double #1a5632;
        padding: 40px 50px;
        position: relative;
        background: #fff;
    }
    .corner-ornament {
        position: absolute;
        width: 60px;
        height: 60px;
        border: 3px solid #c9a227;
    }
    .corner-tl { top: 15px; left: 15px; border-right: none; border-bottom: none; }
    .corner-tr { top: 15px; right: 15px; border-left: none; border-bottom: none; }
    .corner-bl { bottom: 15px; left: 15px; border-right: none; border-top: none; }
    .corner-br { bottom: 15px; right: 15px; border-left: none; border-top: none; }
    .header { text-align: center; margin-bottom: 20px; }
    .uni-name {
        font-size: 22px;
        font-weight: bold;
        color: #1a5632;
        text-transform: uppercase;
        letter-spacing: 2px;
    }
    .uni-motto {
        font-size: 11px;
        color: #666;
        font-style: italic;
        margin-top: 3px;
    }
    .divider {
        width: 200px;
        height: 2px;
        background: linear-gradient(to right, transparent, #c9a227, transparent);
        margin: 12px auto;
    }
    .cert-title {
        text-align: center;
        font-size: 30px;
        font-weight: bold;
        color: #1a5632;
        text-transform: uppercase;
        letter-spacing: 4px;
        margin: 15px 0;
    }
    .cert-subtitle {
        text-align: center;
        font-size: 13px;
        color: #888;
        text-transform: uppercase;
        letter-spacing: 3px;
        margin-bottom: 25px;
    }
    .body { text-align: center; margin: 20px 0; }
    .body-text {
        font-size: 14px;
        color: #333;
        line-height: 1.8;
        max-width: 500px;
        margin: 0 auto;
    }
    .recipient-name {
        font-size: 24px;
        font-weight: bold;
        color: #1a5632;
        text-decoration: underline;
        text-underline-offset: 4px;
        margin: 15px 0;
    }
    .project-title {
        font-size: 14px;
        color: #555;
        font-style: italic;
        max-width: 450px;
        margin: 10px auto;
    }
    .cert-code {
        font-size: 11px;
        color: #999;
        margin-top: 20px;
        letter-spacing: 1px;
    }
    .footer {
        display: table;
        width: 100%;
        margin-top: 40px;
    }
    .footer-col {
        display: table-cell;
        width: 33%;
        text-align: center;
        vertical-align: bottom;
    }
    .signature-line {
        width: 150px;
        border-top: 1px solid #333;
        margin: 0 auto 5px;
        padding-top: 5px;
    }
    .signer-name {
        font-size: 12px;
        font-weight: bold;
        color: #333;
    }
    .signer-title {
        font-size: 10px;
        color: #666;
    }
    .date-line {
        text-align: center;
        font-size: 12px;
        color: #666;
        margin-top: 10px;
    }
    .watermark {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%) rotate(-30deg);
        font-size: 80px;
        color: rgba(26, 86, 50, 0.04);
        font-weight: bold;
        text-transform: uppercase;
        letter-spacing: 10px;
        pointer-events: none;
    }
</style>
</head>
<body>
<div class="certificate">
    <div class="corner-ornament corner-tl"></div>
    <div class="corner-ornament corner-tr"></div>
    <div class="corner-ornament corner-bl"></div>
    <div class="corner-ornament corner-br"></div>
    <div class="watermark">Institution</div>

    <div class="header">
        <div class="uni-name">Institution</div>
        <div class="uni-motto">Excellence in Research, Technology Transfer &amp; Community Service</div>
    </div>
    <div class="divider"></div>

    @php
        $isAward = in_array($type, ['Award', 'PublicationAward']);
    @endphp

    <div class="cert-title">{{ $isAward ? 'Award of Excellence' : 'Certificate of Completion' }}</div>
    <div class="cert-subtitle">{{ $isAward ? 'Journal Publication Award' : 'Research Project Completion' }}</div>

    <div class="body">
        <div class="body-text">
            This is to certify that
        </div>
        <div class="recipient-name">{{ $issued_to_name }}</div>
        <div class="body-text">
            has successfully {{ $isAward ? 'published peer-reviewed research work in a reputable journal and is hereby recognized with this official award of excellence' : 'completed the research project hereunder described and is awarded this certificate of completion in recognition of the successful fulfillment of all project requirements' }}:
        </div>
        <div class="project-title">"{{ $project->title }}"</div>
        <div class="body-text" style="font-size: 12px; margin-top: 10px;">
            Project ID: #{{ $project->project_id }} &nbsp;|&nbsp;
            Budget: {{ number_format($project->approved_budget ?: ($project->requested_budget ?? 0), 2) }} ETB &nbsp;|&nbsp;
            Status: {{ $project->status }}
        </div>
    </div>

    <div class="date-line">Issued on {{ $issued_at ? \Carbon\Carbon::parse($issued_at)->format('F j, Y') : now()->format('F j, Y') }}</div>

    <div class="footer">
        <div class="footer-col">
            <div class="signature-line"></div>
            <div class="signer-name">Principal Investigator</div>
            <div class="signer-title">Research Project Lead</div>
        </div>
        <div class="footer-col">
            <div class="signature-line"></div>
            <div class="signer-name">Research Coordinator</div>
            <div class="signer-title">College of Engineering &amp; Technology</div>
        </div>
        <div class="footer-col">
            <div class="signature-line"></div>
            <div class="signer-name">Vice President (ARTTCS)</div>
            <div class="signer-title">Institution</div>
        </div>
    </div>

    <div class="cert-code">Certificate Code: {{ $certificate_code }}</div>
</div>
</body>
</html>
