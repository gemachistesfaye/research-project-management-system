<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Role;
use App\Models\Permission;
use App\Models\User;

class RbacSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['name' => 'pi', 'display_name' => 'Principal Investigator', 'description' => 'Lead researcher who creates and manages projects'],
            ['name' => 'tm', 'display_name' => 'Team Member', 'description' => 'Co-researcher or team member on projects'],
            ['name' => 'reviewer', 'display_name' => 'Reviewer', 'description' => 'Blind examiner who evaluates project proposals'],
            ['name' => 'dh', 'display_name' => 'Department Head', 'description' => 'Screens and approves/rejects proposals at department level'],
            ['name' => 'coordinator', 'display_name' => 'Research Coordinator', 'description' => 'Manages review assignments, procurement, certificates, and extensions'],
            ['name' => 'dean', 'display_name' => 'College Dean', 'description' => 'Approves budgets under the threshold and reviews at college level'],
            ['name' => 'irerc', 'display_name' => 'IRERC Chair', 'description' => 'Ethics review committee member who clears ethical compliance'],
            ['name' => 'vparttcs', 'display_name' => 'Vice President (ARTTCS)', 'description' => 'Senior leadership for contract signing, high-budget approvals, and extensions'],
            ['name' => 'rcsc', 'display_name' => 'RCSC Chair', 'description' => 'Research and Community Service Committee chair for high-budget and analytics'],
            ['name' => 'finance', 'display_name' => 'Finance Office', 'description' => 'Processes budget disbursements and financial transactions'],
            ['name' => 'admin', 'display_name' => 'System Administrator', 'description' => 'Manages users, thematic areas, audit logs, and system configuration'],
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role['name']], $role);
        }

        $permissions = [
            ['name' => 'view_dashboard', 'display_name' => 'View Dashboard', 'description' => 'Access the main dashboard'],
            ['name' => 'manage_profile', 'display_name' => 'Manage Profile', 'description' => 'View and update personal profile and password'],
            ['name' => 'create_project', 'display_name' => 'Create Project', 'description' => 'Submit new research project proposals'],
            ['name' => 'view_projects', 'display_name' => 'View Projects', 'description' => 'Access project listings and details'],
            ['name' => 'manage_team', 'display_name' => 'Manage Team Members', 'description' => 'Add, update, or remove team members on projects'],
            ['name' => 'request_extension', 'display_name' => 'Request Extension', 'description' => 'Request project timeline extensions'],
            ['name' => 'request_amendment', 'display_name' => 'Request Amendment', 'description' => 'Request budget amendments for projects'],
            ['name' => 'request_termination', 'display_name' => 'Request Termination', 'description' => 'Request project termination with refund calculation'],
            ['name' => 'request_pi_transfer', 'display_name' => 'Request PI Transfer', 'description' => 'Request transfer of PI responsibility to another user'],
            ['name' => 'submit_procurement', 'display_name' => 'Submit Procurement', 'description' => 'Submit purchase requests for project needs'],
            ['name' => 'sign_contract', 'display_name' => 'Sign Contract', 'description' => 'Sign project contracts as PI or VP'],
            ['name' => 'assign_reviewer', 'display_name' => 'Assign Reviewer', 'description' => 'Assign blind reviewers to project proposals'],
            ['name' => 'submit_evaluation', 'display_name' => 'Submit Evaluation', 'description' => 'Submit evaluation scores and decisions on assigned reviews'],
            ['name' => 'screen_proposals', 'display_name' => 'Screen Proposals', 'description' => 'Department head screening and approval/rejection of proposals'],
            ['name' => 'approve_budget', 'display_name' => 'Approve Budget', 'description' => 'Approve project budgets at various tiers'],
            ['name' => 'process_disbursement', 'display_name' => 'Process Disbursement', 'description' => 'Process approved budget disbursements'],
            ['name' => 'ethics_review', 'display_name' => 'Ethics Review', 'description' => 'Conduct ethics review and issue IRERC clearance'],
            ['name' => 'approve_extensions', 'display_name' => 'Approve Extensions', 'description' => 'Approve or reject project extension requests'],
            ['name' => 'approve_amendments', 'display_name' => 'Approve Amendments', 'description' => 'Approve or reject budget amendment requests'],
            ['name' => 'approve_pi_transfer', 'display_name' => 'Approve PI Transfer', 'description' => 'Approve or reject PI transfer requests'],
            ['name' => 'approve_termination', 'display_name' => 'Approve Termination', 'description' => 'Approve or reject project termination requests'],
            ['name' => 'manage_certificates', 'display_name' => 'Manage Certificates', 'description' => 'Issue and manage project completion certificates'],
            ['name' => 'view_rcsc_portal', 'display_name' => 'View RCSC Portal', 'description' => 'Access the RCSC high-budget project portal'],
            ['name' => 'view_analytics', 'display_name' => 'View Analytics', 'description' => 'Access system analytics and charts'],
            ['name' => 'manage_users', 'display_name' => 'Manage Users', 'description' => 'Create, edit, and manage user accounts'],
            ['name' => 'manage_thematic_areas', 'display_name' => 'Manage Thematic Areas', 'description' => 'Create, edit, and manage research thematic areas'],
            ['name' => 'view_audit_logs', 'display_name' => 'View Audit Logs', 'description' => 'Access system audit logs'],
            ['name' => 'hrms_sync', 'display_name' => 'HRMS Sync', 'description' => 'Access HRMS synchronization monitor'],
            ['name' => 'manage_procurement', 'display_name' => 'Manage Procurement', 'description' => 'Approve or reject procurement requests'],
            ['name' => 'submit_progress_report', 'display_name' => 'Submit Progress Report', 'description' => 'Submit milestone progress reports for projects'],
            ['name' => 'review_progress_report', 'display_name' => 'Review Progress Report', 'description' => 'Review and provide feedback on progress reports'],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission['name']], $permission);
        }

        $rolePermissions = [
            'pi' => [
                'view_dashboard', 'manage_profile', 'create_project', 'view_projects',
                'manage_team', 'request_extension', 'request_amendment', 'request_termination',
                'request_pi_transfer', 'submit_procurement', 'sign_contract',
                'submit_progress_report',
            ],
            'tm' => [
                'view_dashboard', 'manage_profile', 'view_projects', 'submit_progress_report',
            ],
            'reviewer' => [
                'view_dashboard', 'manage_profile', 'view_projects', 'submit_evaluation',
            ],
            'dh' => [
                'view_dashboard', 'manage_profile', 'view_projects', 'screen_proposals',
                'review_progress_report',
            ],
            'coordinator' => [
                'view_dashboard', 'manage_profile', 'view_projects', 'assign_reviewer',
                'manage_certificates', 'manage_procurement', 'approve_extensions',
                'approve_amendments', 'approve_pi_transfer', 'review_progress_report',
            ],
            'dean' => [
                'view_dashboard', 'manage_profile', 'view_projects', 'approve_budget',
            ],
            'irerc' => [
                'view_dashboard', 'manage_profile', 'view_projects', 'ethics_review',
            ],
            'vparttcs' => [
                'view_dashboard', 'manage_profile', 'view_projects', 'sign_contract',
                'approve_budget', 'approve_extensions', 'approve_amendments',
                'approve_pi_transfer', 'view_rcsc_portal', 'view_analytics',
                'manage_certificates', 'manage_procurement',
            ],
            'rcsc' => [
                'view_dashboard', 'manage_profile', 'view_projects', 'approve_budget',
                'approve_extensions', 'approve_amendments', 'view_rcsc_portal',
                'view_analytics', 'manage_certificates', 'manage_procurement',
            ],
            'finance' => [
                'view_dashboard', 'manage_profile', 'view_projects', 'process_disbursement',
            ],
            'admin' => [
                'view_dashboard', 'manage_profile', 'view_projects', 'manage_users',
                'manage_thematic_areas', 'view_audit_logs', 'hrms_sync',
                'approve_termination',
            ],
        ];

        foreach ($rolePermissions as $roleName => $permissionNames) {
            $role = Role::where('name', $roleName)->first();
            if ($role) {
                $permissionIds = Permission::whereIn('name', $permissionNames)->pluck('id')->toArray();
                $role->permissions()->syncWithoutDetaching($permissionIds);
            }
        }

        $users = User::all();
        foreach ($users as $user) {
            $role = Role::where('name', $user->role)->first();
            if ($role && !$user->role_id) {
                $user->update(['role_id' => $role->id]);
            }
        }
    }
}
