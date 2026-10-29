@extends('layouts.app')

@section('styles')
<style>
    /* ==========================================================================
       RPMS ACADEMIC SHOWCASE - MODERN INSTITUTIONAL STYLES
       ========================================================================== */
    .hero-banner {
        background: linear-gradient(135deg, #072318 0%, #0f3e2e 60%, #165b43 100%);
        border-radius: 20px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 15px 35px -10px rgba(15, 62, 46, 0.45);
        border: 1px solid rgba(255, 255, 255, 0.1);
    }
    .hero-banner::before {
        content: "";
        position: absolute;
        top: -50%;
        right: -20%;
        width: 500px;
        height: 500px;
        background: radial-gradient(circle, rgba(46, 139, 87, 0.25) 0%, transparent 70%);
        pointer-events: none;
    }
    .hero-banner::after {
        content: "";
        position: absolute;
        bottom: -40%;
        left: -10%;
        width: 400px;
        height: 400px;
        background: radial-gradient(circle, rgba(212, 175, 55, 0.12) 0%, transparent 70%);
        pointer-events: none;
    }
    .hero-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        background: rgba(255, 255, 255, 0.12);
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        color: #ffffff;
        font-size: 0.78rem;
        font-weight: 600;
        letter-spacing: 0.8px;
        text-transform: uppercase;
        padding: 0.35rem 0.9rem;
        border-radius: 50px;
    }
    .pulse-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background-color: #22c55e;
        box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.7);
        animation: pulseAnimation 2s infinite;
    }
    @keyframes pulseAnimation {
        0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.7); }
        70% { transform: scale(1); box-shadow: 0 0 0 6px rgba(34, 197, 94, 0); }
        100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(34, 197, 94, 0); }
    }
    .stat-box {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 1.25rem 1rem;
        transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        position: relative;
    }
    .stat-box:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 24px -6px rgba(15, 62, 46, 0.12);
        border-color: #0f3e2e;
    }
    .stat-icon-wrap {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        margin-bottom: 0.75rem;
    }
    .stat-icon-emerald { background: #ecfdf5; color: #059669; }
    .stat-icon-blue    { background: #eff6ff; color: #2563eb; }
    .stat-icon-amber   { background: #fffbeb; color: #d97706; }
    .stat-icon-purple  { background: #faf5ff; color: #7c3aed; }

    .feature-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        transition: all 0.22s ease;
        height: 100%;
        display: flex;
        flex-direction: column;
    }
    .feature-card:hover {
        transform: translateY(-3px);
        border-color: #cbd5e1;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.06);
    }
    .step-pill {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        background: #0f3e2e;
        color: #ffffff;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.95rem;
        box-shadow: 0 4px 10px rgba(15, 62, 46, 0.25);
    }
    .role-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        transition: all 0.25s ease;
        overflow: hidden;
    }
    .role-card:hover {
        transform: translateY(-3px);
        border-color: #0f3e2e;
        box-shadow: 0 8px 22px rgba(15, 62, 46, 0.08);
    }
    .role-header {
        padding: 1.25rem;
        background: #fafbfc;
        border-bottom: 1px solid #f1f5f9;
    }
    .pill-tag {
        font-size: 0.72rem;
        font-weight: 600;
        padding: 0.25rem 0.65rem;
        border-radius: 50px;
    }
</style>
@endsection

@section('content')
<div class="py-2 py-md-3">
    <div class="container">

        {{-- ======================================================================
             SECTION 1: HIGH-IMPACT INSTITUTIONAL HERO BANNER
             ====================================================================== --}}
        <section class="hero-banner p-4 p-md-5 mb-4 text-white">
            <div class="position-relative" style="z-index: 2;">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                    <div class="hero-badge">
                        <span class="pulse-dot"></span>
                        <span>Research &amp; Technology Transfer Directorate</span>
                    </div>
                    <span class="badge bg-white bg-opacity-10 text-white border border-white border-opacity-25 px-3 py-1 rounded-pill small">
                        <i class="bi bi-shield-check me-1"></i> Institutional Portal
                    </span>
                </div>

                <div class="row align-items-center">
                    <div class="col-lg-8">
                        <h1 class="display-6 fw-bold mb-3" style="font-size: calc(1.5rem + 1.3vw); letter-spacing: -0.03em; line-height: 1.2;">
                            Advancing Academic Discovery Through Verifiable Research Governance
                        </h1>
                        <p class="lead text-white text-opacity-85 mb-4" style="max-width: 660px; font-size: 1.08rem; line-height: 1.6;">
                            A unified, double-blind peer-reviewed institutional portal managing research proposal intakes, dual-threshold budget approvals, and verifiable milestone disbursements.
                        </p>

                        {{-- Desktop Action Cluster --}}
                        <div class="d-none d-sm-flex flex-wrap gap-2 gap-md-3 mb-3">
                            @auth
                                <a href="{{ route('dashboard') }}" class="btn btn-light text-dark px-4 py-2 fw-bold shadow-sm">
                                    <i class="bi bi-speedometer2 me-2 text-success"></i> Open Dashboard
                                </a>
                            @else
                                <a href="{{ route('login') }}" class="btn btn-light text-dark px-4 py-2 fw-bold shadow-sm">
                                    <i class="bi bi-box-arrow-in-right me-2 text-success"></i> Sign In to Portal
                                </a>
                            @endauth
                            <a href="{{ route('research.catalog') }}" class="btn btn-outline-light px-3 py-2 fw-semibold">
                                <i class="bi bi-journal-text me-1"></i> Research Catalog
                            </a>
                            <a href="{{ route('lifecycle') }}" class="btn btn-outline-light px-3 py-2 fw-semibold">
                                <i class="bi bi-diagram-3 me-1"></i> Lifecycle &amp; Rubric
                            </a>
                            <a href="{{ route('support') }}" class="btn btn-outline-light px-3 py-2 fw-semibold text-white-50">
                                <i class="bi bi-headset me-1"></i> Support
                            </a>
                        </div>

                        {{-- Mobile Action Stack --}}
                        <div class="d-sm-none mb-3">
                            <div class="row g-2">
                                <div class="col-12">
                                    @auth
                                        <a href="{{ route('dashboard') }}" class="btn btn-light text-dark w-100 py-2 fw-bold shadow-sm">
                                            <i class="bi bi-speedometer2 me-1 text-success"></i> Open Dashboard
                                        </a>
                                    @else
                                        <a href="{{ route('login') }}" class="btn btn-light text-dark w-100 py-2 fw-bold shadow-sm">
                                            <i class="bi bi-box-arrow-in-right me-1 text-success"></i> Sign In to Portal
                                        </a>
                                    @endauth
                                </div>
                                <div class="col-4">
                                    <a href="{{ route('research.catalog') }}" class="btn btn-outline-light w-100 py-2 fw-semibold text-truncate small" style="font-size: 0.78rem;">
                                        <i class="bi bi-journal-text d-block mb-1"></i> Catalog
                                    </a>
                                </div>
                                <div class="col-4">
                                    <a href="{{ route('lifecycle') }}" class="btn btn-outline-light w-100 py-2 fw-semibold text-truncate small" style="font-size: 0.78rem;">
                                        <i class="bi bi-diagram-3 d-block mb-1"></i> Rubric
                                    </a>
                                </div>
                                <div class="col-4">
                                    <a href="{{ route('support') }}" class="btn btn-outline-light w-100 py-2 fw-semibold text-truncate small" style="font-size: 0.78rem;">
                                        <i class="bi bi-headset d-block mb-1"></i> Helpdesk
                                    </a>
                                </div>
                            </div>
                        </div>

                        {{-- Live Trust Strip --}}
                        <div class="d-flex flex-wrap align-items-center gap-3 pt-3 border-top border-white border-opacity-20 text-white-50 small" style="font-size: 0.8rem;">
                            <span><i class="bi bi-check2-circle text-success me-1"></i> Double-Blind Review</span>
                            <span><i class="bi bi-check2-circle text-success me-1"></i> 30-40-30 Tranches</span>
                            <span><i class="bi bi-check2-circle text-success me-1"></i> Dual-Tier Approvals</span>
                            <span><i class="bi bi-check2-circle text-success me-1"></i> Role-Based Access</span>
                        </div>
                    </div>

                    {{-- Hero Graphic / Mini Status Showcase (Desktop Only) --}}
                    <div class="col-lg-4 d-none d-lg-block text-end">
                        <div class="p-3 bg-white bg-opacity-10 rounded-4 border border-white border-opacity-20 text-start shadow">
                            <div class="d-flex align-items-center justify-content-between pb-2 mb-2 border-bottom border-white border-opacity-15">
                                <span class="fw-bold text-white small"><i class="bi bi-shield-lock me-1"></i> Governance Engine</span>
                                <span class="badge bg-success text-white" style="font-size: 0.7rem;">Active</span>
                            </div>
                            <div class="small text-white-50 mb-2">
                                <div class="d-flex justify-content-between mb-1">
                                    <span>Peer Review Status:</span>
                                    <span class="text-white fw-semibold">Dynamic Masking</span>
                                </div>
                                <div class="d-flex justify-content-between mb-1">
                                    <span>Financial Governance:</span>
                                    <span class="text-white fw-semibold">Dean &amp; RCSC Dual-Tier</span>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span>Disbursement Model:</span>
                                    <span class="text-white fw-semibold">3-Milestone Tranches</span>
                                </div>
                            </div>
                            <div class="p-2 rounded bg-black bg-opacity-20 text-center">
                                <span class="text-white small fw-bold" style="letter-spacing: 0.5px;">Institutional Research Portal 2026/2027</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- ======================================================================
             SECTION 2: DYNAMIC INSTITUTIONAL METRICS (4 KEY COUNTERS)
             ====================================================================== --}}
        <section class="mb-4">
            <div class="row g-2 g-md-3">
                <div class="col-6 col-lg-3">
                    <div class="stat-box text-center">
                        <div class="stat-icon-wrap stat-icon-emerald mx-auto">
                            <i class="bi bi-folder-check"></i>
                        </div>
                        <div class="display-6 fw-bold text-dark mb-0" style="font-size: calc(1.4rem + 0.6vw);">
                            {{ $stats['projects'] ?? 4 }}
                        </div>
                        <div class="fw-bold text-muted text-uppercase mt-1" style="font-size: 0.74rem; letter-spacing: 0.5px;">
                            Proposals in Intake
                        </div>
                        <div class="text-success small mt-1" style="font-size: 0.74rem;">
                            <i class="bi bi-arrow-up-right"></i> Active Pipeline
                        </div>
                    </div>
                </div>

                <div class="col-6 col-lg-3">
                    <div class="stat-box text-center">
                        <div class="stat-icon-wrap stat-icon-blue mx-auto">
                            <i class="bi bi-compass"></i>
                        </div>
                        <div class="display-6 fw-bold text-dark mb-0" style="font-size: calc(1.4rem + 0.6vw);">
                            {{ $stats['thematics'] ?? 3 }}
                        </div>
                        <div class="fw-bold text-muted text-uppercase mt-1" style="font-size: 0.74rem; letter-spacing: 0.5px;">
                            Priority Themes
                        </div>
                        <div class="text-primary small mt-1" style="font-size: 0.74rem;">
                            <i class="bi bi-stars"></i> Council Approved
                        </div>
                    </div>
                </div>

                <div class="col-6 col-lg-3">
                    <div class="stat-box text-center">
                        <div class="stat-icon-wrap stat-icon-amber mx-auto">
                            <i class="bi bi-buildings"></i>
                        </div>
                        <div class="display-6 fw-bold text-dark mb-0" style="font-size: calc(1.4rem + 0.6vw);">
                            {{ $stats['colleges'] ?? 3 }}
                        </div>
                        <div class="fw-bold text-muted text-uppercase mt-1" style="font-size: 0.74rem; letter-spacing: 0.5px;">
                            Colleges &amp; Units
                        </div>
                        <div class="text-warning small mt-1" style="font-size: 0.74rem;">
                            <i class="bi bi-check2"></i> Multi-Disciplinary
                        </div>
                    </div>
                </div>

                <div class="col-6 col-lg-3">
                    <div class="stat-box text-center">
                        <div class="stat-icon-wrap stat-icon-purple mx-auto">
                            <i class="bi bi-journal-bookmark-fill"></i>
                        </div>
                        <div class="display-6 fw-bold text-dark mb-0" style="font-size: calc(1.4rem + 0.6vw);">
                            {{ $stats['papers'] ?? 0 }}
                        </div>
                        <div class="fw-bold text-muted text-uppercase mt-1" style="font-size: 0.74rem; letter-spacing: 0.5px;">
                            Published Papers
                        </div>
                        <div class="text-purple small mt-1" style="font-size: 0.74rem; color: #7c3aed;">
                            <i class="bi bi-patch-check"></i> Peer-Reviewed
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- ======================================================================
             SECTION 3: ACTIVE GRANT CALLS (Surfaced Dynamically)
             ====================================================================== --}}
        @if(isset($activeCalls) && $activeCalls->count() > 0)
        <section class="mb-4">
            <div class="card border-0 shadow-xs rounded-4 p-3 p-md-4 bg-white">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3">
                    <div>
                        <span class="badge bg-danger text-white px-2 py-1 mb-1" style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.5px;">
                            <i class="bi bi-broadcast me-1"></i> Open Calls
                        </span>
                        <h2 class="h5 fw-bold text-dark mb-0">Active Institutional Research Grant Calls</h2>
                    </div>
                    <span class="text-muted small mt-1 mt-md-0">
                        Submit faculty proposals online before the designated intake deadlines.
                    </span>
                </div>

                <div class="row g-2 g-md-3">
                    @foreach($activeCalls as $call)
                        <div class="col-12 col-md-4">
                            <div class="feature-card p-3">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <span class="badge bg-dark fw-normal" style="font-size: 0.72rem;">{{ $call->call_type ?? 'Annual Call' }}</span>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle fw-semibold" style="font-size: 0.7rem;">
                                        Open
                                    </span>
                                </div>
                                <h3 class="h6 fw-bold text-dark mb-2" style="line-height: 1.4;">{{ $call->title }}</h3>
                                <p class="text-muted small mb-3 flex-grow-1" style="font-size: 0.84rem; line-height: 1.45;">
                                    {{ Str::limit($call->description, 130) }}
                                </p>
                                <div class="pt-2 border-top text-muted small d-flex align-items-center justify-content-between" style="font-size: 0.78rem;">
                                    <span><i class="bi bi-calendar-event me-1"></i> Deadline:</span>
                                    <span class="fw-bold text-dark">{{ $call->deadline ? \Carbon\Carbon::parse($call->deadline)->format('M d, Y') : 'Open Intake' }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
        @endif

        {{-- ======================================================================
             SECTION 4: 4-STAGE RESEARCH LIFECYCLE BANNER
             ====================================================================== --}}
        <section class="card border-0 shadow-xs rounded-4 p-3 p-md-4 bg-white mb-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3">
                <div>
                    <span class="text-uppercase fw-bold text-muted small" style="letter-spacing: 1px; font-size: 0.74rem;">
                        Systematic Workflow
                    </span>
                    <h2 class="h5 fw-bold text-dark mb-0">End-to-End Governance Lifecycle</h2>
                </div>
                <a href="{{ route('lifecycle') }}" class="btn btn-sm btn-outline-dark fw-semibold mt-2 mt-md-0 align-self-start align-self-md-auto">
                    View Full Evaluation Rubrics <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>

            <div class="row g-2 g-md-3 text-center">
                <div class="col-6 col-md-3">
                    <div class="feature-card p-3">
                        <div class="step-pill mx-auto mb-2">1</div>
                        <h3 class="fw-bold text-dark mb-1" style="font-size: 0.88rem;">Intake &amp; DH Screening</h3>
                        <p class="text-muted small mb-0" style="font-size: 0.78rem;">PI proposal submission with departmental alignment verification.</p>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="feature-card p-3">
                        <div class="step-pill mx-auto mb-2">2</div>
                        <h3 class="fw-bold text-dark mb-1" style="font-size: 0.88rem;">Double-Blind Review</h3>
                        <p class="text-muted small mb-0" style="font-size: 0.78rem;">Independent masked peer evaluations scored on objective rubrics.</p>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="feature-card p-3">
                        <div class="step-pill mx-auto mb-2">3</div>
                        <h3 class="fw-bold text-dark mb-1" style="font-size: 0.88rem;">Dual-Tier Approval</h3>
                        <p class="text-muted small mb-0" style="font-size: 0.78rem;">Dean approval (&lt;500k ETB) or RCSC/VP Council ratification (&ge;500k ETB).</p>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="feature-card p-3">
                        <div class="step-pill mx-auto mb-2">4</div>
                        <h3 class="fw-bold text-dark mb-1" style="font-size: 0.88rem;">Milestone Funding</h3>
                        <p class="text-muted small mb-0" style="font-size: 0.78rem;">Audited disbursements released in 3 tranches: 30% &bull; 40% &bull; 30%.</p>
                    </div>
                </div>
            </div>
        </section>

        {{-- ======================================================================
             SECTION 5: PRIORITY THEMATIC RESEARCH AREAS
             ====================================================================== --}}
        <section class="card border-0 shadow-xs rounded-4 p-3 p-md-4 bg-white mb-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end mb-3">
                <div>
                    <span class="text-uppercase fw-bold text-muted small" style="letter-spacing: 1px; font-size: 0.74rem;">
                        Strategic Pillars
                    </span>
                    <h2 class="h5 fw-bold text-dark mb-1">Priority Research Thematic Areas</h2>
                    <p class="text-muted small mb-0">Current institutional grant call priorities for academic faculty proposals:</p>
                </div>
                <span class="badge bg-light text-dark border px-3 py-2 mt-2 mt-md-0 align-self-start align-self-md-auto" style="font-size: 0.78rem;">
                    <i class="bi bi-check-circle-fill text-success me-1"></i> Active Funding Cycle 2026/2027
                </span>
            </div>

            <div class="row g-2 g-md-3">
                @forelse($thematicAreas as $ta)
                    <div class="col-12 col-md-4">
                        <div class="feature-card p-3 p-md-4">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="badge bg-dark fw-semibold" style="font-size: 0.72rem;">
                                    {{ $ta->category ?? 'Research Priority' }}
                                </span>
                                <span class="text-muted small" style="font-size: 0.72rem;">Theme #{{ $ta->id }}</span>
                            </div>
                            <h3 class="h6 fw-bold text-dark mb-2" style="font-size: 1.02rem; line-height: 1.4;">
                                {{ $ta->title }}
                            </h3>
                            <p class="text-muted mb-3 flex-grow-1" style="font-size: 0.86rem; line-height: 1.5;">
                                {{ $ta->description }}
                            </p>
                            <div class="pt-2 border-top text-muted small d-flex align-items-center justify-content-between" style="font-size: 0.78rem;">
                                <span><i class="bi bi-tag me-1"></i> Thematic Grant</span>
                                <span class="fw-bold text-success"><i class="bi bi-circle-fill me-1" style="font-size: 0.5rem;"></i>Open for Intake</span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="p-4 rounded-3 bg-white border text-center text-muted">
                            <i class="bi bi-compass fs-2 text-muted mb-2 d-block"></i>
                            <h4 class="h6 fw-bold text-dark mb-1">Institutional Priority Areas</h4>
                            <p class="small text-muted mb-0">Priority thematic grant areas open for faculty proposals will appear here.</p>
                        </div>
                    </div>
                @endforelse
            </div>
        </section>

        {{-- ======================================================================
             SECTION 6: TAILORED USER PATHWAYS (ROLE PORTALS)
             ====================================================================== --}}
        <section class="card border-0 shadow-xs rounded-4 p-3 p-md-4 bg-white mb-4">
            <div class="text-center mb-3">
                <span class="text-uppercase fw-bold text-muted small" style="letter-spacing: 1px; font-size: 0.74rem;">
                    Institutional Roles
                </span>
                <h2 class="h5 fw-bold text-dark mb-1">Tailored Portals for Every Academic Stakeholder</h2>
            </div>
            <div class="row g-2 g-md-3">
                <div class="col-12 col-md-4">
                    <div class="role-card h-100 d-flex flex-column">
                        <div class="role-header d-flex align-items-center">
                            <div class="rounded-3 bg-success bg-opacity-10 text-success p-2 me-3 fs-5">
                                <i class="bi bi-person-workspace"></i>
                            </div>
                            <div>
                                <h3 class="h6 fw-bold mb-0 text-dark">Faculty &amp; Researchers</h3>
                                <span class="text-muted small" style="font-size: 0.74rem;">Principal Investigators</span>
                            </div>
                        </div>
                        <div class="p-3 flex-grow-1 d-flex flex-column justify-content-between">
                            <p class="text-muted small mb-3" style="font-size: 0.84rem; line-height: 1.5;">
                                Draft multi-investigator proposals, submit line-item budgets, track double-blind reviewer scoring in real-time, and request milestone tranches.
                            </p>
                            <a href="{{ route('login') }}" class="btn btn-sm btn-outline-dark w-100 fw-semibold">
                                <i class="bi bi-arrow-right me-1"></i> Researcher Portal
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-4">
                    <div class="role-card h-100 d-flex flex-column">
                        <div class="role-header d-flex align-items-center">
                            <div class="rounded-3 bg-primary bg-opacity-10 text-primary p-2 me-3 fs-5">
                                <i class="bi bi-pencil-square"></i>
                            </div>
                            <div>
                                <h3 class="h6 fw-bold mb-0 text-dark">Peer Evaluators</h3>
                                <span class="text-muted small" style="font-size: 0.74rem;">Double-Blind Referees</span>
                            </div>
                        </div>
                        <div class="p-3 flex-grow-1 d-flex flex-column justify-content-between">
                            <p class="text-muted small mb-3" style="font-size: 0.84rem; line-height: 1.5;">
                                Access anonymized manuscripts, evaluate 6-dimensional institutional rubrics, submit qualitative feedback, and recommend funding revisions.
                            </p>
                            <a href="{{ route('login') }}" class="btn btn-sm btn-outline-dark w-100 fw-semibold">
                                <i class="bi bi-arrow-right me-1"></i> Evaluator Portal
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-4">
                    <div class="role-card h-100 d-flex flex-column">
                        <div class="role-header d-flex align-items-center">
                            <div class="rounded-3 bg-warning bg-opacity-10 text-warning p-2 me-3 fs-5">
                                <i class="bi bi-bank"></i>
                            </div>
                            <div>
                                <h3 class="h6 fw-bold mb-0 text-dark">Deans &amp; Directorate</h3>
                                <span class="text-muted small" style="font-size: 0.74rem;">Executive Leadership</span>
                            </div>
                        </div>
                        <div class="p-3 flex-grow-1 d-flex flex-column justify-content-between">
                            <p class="text-muted small mb-3" style="font-size: 0.84rem; line-height: 1.5;">
                                Screen departmental alignment, endorse College grants (&lt;500k), preside over RCSC Council ratification (&ge;500k), and audit fund disbursements.
                            </p>
                            <a href="{{ route('login') }}" class="btn btn-sm btn-outline-dark w-100 fw-semibold">
                                <i class="bi bi-arrow-right me-1"></i> Directorate Access
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- ======================================================================
             SECTION 7: INSTITUTIONAL GOVERNANCE PRINCIPLES (THE 3 PILLARS)
             ====================================================================== --}}
        <section class="card border-0 shadow-xs rounded-4 p-3 p-md-4 bg-white mb-4">
            <div class="mb-3">
                <span class="text-uppercase fw-bold text-muted small" style="letter-spacing: 1px; font-size: 0.74rem;">
                    Institutional Standards
                </span>
                <h2 class="h5 fw-bold text-dark mb-1">Core Governance Principles</h2>
                <p class="text-muted small mb-0">Systematically enforced across all university research grants and proposal evaluations:</p>
            </div>
            <div class="row g-2 g-md-3">
                <div class="col-12 col-md-4">
                    <div class="p-3 rounded-3 bg-light border h-100">
                        <div class="fw-bold text-dark mb-2 fs-6 d-flex align-items-center">
                            <i class="bi bi-shield-lock-fill text-success me-2 fs-5"></i>
                            <span>Double-Blind Integrity</span>
                        </div>
                        <p class="text-muted small mb-0" style="line-height: 1.5;">
                            Author names and institutional affiliations are masked dynamically throughout the peer evaluation process to guarantee 100% impartial, merit-driven decisions.
                        </p>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="p-3 rounded-3 bg-light border h-100">
                        <div class="fw-bold text-dark mb-2 fs-6 d-flex align-items-center">
                            <i class="bi bi-sliders text-primary me-2 fs-5"></i>
                            <span>Dual-Tier Governance</span>
                        </div>
                        <p class="text-muted small mb-0" style="line-height: 1.5;">
                            Grants under ETB 500k are expedited through College Deans, whereas major institutional projects (&ge;500k) require formal RCSC Council and VP ratification.
                        </p>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="p-3 rounded-3 bg-light border h-100">
                        <div class="fw-bold text-dark mb-2 fs-6 d-flex align-items-center">
                            <i class="bi bi-cash-stack text-warning me-2 fs-5"></i>
                            <span>Tranche Disbursements</span>
                        </div>
                        <p class="text-muted small mb-0" style="line-height: 1.5;">
                            Approved budgets are released incrementally in 3 verified phases (30% Advance, 40% Mid-Term, 30% Final) strictly upon deliverable milestone approval.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        {{-- ======================================================================
             SECTION 8: HELPDESK & INSTITUTIONAL SUPPORT STRIP
             ====================================================================== --}}
        <section class="card border-0 shadow-xs rounded-4 p-3 p-md-4 bg-light text-center">
            <div class="row align-items-center justify-content-between">
                <div class="col-md-8 text-md-start mb-2 mb-md-0">
                    <h3 class="h6 fw-bold text-dark mb-1"><i class="bi bi-question-circle me-1 text-primary"></i> Need assistance with proposal submission or evaluation?</h3>
                    <p class="text-muted small mb-0">Browse our institutional documentation, evaluation rubrics, or contact the Directorate Helpdesk.</p>
                </div>
                <div class="col-md-4 text-md-end">
                    <a href="{{ route('support') }}" class="btn btn-outline-dark btn-sm fw-semibold me-2">
                        <i class="bi bi-life-preserver me-1"></i> Support Helpdesk
                    </a>
                    <a href="{{ route('lifecycle') }}" class="btn btn-dark btn-sm fw-semibold">
                        <i class="bi bi-book me-1"></i> Guidelines
                    </a>
                </div>
            </div>
        </section>

    </div>
</div>
@endsection
