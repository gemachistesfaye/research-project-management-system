# GMU-RPMS System Architecture

## 1. High-Level Overview
The Gambella University Research Project Management System (GMU-RPMS) is an enterprise-grade web application built on the **Laravel Framework** (PHP). It follows the **MVC (Model-View-Controller)** architectural pattern to ensure clean separation of concerns.

## 2. Core Architecture
- **Framework:** Laravel 8/9+
- **Database:** SQLite (Local/Development) -> MySQL/PostgreSQL ready (Production)
- **Frontend/UI:** Laravel Blade Templates + Bootstrap 5
- **Authentication:** Laravel built-in Session-based Authentication

## 3. Directory Structure
The application code is organized to maintain strict boundaries between data, logic, and presentation:

- **`app/Http/Controllers/` (The Managers)**: Handles routing logic, processes requests, and passes data to views.
- **`app/Models/` (Data Layer)**: Contains 19 Eloquent ORM models (e.g., `Project`, `User`, `BudgetRequest`) representing database tables.
- **`app/Services/` (Business Logic Layer)**: Extracts complex business logic out of controllers. Includes:
  - `BlindReviewService`: Handles double-blind peer review masking.
  - `BudgetWorkflowEngine`: Handles multi-tier financial governance routing.
  - `LifecycleChangeService`: Manages project state transitions.
- **`resources/views/` (Presentation Layer)**: Contains all `.blade.php` HTML templates, strictly grouped by feature (e.g., `admin/`, `governance/`, `finance/`).
- **`database/`**: Contains SQLite database, Migrations (table blueprints), and Seeders (default data injectors).
- **`routes/web.php`**: The central nervous system mapping URLs to Controllers, heavily protected by Role-Based Access Control (RBAC) middleware.

## 4. Security & Governance
- **Role-Based Access Control (RBAC):** Strict middleware (`role:pi`, `role:dean`, `role:finance`) blocks unauthorized access at the routing level.
- **Data Protection:** All file uploads (Proposals) are hashed and securely stored in `storage/app/public/proposals/`.

## 5. Deployment Readiness
The system includes configuration for containerized cloud deployment:
- `Dockerfile` (PHP-FPM + Nginx)
- `entrypoint.sh` (Database migration & caching automation)
- `render.yaml` (Cloud hosting configuration)
