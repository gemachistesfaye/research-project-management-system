<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Project;
use App\Models\ProjectMember;
use App\Models\MilestoneReport;

class ProgressReportController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->role === 'tm') {
            $teamProjectIds = ProjectMember::where('user_id', $user->id)->pluck('project_id')->toArray();
            $projects = Project::whereIn('project_id', $teamProjectIds)
                ->whereIn('status', ['Active', 'Approved', 'Completed'])
                ->with(['milestoneReports', 'pi'])
                ->latest()
                ->get();
        } elseif (in_array($user->role, ['coordinator', 'admin', 'dean', 'vparttcs', 'rcsc'])) {
            $projects = Project::whereIn('status', ['Active', 'Approved', 'Completed'])
                ->with(['milestoneReports', 'pi'])
                ->latest()
                ->get();
        } elseif ($user->role === 'dh') {
            $projects = Project::where('dept_id', $user->dept_id)
                ->whereIn('status', ['Active', 'Approved', 'Completed'])
                ->with(['milestoneReports', 'pi'])
                ->latest()
                ->get();
        } else {
            $projects = Project::where('pi_id', $user->id)
                ->whereIn('status', ['Active', 'Approved', 'Completed'])
                ->with(['milestoneReports', 'pi'])
                ->latest()
                ->get();
        }

        return view('progress.index', compact('projects'));
    }

    public function show($projectId)
    {
        $user = Auth::user();
        $project = Project::where('project_id', $projectId)
            ->with(['milestoneReports', 'pi', 'members.user', 'budgetRequests'])
            ->firstOrFail();

        if ($user->role === 'pi' && (int) $project->pi_id !== (int) $user->id) {
            abort(403, 'You can only view progress reports for your own projects.');
        }

        if ($user->role === 'tm') {
            $isMember = ProjectMember::where('project_id', $projectId)->where('user_id', $user->id)->exists();
            if (!$isMember) {
                abort(403, 'You are not a member of this project.');
            }
        }

        if ($user->role === 'dh' && (int) $project->dept_id !== (int) $user->dept_id) {
            abort(403, 'You can only view progress reports for projects in your department.');
        }

        $reports = MilestoneReport::where('project_id', $projectId)
            ->latest()
            ->get();

        return view('progress.show', compact('project', 'reports'));
    }

    public function store(Request $request, $projectId)
    {
        $user = Auth::user();
        $project = Project::where('project_id', $projectId)->firstOrFail();

        if ($user->role === 'pi' && (int) $project->pi_id !== (int) $user->id) {
            abort(403, 'You can only submit progress reports for your own projects.');
        }

        if ($user->role === 'tm') {
            $isMember = ProjectMember::where('project_id', $projectId)->where('user_id', $user->id)->exists();
            if (!$isMember) {
                abort(403, 'You are not a member of this project.');
            }
        }

        $request->validate([
            'milestone_name'          => 'required|string|max:255',
            'progress_percentage'     => 'required|numeric|min:0|max:100',
            'summary_text'            => 'required|string',
            'deliverable_file'        => 'nullable|file|mimes:pdf,doc,docx,zip,rar,txt,xlsx,csv|max:20480',
            'deliverable_link_url'    => 'nullable|url|max:500',
            'deliverable_document_url'=> 'nullable|string|max:500',
        ]);

        $filePath = null;
        if ($request->hasFile('deliverable_file')) {
            $filePath = $request->file('deliverable_file')->store('progress_deliverables', 'public');
        }

        $linkUrl = $request->deliverable_link_url ?: $request->deliverable_document_url;

        MilestoneReport::create([
            'project_id'              => $projectId,
            'milestone_name'          => $request->milestone_name,
            'progress_percentage'     => $request->progress_percentage,
            'summary_text'            => $request->summary_text,
            'deliverable_file_path'   => $filePath,
            'deliverable_link_url'    => $linkUrl,
            'deliverable_document_url'=> $linkUrl ?: ($filePath ? \Illuminate\Support\Facades\Storage::url($filePath) : null),
            'status'                  => 'Submitted',
        ]);

        return back()->with('success', 'Progress report submitted successfully.');
    }

    public function update(Request $request, $reportId)
    {
        $report = MilestoneReport::findOrFail($reportId);
        $project = Project::where('project_id', $report->project_id)->firstOrFail();
        $user = Auth::user();

        if (!in_array($user->role, ['coordinator', 'admin'])) {
            abort(403, 'Only Research Coordinators and Administrators are authorized to audit and approve progress reports.');
        }

        $request->validate([
            'coordinator_feedback' => 'required|string',
            'status'               => 'required|in:Coordinator_Audited,Approved,Needs_Revision',
        ]);

        $report->update([
            'coordinator_feedback' => $request->coordinator_feedback,
            'status'               => $request->status,
        ]);

        // Auto-generate Tranche 2 and Tranche 3 Budget Requests on Progress Approval based on milestone thresholds
        if (in_array($request->status, ['Approved', 'Coordinator_Audited'])) {
            $approvedBudget = $project->approved_budget ?: $project->requested_budget;
            $tier = ($approvedBudget >= 500000) ? 'RCSC_VP' : 'Dean';

            // Tranche 2 (40% Mid-Term Release): Triggered ONLY when milestone progress reaches mid-term (>= 40%)
            if ($report->progress_percentage >= 40) {
                $existingTranche2 = \App\Models\BudgetRequest::where('project_id', $project->project_id)
                    ->where('milestone_phase', 'Tranche 2')
                    ->first();

                if (!$existingTranche2) {
                    $tranche2Amount = round($approvedBudget * 0.40, 2);
                    \App\Models\BudgetRequest::create([
                        'project_id'       => $project->project_id,
                        'milestone_phase'  => 'Tranche 2',
                        'requested_amount' => $tranche2Amount,
                        'approved_amount'  => $tranche2Amount,
                        'approval_tier'    => $tier,
                        'status'           => 'Approved',
                        'approved_by'      => $user->id,
                    ]);
                }
            }

            // Tranche 3 (30% Final Release): Triggered ONLY when final milestone progress reaches 100%
            if ($report->progress_percentage >= 100) {
                $existingTranche3 = \App\Models\BudgetRequest::where('project_id', $project->project_id)
                    ->where('milestone_phase', 'Tranche 3')
                    ->first();

                if (!$existingTranche3) {
                    $tranche3Amount = round($approvedBudget * 0.30, 2);
                    \App\Models\BudgetRequest::create([
                        'project_id'       => $project->project_id,
                        'milestone_phase'  => 'Tranche 3',
                        'requested_amount' => $tranche3Amount,
                        'approved_amount'  => $tranche3Amount,
                        'approval_tier'    => $tier,
                        'status'           => 'Approved',
                        'approved_by'      => $user->id,
                    ]);
                }
            }
        }

        return back()->with('success', 'Progress report status updated successfully. Tranche disbursement has been queued in Finance.');
    }
}
