# Research Project Management System

A full-featured web application for managing the complete lifecycle of academic research projects, from proposal submission to completion and certificate issuance. Built for Research Project Management System.

## Features

- **12 Role-Based Access Control (RBAC):** Principal Investigator, Team Member, Department Head, Coordinator, Reviewer, College Dean, IRERC Ethics Committee, VP Academic (ARTTCS), RCSC Chair, Finance Office, System Administrator
- **Workflow Engine:** Draft → Submitted → DH Screened → Under Review → Approved → Active → Completed
- **Blind Peer Review:** Anonymous double-blind evaluation with scoring rubrics
- **Dual-Threshold Financial Governance:** Dean (<500k ETB) and RCSC (≥500k ETB) approval tiers
- **Budget Management:** Tranche-based disbursement tracking with approval workflows
- **Progress Reports:** Quarterly/semi-annual/annual reporting with team member contributions
- **Contract Signing:** Digital contract generation and tracking
- **PDF Certificates:** Research Project Management System-branded completion certificates with PDF download
- **Project Lifecycle:** Extensions, amendments, PI transfers, and termination with refund calculations
- **Procurement Requests:** Equipment and supplies procurement tied to projects
- **Analytics Dashboard:** Real-time project statistics, budget charts, and departmental metrics
- **Audit Trail:** Complete action logging for compliance
- **Mobile Responsive:** Dark-themed, professional UI with collapsible sidebar on mobile

## Test Accounts / Demo Logins

If you have run the database seeders (`php artisan migrate --seed`), you can log in with any of the following demo accounts. **All accounts share the same password:** `UNI@Demo1`

| Role                               | Email Address                   |
| :--------------------------------- | :------------------------------ |
| **System Administrator**     | `admin@rpms.local`       |
| **Principal Investigator**   | `pi@rpms.local`          |
| **Team Member**              | `tm@rpms.local`          |
| **Department Head**          | `dh@rpms.local`          |
| **Research Coordinator**     | `coordinator@rpms.local` |
| **Blind Reviewer**           | `reviewer@rpms.local`    |
| **College Dean**             | `dean@rpms.local`        |
| **Ethics Committee (IRERC)** | `irerc@rpms.local`       |
| **VP Academic (ARTTCS)**     | `vp@rpms.local`          |
| **RCSC Chair**               | `rcsc@rpms.local`        |
| **Finance Office**           | `finance@rpms.local`     |

## Tech Stack

- **Backend:** Laravel 9 (PHP 8.1)
- **Web Server:** Nginx + PHP-FPM (production)
- **Caching:** OPcache for compiled PHP, file-based cache for application data
- **Database:** SQLite (production) / MySQL (local development)
- **Frontend:** Bootstrap 5.3, Blade Templates, Chart.js
- **PDF Generation:** barryvdh/laravel-dompdf
- **Containerization:** Docker

## Project Structure

```
├── app/
│   ├── Http/
│   │   ├── Controllers/    # Auth, Admin, Projects, Governance, Finance, etc.
│   │   └── Middleware/      # RBAC, TrustProxies, AuditLog
│   ├── Models/             # User, Project, Budget, Review, Certificate, etc.
│   └── Services/           # Business logic services
├── database/
│   ├── migrations/         # Database schema
│   └── seeders/            # DatabaseSeeder, RbacSeeder (roles, permissions, demo users)
├── public/
│   └── vendor/             # Bootstrap 5.3 CSS/JS (local assets)
├── resources/
│   └── views/
│       ├── layouts/        # app.blade.php (shared navbar, sidebar, footer)
│       ├── admin/          # Users, Colleges, Departments, Thematic Areas
│       ├── governance/     # Dean Approvals, RCSC Portal
│       ├── projects/       # Project CRUD, Show, Edit
│       └── ...
├── routes/
│   ├── web.php             # All web routes with RBAC middleware
│   └── api.php
├── nginx.conf              # Nginx config for production
├── Dockerfile              # PHP 8.1-FPM + Nginx
└── entrypoint.sh           # Container startup (migrate, seed, cache, launch)
```

## Local Installation

### Prerequisites

- PHP 8.1+
- Composer
- SQLite or MySQL

### Setup

```bash
git clone https://github.com/gemachistesfaye/research-project-management-system.git
cd research-project-management-system
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

Visit `http://localhost:8000`

## Environment Variables

Key variables in `.env`:

| Variable           | Description     | Default                   |
| ------------------ | --------------- | ------------------------- |
| `APP_KEY`        | Encryption key  | Pre-configured            |
| `APP_URL`        | Application URL | Auto-detected             |
| `APP_DEBUG`      | Debug mode      | `false` in production   |
| `DB_CONNECTION`  | Database driver | `sqlite`                |
| `DB_DATABASE`    | Database path   | `/data/database.sqlite` |
| `CACHE_DRIVER`   | Cache backend   | `file`                  |
| `SESSION_DRIVER` | Session storage | `file`                  |

## Docker Deployment

The application runs as a Docker container with **Nginx + PHP-FPM**:

```bash
docker build -t rpms .
docker run -p 8000:8000 -v data:/data rpms
```

The container handles automatically on startup:

1. Environment variable overrides for production
2. Database migration and seeding
3. Config, route, and view cache rebuild
4. Nginx and PHP-FPM launch

## RBAC Permissions

The system enforces 60+ granular permissions across 12 roles:

| Role                   | Key Permissions                                                       |
| ---------------------- | --------------------------------------------------------------------- |
| Principal Investigator | Create/manage own projects, submit progress reports                   |
| Team Member            | View assigned projects, submit contributions                          |
| Department Head        | Screen proposals within department                                    |
| Coordinator            | Assign reviewers, manage review workflow                              |
| Reviewer               | Conduct blind evaluations, submit scores                              |
| College Dean           | Approve/reject projects (<500k ETB), view college analytics           |
| IRERC Ethics Committee | Ethics review and approval                                            |
| VP Academic (ARTTCS)   | Final approval, amendment review, certificate issuance                |
| RCSC Chair             | Financial governance (≥500k ETB), budget approval                    |
| Finance Office         | Process disbursements, financial tracking                             |
| System Administrator   | Full access: users, colleges, departments, thematic areas, audit logs |

## License

MIT License
