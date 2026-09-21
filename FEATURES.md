# Gambella University Research Project Management System (GMU-RPMS)

## Comprehensive Master Feature Specification & Technical Architecture

> **Institution**: Gambella University (GMU), Ethiopia  
> **Platform**: Research Project Management & Governance System (RPMS)  
> **Development Context**: Practical Attachment / Senior Capstone Project  
> **Lead Developer**: Gemachis Tesfaye  
> **Release Version**: 2.0.0-PROD-READY

---

## Master Feature Index

1. [Executive Summary & Core Metrics](#1-executive-summary--core-metrics)
2. [11 Core System Roles & Privilege Matrix](#2-11-core-system-roles--privilege-matrix)
3. [End-to-End Academic Governance Lifecycle](#3-end-to-end-academic-governance-lifecycle)
    - [3.1 Proposal Creation & PI Draft Staging](#31-proposal-creation--pi-draft-staging)
    - [3.2 Department Head (DH) Quality Screening](#32-department-head-dh-quality-screening)
    - [3.3 Double-Blind Peer Review & Examiner Masking Engine](#33-double-blind-peer-review--examiner-masking-engine)
    - [3.4 Standardized 100-Point Evaluation Rubric](#34-standardized-100-point-evaluation-rubric)
    - [3.5 Dual-Threshold Financial Governance (<500k vs ≥500k ETB)](#35-dual-threshold-financial-governance-500k-vs-500k-etb)
    - [3.6 IRERC Ethics Protocol Review & Clearance Codes](#36-irerc-ethics-protocol-review--clearance-codes)
    - [3.7 Sequential Digital Grant Contract Signing & Gatekeeper](#37-sequential-digital-grant-contract-signing--gatekeeper)
    - [3.8 Milestone Progress Auditing & Auto-Tranche Generation](#38-milestone-progress-auditing--auto-tranche-generation)
    - [3.9 Dual Deliverable Upload (File Attachment + Repository URL)](#39-dual-deliverable-upload-file-attachment--repository-url)
    - [3.10 Finance 3-Stage Tranche Disbursement Pipeline](#310-finance-3-stage-tranche-disbursement-pipeline)
    - [3.11 Project Completion & Digital Certificate Engine](#311-project-completion--digital-certificate-engine)
4. [Operational & Lifecycle Support Modules](#4-operational--lifecycle-support-modules)
    - [4.1 Time Extension Governance (Strict 3-Cap Limit)](#41-time-extension-governance-strict-3-cap-limit)
    - [4.2 Budget Amendment & Price Inflation Justification](#42-budget-amendment--price-inflation-justification)
    - [4.3 Principal Investigator (PI) Transfer Protocol](#43-principal-investigator-pi-transfer-protocol)
    - [4.4 Emergency Project Termination Governance](#44-emergency-project-termination-governance)
    - [4.5 Laboratory & Consumables Procurement Tracker](#45-laboratory--consumables-procurement-tracker)
    - [4.6 Co-Researcher & Team Collaboration Management](#46-co-researcher--team-collaboration-management)
5. [System Administration & Institutional Tools](#5-system-administration--institutional-tools)
    - [5.1 College & Department Structural Management](#51-college--department-structural-management)
    - [5.2 Thematic Priority Research Area Management](#52-thematic-priority-research-area-management)
    - [5.3 Enterprise HRMS User Synchronization](#53-enterprise-hrms-user-synchronization)
    - [5.4 Tamper-Evident Audit Logging & CSV Export](#54-tamper-evident-audit-logging--csv-export)
    - [5.5 Admin Password Policy & Reset Portal](#55-admin-password-policy--reset-portal)
6. [Security, Authentication & Data Protection](#6-security-authentication--data-protection)
    - [6.1 Brute-Force Lockout Defense (5 Attempts / 15-Min Lock)](#61-brute-force-lockout-defense-5-attempts--15-min-lock)
    - [6.2 Anti-Tamper Blind Review Security](#62-anti-tamper-blind-review-security)
    - [6.3 Foreign Key & Transactional Database Integrity](#63-foreign-key--transactional-database-integrity)
    - [6.4 Automated Test Suite (13/13 Tests Passing)](#64-automated-test-suite-1313-tests-passing)
7. [User Experience, UI Theme & Mobile Responsiveness](#7-user-experience-ui-theme--mobile-responsiveness)
    - [7.1 Sleek Dark & Gambella Forest Green Aesthetic](#71-sleek-dark--gambella-forest-green-aesthetic)
    - [7.2 Real-Time Notifications Center (SCR-17)](#72-real-time-notifications-center-scr-17)
    - [7.3 Interactive Confirmation Modal Framework](#73-interactive-confirmation-modal-framework)
    - [7.4 One-Click Demo Role Switcher](#74-one-click-demo-role-switcher)
    - [7.5 Mobile-First Responsive Design & Offcanvas Drawer](#75-mobile-first-responsive-design--offcanvas-drawer)
    - [7.6 Interactive Radial SVG Progress & Milestone Timeline](#76-interactive-radial-svg-progress--milestone-timeline)
8. [Project Credits & Institutional Attribution](#8-project-credits--institutional-attribution)

---

## 1. Executive Summary & Core Metrics

The **Gambella University Research Project Management System (GMU-RPMS)** is an institutional enterprise web platform engineered to eliminate paper bottlenecks, prevent research fraud, enforce multi-tier accountability, and track research fund expenditures.

```
Total Dedicated Roles        : 11 Roles
Registered System Routes     : 99 Secure Routes
Blade View Templates         : 39 Responsive Views
Automated Unit/Feature Tests : 13/13 Passing (100% Green)
Standard Color Palette       : Charcoal Dark (#212529), Forest Green (#198754), Gold (#D4AF37)
Supported Devices            : Mobile (320px+), Tablet, Laptop, Desktop
```

---

## 2. 11 Core System Roles & Privilege Matrix

|   Role Code   | Role Name                       | Access Scope & Primary Permissions                                                                                                                                             |
| :-----------: | :------------------------------ | :----------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
|     `pi`      | **Principal Investigator**      | Create/edit/delete draft proposals, submit for screening, sign contracts, submit progress with file/link, request extensions (max 3), request amendments, request PI transfer. |
|     `tm`      | **Team Member (Co-Researcher)** | View assigned team projects, submit milestone progress reports with files/links, track research status.                                                                        |
|     `dh`      | **Department Head**             | Screen proposals submitted in their department, approve to Coordinator or return for revision, view department research progress.                                              |
| `coordinator` | **Research Coordinator**        | Assign blind peer reviewers, dispatch ethics reviews, audit and approve milestone progress, issue digital completion certificates, approve 1st & 2nd extensions.               |
|  `reviewer`   | **Peer Reviewer / Examiner**    | View anonymized proposals with masked PI identity, submit 100-point rubric scores, provide constructive evaluation comments.                                                   |
|    `dean`     | **College Dean**                | Review and ratify budget requests for proposals under 500,000 ETB, monitor college research activity.                                                                          |
|    `irerc`    | **IRERC Committee Chair**       | Review ethical protocols, assign risk classifications (Low, Medium, High, Exempt), issue official ethical clearance codes.                                                     |
|  `vparttcs`   | **Vice President (ARTTCS)**     | Co-sign institutional grant contracts, oversee university research output, approve terminations.                                                                               |
|    `rcsc`     | **RCSC / University President** | Review and ratify high-budget proposals (≥ 500,000 ETB), approve 3rd time extensions, ratify budget inflation amendments.                                                      |
|   `finance`   | **Finance Office**              | Disburse 3-stage budget tranches (30% Advance, 40% Mid-Term, 30% Final), record CBE voucher numbers and payment methods.                                                       |
|    `admin`    | **System Administrator**        | Manage system users, academic colleges, departments, thematic research areas, HRMS synchronization, audit logs, and secure password resets.                                    |

---

## 3. End-to-End Academic Governance Lifecycle

### 3.1 Proposal Creation & PI Draft Staging

- Structured proposal builder capturing research title, detailed abstract, thematic priority area, department affiliation, and requested budget (ETB).
- File upload support for formal proposal documents (`.pdf`, `.doc`, `.docx`).
- Draft staging allows PIs to iterate on proposals prior to final submission.
- One-click deletion and edit capabilities restricted to draft state.

### 3.2 Department Head (DH) Quality Screening

- Proposals submitted by PIs are routed to their respective Department Head.
- DH verifies academic quality, alignment with department goals, and resource feasibility.
- **Actions**: Approve (forwards to Coordinator Hub) or Return with revision feedback.

### 3.3 Double-Blind Peer Review & Examiner Masking Engine

- Coordinators assign qualified external or internal reviewers from an examiner directory.
- **Algorithmic Anonymization (`BlindReviewService`)**:
    - Automatically strips PI name, staff ID, email, and departmental markers.
    - Generates a deterministic masked tag (e.g. `PI-MASK-A1B2`).
    - Examiners only see sanitized metadata to ensure unbiased scoring.

### 3.4 Standardized 100-Point Evaluation Rubric

Reviewers evaluate proposals against five standardized academic dimensions:

1. **Methodology & Research Design** (25 Points)
2. **Background & Literature Review** (20 Points)
3. **Work Plan & Feasibility** (20 Points)
4. **Regional Relevance & Community Impact** (20 Points)
5. **Budget Justification & Cost Effectiveness** (15 Points)

**Decision Engine**:

- `Accepted`: Advances proposal to Financial Ratification.
- `Accepted with Minor Modifications`: Advances with reviewer remarks.
- `Accepted with Major Modifications`: Automatically returns proposal to PI for required changes.
- `Rejected`: Formal rejection with consolidated reviewer critique.

### 3.5 Dual-Threshold Financial Governance (<500k vs ≥500k ETB)

The system enforces statutory university spending thresholds:

- **Tier 1 (< 500,000.00 ETB)**: Routed directly to **College Dean Approvals**.
- **Tier 2 (≥ 500,000.00 ETB)**: Routed to **RCSC Portal (Research & Community Service Council / President)**.
- Ratified budget records initial approved budget and queues Tranche 1 in Finance.

### 3.6 IRERC Ethics Protocol Review & Clearance Codes

- Coordinators route eligible human, animal, or environmental protocols to the **Institutional Research Ethics Review Committee (IRERC)**.
- Ethics panel assigns a risk category: **Low Risk**, **Medium Risk**, **High Risk**, or **Exempt**.
- Issues official, tamper-evident digital clearance codes formatted as `GMU-IRERC-YYYY-XXXX`.

### 3.7 Sequential Digital Grant Contract Signing & Gatekeeper

- **Precondition Gate**: Contract view and signing actions are hard-blocked until IRERC clearance is approved.
- **Two-Party Sequential Execution**:
    1. Principal Investigator (PI) signs first with a digital agreement confirmation.
    2. Vice President (ARTTCS) signs second, ratifying the contract.
- Upon VP signing, the project automatically transitions to **`Active`** status.

### 3.8 Milestone Progress Auditing & Auto-Tranche Generation

- Interactive percentage slider (0% to 100%) for submitting milestone progress.
- Progress summary narrative describing deliverables, challenges, and fieldwork outcomes.
- **Automated Tranche Triggers**:
    - Reaching **40%+ audited progress** automatically creates and queues **Tranche 2 (40% Mid-Term)** in Finance.
    - Reaching **100% verified completion** automatically creates and queues **Tranche 3 (30% Final Release)** in Finance.
- Audit & approval controls strictly restricted to **Research Coordinator** and **Admin**.

### 3.9 Dual Deliverable Upload (File Attachment + Repository URL)

- Supports flexible deliverable verification:
    - **Document File Upload**: Upload PDF reports, datasets, Word docs, or ZIP archives (up to 20MB).
    - **External URL Link**: Link to external GitHub repositories, Google Drive folders, Zenodo/Dryad datasets, or published journal articles.
    - Fully optional & independent: researchers can attach either, both, or narrative text.

### 3.10 Finance 3-Stage Tranche Disbursement Pipeline

Sequential disbursement structure guaranteeing fund release tied to real deliverables:

- **Tranche 1 (30% Advance)**: Disbursed immediately upon contract activation to begin procurement and fieldwork.
- **Tranche 2 (40% Mid-Term)**: Disbursed upon Coordinator approval of 40%+ milestone progress.
- **Tranche 3 (30% Final Release)**: Disbursed upon 100% project completion audit.
- Records CBE direct deposit, cheque number, internal voucher code, and disbursement date/time.

### 3.11 Project Completion & Digital Certificate Engine

- Projects reaching 100% milestone progress and complete financial liquidation transition to **`Completed`**.
- Coordinator / Admin issues formal **Digital Completion Certificates** and **Journal Publication Award Letters**.
- Auto-generates verifiable certificate serial numbers (`GMU-CERT-YYYY-XXXX`).
- One-click in-browser certificate viewer and direct PDF download.

---

## 4. Operational & Lifecycle Support Modules

### 4.1 Time Extension Governance (Strict 3-Cap Limit)

- PI requests 1–6 month extensions with detailed fieldwork delay justifications.
- **Extensions 1 & 2**: Evaluated and approved by Research Coordinator.
- **Extension 3**: Requires RCSC Council ratification.
- **Hard Cap of 3**: The system hard-blocks any 4th extension request on database constraint and UI levels.

### 4.2 Budget Amendment & Price Inflation Justification

- PIs submit requests for supplementary budget adjustments due to documented material price inflation.
- Mandatory justification field requiring evidence of market price shifts.
- Reviewed, approved, and ratified exclusively by RCSC.

### 4.3 Principal Investigator (PI) Transfer Protocol

- Facilitates smooth transfer of research project leadership in cases of PI sabbatical, relocation, or health leave.
- Designates a new qualified PI from the department with Department Head and Coordinator approval.

### 4.4 Emergency Project Termination Governance

- Multi-tier termination workflows for non-performing or discontinued research.
- Automatically freezes future tranche releases and archives project files.

### 4.5 Laboratory & Consumables Procurement Tracker

- Dedicated purchase request pipeline for specialized laboratory consumables, field sensors, reagents, and hardware.
- Tracks item status from `Submitted` to `Approved` to `Purchased`.

### 4.6 Co-Researcher & Team Collaboration Management

- PIs can invite and manage co-researchers and laboratory technicians.
- Team members receive scoped access to view project details and submit progress reports.

---

## 5. System Administration & Institutional Tools

### 5.1 College & Department Structural Management

- Dynamic hierarchy management for academic colleges and constituent departments.
- Automatic cascading relationships for screening and departmental analytics.

### 5.2 Thematic Priority Research Area Management

- Priority themes categorized into Agriculture & Environment, Technology Transfer, Health, and Social Sciences.
- Allows enabling or disabling thematic calls for proposals.

### 5.3 Enterprise HRMS User Synchronization

- Simulates automated staff profile, department affiliation, and academic rank updates from university HRMS.

### 5.4 Tamper-Evident Audit Logging & CSV Export

- Comprehensive immutable audit trail capturing user ID, event type, project ID, IP address, and timestamp.
- Date range filtering and one-click CSV export download.

### 5.5 Admin Password Policy & Reset Portal

- Secure administrative portal for resetting forgotten staff passwords.
- Enforces strict complexity: minimum 8 characters, uppercase, lowercase, numbers, and symbols.

---

## 6. Security, Authentication & Data Protection

### 6.1 Brute-Force Lockout Defense (5 Attempts / 15-Min Lock)

- Integrated `RateLimiter` security monitoring failed login attempts per email/IP.
- Locks account access for 15 minutes after 5 consecutive failed attempts.

### 6.2 Anti-Tamper Blind Review Security

- Examiner views sanitize PI identifiers at the query and template layer.
- Prevents tampering with URL IDs to inspect author identities.

### 6.3 Foreign Key & Transactional Database Integrity

- Strict SQLite / MySQL foreign key cascades, unique constraints, and atomic database transactions.

### 6.4 Automated Test Suite (13/13 Tests Passing)

- **`RPMSWorkflowTest`**: 7 end-to-end integration tests validating peer review masking, dual threshold routing, contract execution, certificates, and tranches.
- **`ForgotPasswordTest`**: 6 security tests validating rate-limiting lockout and password complexity.

---

## 7. User Experience, UI Theme & Mobile Responsiveness

### 7.1 Sleek Dark & Gambella Forest Green Aesthetic

- Modern dark charcoal (`#212529` / `#0f172a`) framing combined with Gambella Forest Green (`#198754` / `#0f3e2e`) and Gold (`#D4AF37`) accents.
- Zero loud neon or unstyled components.

### 7.2 Real-Time Notifications Center (SCR-17)

- Navbar bell with badge counts alerting users to pending reviews, approvals, and contract requests.

### 7.3 Interactive Confirmation Modal Framework

- Custom `confirm-btn` system replacing default browser popups with beautiful Bootstrap confirmation modals.

### 7.4 One-Click Demo Role Switcher

- Instant dropdown switcher allowing prototype evaluators to test all 11 system roles without logging out.

### 7.5 Mobile-First Responsive Design & Offcanvas Drawer

- Offcanvas slide-out menu drawer on tablet and mobile viewports (< 992px).
- Horizontal momentum touch scrolling for milestone progress timelines.
- Fluid table wrappers (`.table-responsive`) preventing horizontal page clipping.
- Responsive modal bounds (`max-width: 95vw`).

### 7.6 Interactive Radial SVG Progress & Milestone Timeline

- Mathematical SVG `stroke-dasharray` radial charts calculating milestone completion dynamically.

---

## 8. Project Credits & Institutional Attribution

- **Lead Developer & Author**: Gemachis Tesfaye
- **Institution**: Gambella University, Gambella, Ethiopia
- **Academic Context**: Practical Attachment / Senior Project Demonstration
- **System Architecture**: Enterprise Model-View-Controller (MVC) on Laravel Framework

---

_Gambella University Research Project Management System (GMU-RPMS) &copy; 2026. All Rights Reserved._
