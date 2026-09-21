<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Project;
use App\Models\IRERCClearance;
use App\Models\BudgetRequest;
use App\Models\Certificate;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class GovernanceController extends Controller
{
    public function dhScreening()
    {
        $user = \Auth::user();
        $projects = Project::where('status', 'Submitted')
            ->where('dept_id', $user->dept_id)
            ->with('pi')
            ->get();
        return view('governance.dh_screening', compact('projects'));
    }

    public function dhScreeningDecision(Request $request, $id)
    {
        $request->validate([
            'decision' => 'required|in:approve,reject',
            'comments' => 'nullable|string',
        ]);

        $project = Project::findOrFail($id);
        $user = \Auth::user();

        if ((int) $project->dept_id !== (int) $user->dept_id) {
            abort(403, 'You can only screen proposals within your department.');
        }

        if ($project->status !== 'Submitted') {
            return back()->with('error', 'This proposal is not in the Submitted status.');
        }

        if ($request->decision === 'approve') {
            $project->update(['status' => 'DH_Screened', 'dh_screened_at' => now()]);
            return back()->with('success', 'Project screened and forwarded to Coordinator.');
        } else {
            $project->update(['status' => 'Returned', 'dh_screened_at' => null, 'feedback' => $request->comments]);
            return back()->with('success', 'Project sent back to PI for revisions.');
        }
    }

    public function coordinatorHub()
    {
        $projects = Project::whereIn('status', ['DH_Screened', 'UnderReview'])->with(['pi', 'evaluations', 'irercClearance'])->get();

        $reviewers = User::where('role', 'reviewer')
            ->with('department', 'evaluations')
            ->get()
            ->map(function ($reviewer) {
                $activeReviews = $reviewer->evaluations()->where('decision', 'Pending')->count();
                $totalEvaluations = $reviewer->evaluations()->whereNotNull('score')->count();
                $avgScore = $reviewer->evaluations()->whereNotNull('score')->avg('score');
                return [
                    'user' => $reviewer,
                    'active_reviews' => $activeReviews,
                    'total_evaluations' => $totalEvaluations,
                    'avg_score' => $avgScore ? round($avgScore, 1) : 'N/A',
                    'workload_percent' => min(100, ($activeReviews / 5) * 100),
                ];
            });

        return view('governance.coordinator_hub', compact('projects', 'reviewers'));
    }

    public function deanApprovals()
    {
        $pending = BudgetRequest::where('approval_tier', 'Dean')
            ->where('status', 'Pending')
            ->with('project.pi')
            ->get();

        $completed = BudgetRequest::where('approval_tier', 'Dean')
            ->whereIn('status', ['Approved', 'Rejected'])
            ->with('project.pi')
            ->latest('updated_at')
            ->get();

        return view('governance.dean_approvals', compact('pending', 'completed'));
    }

    public function deanDecision(Request $request, $id)
    {
        $request->validate([
            'decision' => 'required|in:Approved,Rejected',
            'approved_amount' => 'nullable|numeric|min:0',
            'comments' => 'nullable|string',
        ]);

        $budgetRequest = BudgetRequest::findOrFail($id);

        if ($budgetRequest->approval_tier !== 'Dean') {
            return back()->with('error', 'This budget request is not in the Dean approval tier.');
        }

        if ($budgetRequest->status !== 'Pending') {
            return back()->with('error', 'This budget request has already been decided and cannot be re-submitted.');
        }

        $project = Project::where('project_id', $budgetRequest->project_id)->first();
        if (!$project) {
            return back()->with('error', 'Associated project not found.');
        }

        if ($project->requested_budget >= 500000) {
            return back()->with('error', 'This project requires RCSC approval (budget >= 500,000 ETB).');
        }

        $eligibleStatuses = ['UnderReview', 'Dean_Review', 'RCSC_Review', 'DH_Screened'];
        if (!in_array($project->status, $eligibleStatuses)) {
            return back()->with('error', 'This project is not eligible for budget approval at its current status.');
        }

        $updateData = [
            'status' => $request->decision,
            'approved_by' => \Auth::id(),
        ];

        if ($request->decision === 'Approved' && $request->approved_amount !== null) {
            $updateData['approved_amount'] = $request->approved_amount;
        }

        $budgetRequest->update($updateData);

        if ($request->decision === 'Approved') {
            $project->update(['status' => 'Approved', 'approved_budget' => $request->approved_amount ?? $project->requested_budget, 'approved_at' => now()]);
        } else {
            $project->update(['status' => 'Rejected', 'feedback' => $request->comments]);
        }

        return back()->with('success', "Budget request {$request->decision} successfully by Dean.");
    }

    public function rcscPortal()
    {
        $pending = BudgetRequest::where('approval_tier', 'RCSC_VP')
            ->where('status', 'Pending')
            ->with('project.pi')
            ->get();

        $completed = BudgetRequest::where('approval_tier', 'RCSC_VP')
            ->whereIn('status', ['Approved', 'Rejected'])
            ->with('project.pi')
            ->latest('updated_at')
            ->get();

        return view('governance.rcsc_portal', compact('pending', 'completed'));
    }

    public function rcscDecision(Request $request, $id)
    {
        $request->validate([
            'decision' => 'required|in:Approved,Rejected',
            'approved_amount' => 'nullable|numeric|min:0',
            'comments' => 'nullable|string',
        ]);

        $budgetRequest = BudgetRequest::findOrFail($id);

        if ($budgetRequest->approval_tier !== 'RCSC_VP') {
            return back()->with('error', 'This budget request is not in the RCSC approval tier.');
        }

        if ($budgetRequest->status !== 'Pending') {
            return back()->with('error', 'This budget request has already been decided and cannot be re-submitted.');
        }

        $project = Project::where('project_id', $budgetRequest->project_id)->first();
        if (!$project) {
            return back()->with('error', 'Associated project not found.');
        }

        if ($project->requested_budget < 500000) {
            return back()->with('error', 'This project requires Dean approval (budget < 500,000 ETB).');
        }

        $eligibleStatuses = ['UnderReview', 'Dean_Review', 'RCSC_Review', 'DH_Screened'];
        if (!in_array($project->status, $eligibleStatuses)) {
            return back()->with('error', 'This project is not eligible for budget approval at its current status.');
        }

        $updateData = [
            'status' => $request->decision,
            'approved_by' => \Auth::id(),
        ];

        if ($request->decision === 'Approved' && $request->approved_amount !== null) {
            $updateData['approved_amount'] = $request->approved_amount;
        }

        $budgetRequest->update($updateData);

        if ($request->decision === 'Approved') {
            $project->update(['status' => 'Approved', 'approved_budget' => $request->approved_amount ?? $project->requested_budget, 'approved_at' => now()]);
        } else {
            $project->update(['status' => 'Rejected', 'feedback' => $request->comments]);
        }

        return back()->with('success', "Budget request {$request->decision} successfully by RCSC.");
    }

    public function irercPanel()
    {
        $pending = IRERCClearance::where('status', 'Pending')
            ->with('project.pi')
            ->get();
        $completed = IRERCClearance::whereIn('status', ['Approved', 'Rejected'])
            ->with('project.pi')
            ->latest('updated_at')
            ->get();
        return view('governance.irerc_panel', compact('pending', 'completed'));
    }

    public function irercDecision(Request $request, $id)
    {
        $request->validate([
            'decision'   => 'required|in:Approved,Rejected',
            'risk_level' => 'required|in:Low,Medium,High,Critical',
            'comments'   => 'nullable|string',
            'conditions' => 'nullable|string',
        ]);

        $clearance = IRERCClearance::findOrFail($id);

        if ($clearance->status !== 'Pending') {
            return back()->with('error', 'This ethics review has already been decided and cannot be re-submitted.');
        }

        $project = Project::where('project_id', $clearance->project_id)->first();
        if (!$project) {
            return back()->with('error', 'Associated project not found.');
        }

        $eligibleStatuses = ['Submitted', 'DH_Screened', 'UnderReview', 'Dean_Review', 'RCSC_Review', 'Approved', 'Active'];
        if (!in_array($project->status, $eligibleStatuses)) {
            return back()->with('error', 'This project is not eligible for ethics review at its current status.');
        }

        $notes = $request->comments ?: $request->conditions;

        $updateData = [
            'status'          => $request->decision,
            'risk_level'      => $request->risk_level,
            'committee_notes' => $notes,
            'issued_at'       => now(),
        ];

        if ($request->decision === 'Approved') {
            $updateData['clearance_code'] = 'IRERC-' . strtoupper(uniqid());
        }

        $clearance->update($updateData);

        if ($request->decision === 'Approved') {
            Project::where('project_id', $clearance->project_id)->update(['ethical_cleared' => true]);
        } else {
            Project::where('project_id', $clearance->project_id)->update(['status' => 'Rejected', 'feedback' => $notes]);
        }

        return back()->with('success', "Ethics review {$request->decision} successfully.");
    }

    public function analytics()
    {
        $submittedProjectsQuery = Project::where('status', '!=', 'Draft');

        $totalProjects       = (clone $submittedProjectsQuery)->count();
        $activeProjects      = Project::where('status', 'Active')->count();
        $completedProjects   = Project::where('status', 'Completed')->count();
        $underReviewProjects = Project::whereIn('status', ['Submitted', 'DH_Screened', 'UnderReview', 'Dean_Review', 'RCSC_Review'])->count();

        $statusCounts = Project::where('status', '!=', 'Draft')
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->get();

        $statusLabels = $statusCounts->pluck('status')->toArray();
        $statusData   = $statusCounts->pluck('total')->toArray();

        $budgetByThematic = DB::table('projects')
            ->where('status', '!=', 'Draft')
            ->join('thematic_areas', 'projects.thematic_id', '=', 'thematic_areas.id')
            ->select(
                'thematic_areas.title as thematic_title',
                DB::raw('count(projects.project_id) as project_count'),
                DB::raw('sum(projects.requested_budget) as total_requested'),
                DB::raw('sum(projects.approved_budget) as total_approved')
            )
            ->groupBy('thematic_areas.id', 'thematic_areas.title')
            ->get();

        $thematicLabels  = $budgetByThematic->pluck('thematic_title')
            ->map(fn($t) => strlen($t) > 30 ? substr($t, 0, 30).'…' : $t)
            ->toArray();
        $thematicBudgets = $budgetByThematic->pluck('total_requested')->toArray();

        return view('governance.analytics', compact(
            'totalProjects', 'activeProjects', 'completedProjects', 'underReviewProjects',
            'statusLabels', 'statusData',
            'thematicLabels', 'thematicBudgets',
            'budgetByThematic'
        ));
    }

    public function exportAnalyticsCsv()
    {
        $budgetByThematic = DB::table('projects')
            ->where('status', '!=', 'Draft')
            ->join('thematic_areas', 'projects.thematic_id', '=', 'thematic_areas.id')
            ->select(
                'thematic_areas.title as thematic_title',
                DB::raw('count(projects.project_id) as project_count'),
                DB::raw('sum(projects.requested_budget) as total_requested'),
                DB::raw('sum(projects.approved_budget) as total_approved')
            )
            ->groupBy('thematic_areas.id', 'thematic_areas.title')
            ->get();

        $filename = 'GMU_Research_Portfolio_Report_' . date('Y_m_d_His') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        $callback = function () use ($budgetByThematic) {
            $handle = fopen('php://output', 'w');
            // UTF-8 BOM for Excel compatibility
            fputs($handle, "\xEF\xBB\xBF");

            fputcsv($handle, ['Gambella University - Institutional Research Portfolio Report']);
            fputcsv($handle, ['Academic Year:', 'AY 2026/2027 (Call #1 Active)']);
            fputcsv($handle, ['Generated At:', now()->format('M d, Y H:i:s')]);
            fputcsv($handle, ['Generated By:', Auth::user()->name . ' (' . strtoupper(Auth::user()->role) . ')']);
            fputcsv($handle, []);

            // Thematic Budget Table
            fputcsv($handle, ['Thematic Area', 'Submitted Projects', 'Total Requested (ETB)', 'Total Approved (ETB)', 'Governance Tier']);
            foreach ($budgetByThematic as $row) {
                $tier = ($row->total_requested >= 500000) ? 'RCSC / VP Tier' : 'College Dean Tier';
                fputcsv($handle, [
                    $row->thematic_title,
                    $row->project_count,
                    number_format($row->total_requested, 2, '.', ''),
                    number_format($row->total_approved ?? 0, 2, '.', ''),
                    $tier
                ]);
            }

            fputcsv($handle, []);
            fputcsv($handle, ['--- Individual Submitted Research Projects ---']);
            fputcsv($handle, ['Project Code', 'Title', 'Thematic Area', 'PI Name', 'Requested Budget (ETB)', 'Approved Budget (ETB)', 'Status']);

            $projects = Project::where('status', '!=', 'Draft')
                ->with(['thematicArea', 'pi'])
                ->get();

            foreach ($projects as $p) {
                fputcsv($handle, [
                    $p->project_code ?? ('GMU-PRJ-' . $p->project_id),
                    $p->title,
                    $p->thematicArea->title ?? 'N/A',
                    $p->pi->name ?? 'N/A',
                    number_format($p->requested_budget, 2, '.', ''),
                    number_format($p->approved_budget ?? 0, 2, '.', ''),
                    $p->status
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportAnalyticsPdf()
    {
        $submittedProjectsQuery = Project::where('status', '!=', 'Draft');

        $totalProjects       = (clone $submittedProjectsQuery)->count();
        $activeProjects      = Project::where('status', 'Active')->count();
        $completedProjects   = Project::where('status', 'Completed')->count();
        $underReviewProjects = Project::whereIn('status', ['Submitted', 'DH_Screened', 'UnderReview', 'Dean_Review', 'RCSC_Review'])->count();

        $statusCounts = Project::where('status', '!=', 'Draft')
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->get();

        $budgetByThematic = DB::table('projects')
            ->where('status', '!=', 'Draft')
            ->join('thematic_areas', 'projects.thematic_id', '=', 'thematic_areas.id')
            ->select(
                'thematic_areas.title as thematic_title',
                DB::raw('count(projects.project_id) as project_count'),
                DB::raw('sum(projects.requested_budget) as total_requested'),
                DB::raw('sum(projects.approved_budget) as total_approved')
            )
            ->groupBy('thematic_areas.id', 'thematic_areas.title')
            ->get();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('governance.analytics_pdf', [
            'totalProjects'       => $totalProjects,
            'activeProjects'      => $activeProjects,
            'completedProjects'   => $completedProjects,
            'underReviewProjects' => $underReviewProjects,
            'statusCounts'        => $statusCounts,
            'budgetByThematic'    => $budgetByThematic,
            'generatedAt'         => now()->format('M d, Y H:i:s'),
            'generatedBy'         => Auth::user()->name,
        ])->setPaper('a4', 'portrait');

        return $pdf->download('GMU_Research_Portfolio_Report_' . date('Y_m_d_His') . '.pdf');
    }

    public function certificates()
    {
        $certificates = Certificate::with('project')->get();
        $completedProjects = Project::where('status', 'Completed')->get();
        return view('governance.certificates', compact('certificates', 'completedProjects'));
    }

    public function createIrercClearance($id)
    {
        $project = Project::findOrFail($id);

        $eligibleStatuses = ['Submitted', 'DH_Screened', 'UnderReview', 'Dean_Review', 'RCSC_Review', 'Approved', 'Active'];
        if (!in_array($project->status, $eligibleStatuses)) {
            return back()->with('error', 'This project is not eligible for ethics review at its current status.');
        }

        $existing = IRERCClearance::where('project_id', $project->project_id)->first();
        if ($existing) {
            return back()->with('error', 'An ethics clearance already exists for this project.');
        }

        IRERCClearance::create([
            'project_id' => $project->project_id,
            'risk_level' => 'Low',
            'status' => 'Pending',
        ]);

        return back()->with('success', 'IRERC ethics clearance created successfully.');
    }

    public function storeCertificate(Request $request)
    {
        $request->validate([
            'project_id'      => 'required|exists:projects,project_id',
            'type'            => 'required|in:Completion,Award',
            'issued_to_name'  => 'required|string|max:255',
        ]);

        $project = Project::where('project_id', $request->project_id)->first();
        if ($project->status !== 'Completed') {
            return back()->with('error', 'Certificates can only be issued for completed projects.');
        }

        Certificate::create([
            'project_id'      => $request->project_id,
            'certificate_code' => 'GMU-CERT-' . strtoupper(uniqid()),
            'type'            => $request->type,
            'issued_to_name'  => $request->issued_to_name,
            'issued_at'       => now(),
        ]);

        return back()->with('success', 'Certificate issued successfully.');
    }

    public function downloadCertificate($id)
    {
        $cert = Certificate::with('project')->findOrFail($id);
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('certificates.pdf', [
            'certificate_code' => $cert->certificate_code,
            'type'             => $cert->type,
            'issued_to_name'   => $cert->issued_to_name,
            'issued_at'        => $cert->issued_at,
            'project'          => $cert->project,
        ]);
        return $pdf->download('Certificate-' . $cert->certificate_code . '.pdf');
    }

    public function viewCertificate($id)
    {
        $cert = Certificate::with('project')->findOrFail($id);
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('certificates.pdf', [
            'certificate_code' => $cert->certificate_code,
            'type'             => $cert->type,
            'issued_to_name'   => $cert->issued_to_name,
            'issued_at'        => $cert->issued_at,
            'project'          => $cert->project,
        ]);
        return $pdf->stream('Certificate-' . $cert->certificate_code . '.pdf');
    }
}
