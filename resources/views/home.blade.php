@extends('layouts.app')

@section('styles')
<style>
    .shadow-xs {
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05);
    }
    .hover-lift {
        transition: transform 0.22s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.22s cubic-bezier(0.16, 1, 0.3, 1), border-color 0.22s ease;
    }
    .hover-lift:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(15, 62, 46, 0.09) !important;
        border-color: #cbd5e1 !important;
    }
    .step-number {
        width: 38px;
        height: 38px;
        font-weight: 700;
        font-size: 0.95rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        background: #f1f5f9;
        color: #0f3e2e;
        border: 2px solid #e2e8f0;
    }
    .stat-card {
        border-radius: 12px;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        padding: 1rem;
        transition: all 0.2s ease;
    }
    .stat-card:hover {
        border-color: #0f3e2e;
        box-shadow: 0 4px 12px rgba(15, 62, 46, 0.06);
    }
    .governance-card {
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        background: #ffffff;
        transition: all 0.2s ease;
    }
    .governance-card:hover {
        border-color: #94a3b8;
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.04);
    }
</style>
@endsection

@section('content')
<div class="py-3 py-lg-4">
    <div class="container">

        {{-- ======================================================================
             HERO SECTION: High-Impact Institutional Header & Actions
             ====================================================================== --}}
        <section class="text-center pb-3 pb-md-4 mb-3">
            {{-- Institutional Eyebrow Tag --}}
            <div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill bg-white border shadow-xs mb-3">
                <span class="d-inline-block rounded-circle bg-success" style="width: 8px; height: 8px;"></span>
                <span class="text-uppercase fw-bold text-muted" style="font-size: 0.76rem; letter-spacing: 1.2px;">
                    Research &amp; Technology Transfer Directorate
                </span>
            </div>

            {{-- Main Title --}}
            <h1 class="display-6 fw-bold text-dark mb-2 mb-md-3" style="font-size: calc(1.4rem + 1.2vw); letter-spacing: -0.025em;">
                Research Project Management System
            </h1>

            {{-- Lead Subtitle: Responsive Length (Full on Desktop, Concise on Mobile) --}}
            <p class="lead text-muted mb-4 mx-auto d-none d-md-block" style="max-width: 740px; font-size: 1.12rem; line-height: 1.6;">
                A centralized institutional portal for authoring research proposals, double-blind peer review, dual-threshold financial governance, and verifiable milestone grant disbursements.
            </p>
            <p class="text-muted mb-3 mx-auto d-block d-md-none" style="max-width: 440px; font-size: 0.92rem; line-height: 1.45;">
                Centralized institutional portal for research proposals, double-blind peer review, and milestone grant funding.
            </p>

            {{-- Desktop Actions (Horizontal Cluster with Icons) --}}
            <div class="d-none d-sm-flex flex-wrap justify-content-center gap-2 gap-md-3 mb-4">
                @auth
                    <a href="{{ route('dashboard') }}" class="btn btn-dark px-4 py-2 fw-semibold shadow-sm">
                        <i class="bi bi-speedometer2 me-2"></i> Open Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-dark px-4 py-2 fw-semibold shadow-sm">
                        <i class="bi bi-box-arrow-in-right me-2"></i> Sign In to Portal
                    </a>
                @endauth
                <a href="{{ route('research.catalog') }}" class="btn btn-outline-dark px-3 py-2 fw-semibold">
                    <i class="bi bi-journal-text me-1 text-primary"></i> Research Catalog
                </a>
                <a href="{{ route('lifecycle') }}" class="btn btn-outline-dark px-3 py-2 fw-semibold">
                    <i class="bi bi-diagram-3 me-1"></i> Lifecycle &amp; Rubric
                </a>
                <a href="{{ route('support') }}" class="btn btn-outline-secondary px-3 py-2 fw-semibold">
                    <i class="bi bi-headset me-1"></i> Helpdesk
                </a>
            </div>

            {{-- Mobile Actions (Optimized 2-by-1 Stack: Primary on top, 3 secondary below) --}}
            <div class="d-sm-none mb-3 mx-auto" style="max-width: 380px;">
                <div class="row g-2">
                    <div class="col-12">
                        @auth
                            <a href="{{ route('dashboard') }}" class="btn btn-dark w-100 py-2 fw-semibold shadow-sm">
                                <i class="bi bi-speedometer2 me-1"></i> Open Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-dark w-100 py-2 fw-semibold shadow-sm">
                                <i class="bi bi-box-arrow-in-right me-1"></i> Sign In to Portal
                            </a>
                        @endauth
                    </div>
                    <div class="col-4">
                        <a href="{{ route('research.catalog') }}" class="btn btn-outline-dark w-100 py-2 fw-semibold text-truncate small" style="font-size: 0.78rem;">
                            <i class="bi bi-journal-text d-block mb-1"></i> Catalog
                        </a>
                    </div>
                    <div class="col-4">
                        <a href="{{ route('lifecycle') }}" class="btn btn-outline-dark w-100 py-2 fw-semibold text-truncate small" style="font-size: 0.78rem;">
                            <i class="bi bi-diagram-3 d-block mb-1"></i> Lifecycle
                        </a>
                    </div>
                    <div class="col-4">
                        <a href="{{ route('support') }}" class="btn btn-outline-secondary w-100 py-2 fw-semibold text-truncate small" style="font-size: 0.78rem;">
                            <i class="bi bi-headset d-block mb-1"></i> Support
                        </a>
                    </div>
                </div>
            </div>

            {{-- ======================================================================
                 4 KEY INSTITUTIONAL METRICS (Responsive 2x2 on Mobile, 4-in-a-row on Desktop)
                 ====================================================================== --}}
            <div class="row g-2 g-md-3 justify-content-center pt-2">
                <div class="col-6 col-md-3">
                    <div class="stat-card text-center shadow-xs h-100">
                        <div class="display-6 fw-bold text-dark mb-0" style="font-size: calc(1.3rem + 0.8vw);">
                            {{ $stats['projects'] ?? 4 }}
                        </div>
                        <div class="fw-bold text-muted text-uppercase mt-1" style="font-size: 0.72rem; letter-spacing: 0.5px;">
                            <i class="bi bi-folder-check text-dark me-1"></i>
                            <span class="d-none d-md-inline">Proposals in Intake</span>
                            <span class="d-inline d-md-none">Proposals</span>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stat-card text-center shadow-xs h-100">
                        <div class="display-6 fw-bold text-dark mb-0" style="font-size: calc(1.3rem + 0.8vw);">
                            {{ $stats['thematics'] ?? 3 }}
                        </div>
                        <div class="fw-bold text-muted text-uppercase mt-1" style="font-size: 0.72rem; letter-spacing: 0.5px;">
                            <i class="bi bi-compass text-dark me-1"></i>
                            <span class="d-none d-md-inline">Priority Themes</span>
                            <span class="d-inline d-md-none">Themes</span>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stat-card text-center shadow-xs h-100">
                        <div class="display-6 fw-bold text-dark mb-0" style="font-size: calc(1.3rem + 0.8vw);">
                            {{ $stats['colleges'] ?? 3 }}
                        </div>
                        <div class="fw-bold text-muted text-uppercase mt-1" style="font-size: 0.72rem; letter-spacing: 0.5px;">
                            <i class="bi bi-buildings text-dark me-1"></i>
                            <span class="d-none d-md-inline">Colleges &amp; Faculties</span>
                            <span class="d-inline d-md-none">Colleges</span>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stat-card text-center shadow-xs h-100">
                        <div class="display-6 fw-bold text-dark mb-0" style="font-size: calc(1.3rem + 0.8vw);">
                            {{ $stats['papers'] ?? 0 }}
                        </div>
                        <div class="fw-bold text-muted text-uppercase mt-1" style="font-size: 0.72rem; letter-spacing: 0.5px;">
                            <i class="bi bi-patch-check text-dark me-1"></i>
                            <span class="d-none d-md-inline">Published Papers</span>
                            <span class="d-inline d-md-none">Papers</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- ======================================================================
             SECTION 1: ACTIVE GRANT CALLS (Surfaced when calls are open)
             ====================================================================== --}}
        @if(isset($activeCalls) && $activeCalls->count() > 0)
        <section class="py-3 py-md-4 border-top">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3">
                <div>
                    <span class="badge bg-danger text-white px-2 py-1 mb-1" style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.5px;">
                        <i class="bi bi-broadcast me-1"></i> Open Calls
                    </span>
                    <h2 class="h5 fw-bold text-dark mb-0">Active Institutional Research Grant Calls</h2>
                </div>
                <span class="text-muted small mt-1 mt-md-0">
                    Submit proposals online before the designated intake deadlines.
                </span>
            </div>

            <div class="row g-2 g-md-3">
                @foreach($activeCalls as $call)
                    <div class="col-12 col-md-4">
                        <div class="card h-100 border rounded-3 p-3 bg-white shadow-xs d-flex flex-column justify-content-between hover-lift">
                            <div>
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <span class="badge bg-dark fw-normal" style="font-size: 0.72rem;">{{ $call->call_type ?? 'Annual Call' }}</span>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle fw-semibold" style="font-size: 0.7rem;">
                                        Open
                                    </span>
                                </div>
                                <h3 class="h6 fw-bold text-dark mb-2" style="line-height: 1.4;">{{ $call->title }}</h3>
                                <p class="text-muted small mb-3" style="font-size: 0.84rem; line-height: 1.45;">
                                    {{ Str::limit($call->description, 130) }}
                                </p>
                            </div>
                            <div class="pt-2 border-top text-muted small d-flex align-items-center justify-content-between" style="font-size: 0.78rem;">
                                <span><i class="bi bi-calendar-event me-1"></i> Deadline:</span>
                                <span class="fw-bold text-dark">{{ $call->deadline ? \Carbon\Carbon::parse($call->deadline)->format('M d, Y') : 'Open Intake' }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
        @endif

        {{-- ======================================================================
             SECTION 2: 4-STAGE RESEARCH LIFECYCLE BANNER
             ====================================================================== --}}
        <section class="py-3 py-md-4 border-top">
            <div class="text-center mb-3">
                <span class="text-uppercase fw-bold text-muted small" style="letter-spacing: 1px; font-size: 0.74rem;">Institutional Process</span>
                <h2 class="h5 fw-bold text-dark mb-1">End-to-End Governance Lifecycle</h2>
            </div>
            <div class="row g-2 g-md-3 text-center">
                <div class="col-6 col-md-3">
                    <div class="p-3 rounded-3 bg-white border h-100 shadow-xs hover-lift">
                        <div class="step-number mb-2">1</div>
                        <h3 class="fw-bold text-dark mb-1" style="font-size: 0.88rem;">Intake &amp; DH Screening</h3>
                        <p class="text-muted small mb-0" style="font-size: 0.78rem;">PI proposal submission with departmental alignment verification.</p>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="p-3 rounded-3 bg-white border h-100 shadow-xs hover-lift">
                        <div class="step-number mb-2">2</div>
                        <h3 class="fw-bold text-dark mb-1" style="font-size: 0.88rem;">Double-Blind Review</h3>
                        <p class="text-muted small mb-0" style="font-size: 0.78rem;">Independent masked peer evaluations scored on objective rubrics.</p>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="p-3 rounded-3 bg-white border h-100 shadow-xs hover-lift">
                        <div class="step-number mb-2">3</div>
                        <h3 class="fw-bold text-dark mb-1" style="font-size: 0.88rem;">Dual-Tier Approval</h3>
                        <p class="text-muted small mb-0" style="font-size: 0.78rem;">Dean approval (&lt;500k ETB) or RCSC/VP Council ratification (&ge;500k ETB).</p>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="p-3 rounded-3 bg-white border h-100 shadow-xs hover-lift">
                        <div class="step-number mb-2">4</div>
                        <h3 class="fw-bold text-dark mb-1" style="font-size: 0.88rem;">Milestone Funding</h3>
                        <p class="text-muted small mb-0" style="font-size: 0.78rem;">Audited disbursements released in 3 tranches: 30% &bull; 40% &bull; 30%.</p>
                    </div>
                </div>
            </div>
        </section>

        {{-- ======================================================================
             SECTION 3: PRIORITY RESEARCH THEMATIC AREAS
             ====================================================================== --}}
        <section class="py-3 py-md-4 border-top">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end mb-3">
                <div>
                    <h2 class="h5 fw-bold text-dark mb-1">Priority Research Thematic Areas</h2>
                    <p class="text-muted small mb-0">Current institutional grant call priorities for academic faculty proposals:</p>
                </div>
                <span class="badge bg-light text-dark border px-3 py-2 mt-2 mt-md-0 align-self-start align-self-md-auto" style="font-size: 0.78rem;">
                    <i class="bi bi-check-circle-fill text-dark me-1"></i> Active Funding Cycle 2026/2027
                </span>
            </div>

            <div class="row g-2 g-md-3">
                @forelse($thematicAreas as $ta)
                    <div class="col-12 col-md-4">
                        <div class="card h-100 border rounded-3 p-3 p-md-4 bg-white shadow-xs d-flex flex-column justify-content-between hover-lift">
                            <div>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="badge bg-dark fw-semibold" style="font-size: 0.72rem;">
                                        {{ $ta->category ?? 'Research Priority' }}
                                    </span>
                                    <span class="text-muted small" style="font-size: 0.72rem;">Theme #{{ $ta->id }}</span>
                                </div>
                                <h3 class="h6 fw-bold text-dark mb-2" style="font-size: 1.02rem; line-height: 1.4;">
                                    {{ $ta->title }}
                                </h3>
                                <p class="text-muted mb-3" style="font-size: 0.86rem; line-height: 1.5;">
                                    {{ $ta->description }}
                                </p>
                            </div>
                            <div class="pt-2 border-top text-muted small d-flex align-items-center justify-content-between" style="font-size: 0.78rem;">
                                <span><i class="bi bi-tag me-1"></i> Thematic Grant</span>
                                <span class="fw-bold text-success"><i class="bi bi-circle-fill me-1" style="font-size: 0.5rem;"></i>Open for Intake</span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="p-4 rounded-3 bg-white border text-center text-muted shadow-xs">
                            <i class="bi bi-compass fs-2 text-muted mb-2 d-block"></i>
                            <h4 class="h6 fw-bold text-dark mb-1">Institutional Priority Areas</h4>
                            <p class="small text-muted mb-0">Priority thematic grant areas open for faculty proposals will appear here.</p>
                        </div>
                    </div>
                @endforelse
            </div>
        </section>

        {{-- ======================================================================
             SECTION 4: CORE GOVERNANCE PRINCIPLES (The 3 Pillars)
             ====================================================================== --}}
        <section class="py-3 py-md-4 border-top">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end mb-3">
                <div>
                    <h2 class="h5 fw-bold text-dark mb-1">Institutional Research Governance Principles</h2>
                    <p class="text-muted small mb-0">Core standards enforced systematically across all proposals and grant projects:</p>
                </div>
            </div>
            <div class="row g-2 g-md-3">
                <div class="col-12 col-md-4">
                    <div class="governance-card h-100 p-3 shadow-xs">
                        <div class="fw-bold text-dark mb-2 fs-6 d-flex align-items-center">
                            <i class="bi bi-shield-lock-fill text-dark me-2 fs-5"></i>
                            <span>Double-Blind Integrity</span>
                        </div>
                        <p class="text-muted small mb-0 lh-sm d-none d-md-block" style="line-height: 1.5;">
                            Author identities and affiliations are masked dynamically throughout academic peer review to guarantee 100% impartial, merit-first evaluation.
                        </p>
                        <p class="text-muted small mb-0 lh-sm d-block d-md-none" style="font-size: 0.82rem;">
                            Masked author identities ensure 100% impartial, merit-first review.
                        </p>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="governance-card h-100 p-3 shadow-xs">
                        <div class="fw-bold text-dark mb-2 fs-6 d-flex align-items-center">
                            <i class="bi bi-sliders text-dark me-2 fs-5"></i>
                            <span>Dual-Tier Governance</span>
                        </div>
                        <p class="text-muted small mb-0 lh-sm d-none d-md-block" style="line-height: 1.5;">
                            Proposals under ETB 500k are expedited through College Deans, while major institutional grants (&ge;500k) receive formal RCSC Council review.
                        </p>
                        <p class="text-muted small mb-0 lh-sm d-block d-md-none" style="font-size: 0.82rem;">
                            &lt;500k ETB via Deans; &ge;500k reviewed by RCSC Council.
                        </p>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="governance-card h-100 p-3 shadow-xs">
                        <div class="fw-bold text-dark mb-2 fs-6 d-flex align-items-center">
                            <i class="bi bi-cash-stack text-dark me-2 fs-5"></i>
                            <span>Tranche Disbursements</span>
                        </div>
                        <p class="text-muted small mb-0 lh-sm d-none d-md-block" style="line-height: 1.5;">
                            Grant funds are disbursed in 3 structured tranches (30% Advance, 40% Mid-Term, 30% Final) strictly tied to verifiable progress milestones.
                        </p>
                        <p class="text-muted small mb-0 lh-sm d-block d-md-none" style="font-size: 0.82rem;">
                            3 milestone tranches: 30% advance, 40% mid-term, 30% final.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        {{-- ======================================================================
             SECTION 5: ROLE-BASED WORKFLOW NAVIGATION
             ====================================================================== --}}
        <section class="pt-3 pt-md-4 pb-2 border-top">
            <div class="text-center mb-3">
                <span class="text-uppercase fw-bold text-muted small" style="letter-spacing: 1px; font-size: 0.74rem;">User Pathways</span>
                <h2 class="h5 fw-bold text-dark mb-1">Tailored Portals for Every Academic Role</h2>
            </div>
            <div class="row g-2 g-md-3">
                <div class="col-12 col-md-4">
                    <div class="card h-100 border rounded-3 p-3 bg-white shadow-xs hover-lift">
                        <div class="d-flex align-items-center mb-2">
                            <div class="rounded-circle bg-light p-2 me-2 text-dark"><i class="bi bi-person-workspace fs-5"></i></div>
                            <div>
                                <h3 class="h6 fw-bold mb-0 text-dark">Faculty &amp; Researchers</h3>
                                <span class="text-muted small" style="font-size: 0.74rem;">Principal Investigators</span>
                            </div>
                        </div>
                        <p class="text-muted small mb-3" style="font-size: 0.82rem; line-height: 1.45;">
                            Draft multi-investigator proposals, upload budgets, track anonymized reviewer comments, and claim milestone disbursements.
                        </p>
                        <div class="mt-auto">
                            <a href="{{ route('login') }}" class="btn btn-sm btn-outline-dark w-100 fw-semibold">
                                <i class="bi bi-arrow-right me-1"></i> Researcher Portal
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="card h-100 border rounded-3 p-3 bg-white shadow-xs hover-lift">
                        <div class="d-flex align-items-center mb-2">
                            <div class="rounded-circle bg-light p-2 me-2 text-dark"><i class="bi bi-pencil-square fs-5"></i></div>
                            <div>
                                <h3 class="h6 fw-bold mb-0 text-dark">Peer Evaluators</h3>
                                <span class="text-muted small" style="font-size: 0.74rem;">Double-Blind Reviewers</span>
                            </div>
                        </div>
                        <p class="text-muted small mb-3" style="font-size: 0.82rem; line-height: 1.45;">
                            Access blinded manuscripts, score 6-criterion evaluation rubrics, submit quantitative marks, and recommend revisions.
                        </p>
                        <div class="mt-auto">
                            <a href="{{ route('login') }}" class="btn btn-sm btn-outline-dark w-100 fw-semibold">
                                <i class="bi bi-arrow-right me-1"></i> Evaluator Portal
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="card h-100 border rounded-3 p-3 bg-white shadow-xs hover-lift">
                        <div class="d-flex align-items-center mb-2">
                            <div class="rounded-circle bg-light p-2 me-2 text-dark"><i class="bi bi-bank fs-5"></i></div>
                            <div>
                                <h3 class="h6 fw-bold mb-0 text-dark">Deans &amp; Directorate</h3>
                                <span class="text-muted small" style="font-size: 0.74rem;">Executive Governance</span>
                            </div>
                        </div>
                        <p class="text-muted small mb-3" style="font-size: 0.82rem; line-height: 1.45;">
                            Endorse departmental proposals, ratify institutional RCSC funding decisions, audit budget tranches, and oversee annual metrics.
                        </p>
                        <div class="mt-auto">
                            <a href="{{ route('login') }}" class="btn btn-sm btn-outline-dark w-100 fw-semibold">
                                <i class="bi bi-arrow-right me-1"></i> Directorate Access
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    </div>
</div>
@endsection
