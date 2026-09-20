<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Project;
use App\Models\Evaluation;
use App\Models\BudgetRequest;
use App\Models\User;
use App\Models\ProjectMember;
use App\Models\IRERCClearance;
use App\Models\ProjectExtension;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $role = $user->role;

        $stats = $this->getRoleSpecificStats($user, $role);

        $myProjects = collect();
        $assignedReviews = collect();
        $pendingApprovals = collect();
        $teamProjects = collect();

        $pendingDisbursements = collect();
        $disbursedHistory = collect();

        if ($role === 'pi') {
            $myProjects = Project::where('pi_id', $user->id)->with(['thematicArea', 'irercClearance'])->get();
        } elseif ($role === 'reviewer') {
            $assignedReviews = Evaluation::where('examiner_id', $user->id)
                ->with('project')
                ->get();
        } elseif ($role === 'tm') {
            $teamProjects = ProjectMember::where('user_id', $user->id)
                ->with(['project.pi'])
                ->get();
        } elseif ($role === 'finance') {
            // Auto-queue Tranche 2 for any active projects with approved milestone reports
            $activeProjects = Project::where('status', 'Active')->with('milestoneReports')->get();
            foreach ($activeProjects as $p) {
                $hasApprovedMilestone = $p->milestoneReports->whereIn('status', ['Approved', 'Coordinator_Audited'])->count() > 0;
                $hasTranche2 = BudgetRequest::where('project_id', $p->project_id)
                    ->where('milestone_phase', 'Tranche 2')
                    ->exists();

                if ($hasApprovedMilestone && !$hasTranche2) {
                    $approvedBudget = $p->approved_budget ?: $p->requested_budget;
                    $tier = ($approvedBudget >= 500000) ? 'RCSC_VP' : 'Dean';
                    $tranche2Amount = round($approvedBudget * 0.40, 2);
                    BudgetRequest::create([
                        'project_id'       => $p->project_id,
                        'milestone_phase'  => 'Tranche 2',
                        'requested_amount' => $tranche2Amount,
                        'approved_amount'  => $tranche2Amount,
                        'approval_tier'    => $tier,
                        'status'           => 'Approved',
                        'approved_by'      => $user->id,
                    ]);
                }
            }

            $pendingDisbursements = BudgetRequest::where('status', 'Approved')
                ->where(function ($q) {
                    $q->where('milestone_phase', 'like', 'Tranche%')
                      ->orWhere('milestone_phase', 'Budget Amendment');
                })
                ->whereHas('project', function ($q) {
                    $q->whereIn('status', ['Active', 'Approved', 'Completed']);
                })
                ->with(['project.pi'])
                ->latest()
                ->get();

            $disbursedHistory = BudgetRequest::where('status', 'Released')
                ->with(['project.pi'])
                ->latest()
                ->limit(10)
                ->get();
        } elseif (in_array($role, ['dh', 'coordinator', 'dean', 'vparttcs', 'rcsc', 'irerc'])) {
            $pendingApprovals = Project::where('status', '!=', 'Draft')->where('status', '!=', 'Withdrawn')->with(['pi', 'thematicArea'])->latest()->limit(10)->get();
        }

        return view('dashboard', compact('user', 'role', 'stats', 'myProjects', 'assignedReviews', 'pendingApprovals', 'teamProjects', 'pendingDisbursements', 'disbursedHistory'));
    }

    private function getRoleSpecificStats($user, $role)
    {
        if ($role === 'pi') {
            $myProjects = Project::where('pi_id', $user->id);
            return [
                'my_projects' => $myProjects->count(),
                'draft' => (clone $myProjects)->where('status', 'Draft')->count(),
                'submitted' => (clone $myProjects)->where('status', 'Submitted')->count(),
                'active' => (clone $myProjects)->where('status', 'Active')->count(),
                'completed' => (clone $myProjects)->where('status', 'Completed')->count(),
                'total_budget' => (clone $myProjects)->sum('requested_budget'),
                'approved_budget' => (clone $myProjects)->sum('approved_budget'),
                'pending_extensions' => ProjectExtension::whereHas('project', fn($q) => $q->where('pi_id', $user->id))->where('status', 'Pending')->count(),
            ];
        } elseif ($role === 'reviewer') {
            return [
                'pending_reviews' => Evaluation::where('examiner_id', $user->id)->where('decision', 'Pending')->count(),
                'completed_reviews' => Evaluation::where('examiner_id', $user->id)->where('decision', '!=', 'Pending')->count(),
                'avg_score' => Evaluation::where('examiner_id', $user->id)->where('decision', '!=', 'Pending')->avg('score') ?? 0,
            ];
        } elseif ($role === 'dh') {
            return [
                'pending_screening' => Project::where('status', 'Submitted')->where('dept_id', $user->dept_id)->count(),
                'screened_today' => Project::where('status', 'DH_Screened')->whereDate('updated_at', today())->count(),
                'dept_projects' => Project::where('dept_id', $user->dept_id)->count(),
            ];
        } elseif ($role === 'coordinator') {
            return [
                'need_reviewer' => Project::where('status', 'DH_Screened')->count(),
                'under_review' => Project::where('status', 'UnderReview')->count(),
                'total_projects' => Project::where('status', '!=', 'Draft')->count(),
            ];
        } elseif ($role === 'dean') {
            return [
                'pending_approval' => Project::where('requested_budget', '<', 500000)->whereIn('status', ['Dean_Review', 'UnderReview', 'DH_Screened'])->count(),
                'approved' => Project::where('requested_budget', '<', 500000)->whereIn('status', ['Approved', 'Active', 'Completed'])->count(),
                'total_value' => Project::where('requested_budget', '<', 500000)->where('status', '!=', 'Draft')->sum('requested_budget'),
            ];
        } elseif ($role === 'irerc') {
            return [
                'pending_ethics' => IRERCClearance::where('status', 'Pending')->count(),
                'cleared' => IRERCClearance::where('status', 'Approved')->count(),
                'total_reviews' => IRERCClearance::count(),
            ];
        } elseif (in_array($role, ['rcsc', 'vparttcs'])) {
            return [
                'rcsc_pending' => Project::where('requested_budget', '>=', 500000)->whereIn('status', ['RCSC_Review', 'UnderReview', 'DH_Screened'])->count(),
                'pending_contracts' => Project::where('status', 'Approved')->whereNull('vp_signature_date')->count(),
                'approved' => Project::whereIn('status', ['Approved', 'Active', 'Completed'])->count(),
                'high_budget' => Project::where('requested_budget', '>=', 500000)->where('status', '!=', 'Draft')->count(),
            ];
        } elseif ($role === 'finance') {
            return [
                'pending_release' => BudgetRequest::where('status', 'Approved')->where('milestone_phase', 'like', 'Tranche%')->whereHas('project', fn($q) => $q->where('status', 'Active'))->count(),
                'disbursed' => BudgetRequest::where('status', 'Released')->sum('approved_amount'),
                'transactions' => BudgetRequest::where('status', 'Released')->count(),
            ];
        } elseif ($role === 'admin') {
            return [
                'total_users' => User::count(),
                'active_users' => User::where('status', 'active')->count(),
                'projects' => Project::where('status', '!=', 'Draft')->count(),
                'evaluations' => Evaluation::count(),
                'thematic_areas' => \App\Models\ThematicArea::count(),
                'departments' => \App\Models\Department::count(),
                'audit_logs' => \App\Models\AuditLog::count(),
            ];
        } elseif ($role === 'tm') {
            return [
                'team_projects' => ProjectMember::where('user_id', $user->id)->count(),
                'active_projects' => ProjectMember::where('user_id', $user->id)->whereHas('project', fn($q) => $q->where('status', 'Active'))->count(),
            ];
        }

        return [
            'total_projects' => Project::where('status', '!=', 'Draft')->count(),
            'active_projects' => Project::where('status', 'Active')->count(),
            'pending_reviews' => Evaluation::where('decision', 'Pending')->count(),
            'pending_budgets' => BudgetRequest::where('status', 'Pending')->count(),
        ];
    }
}
