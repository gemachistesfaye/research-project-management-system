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
                ->whereIn('status', ['Active', 'Approved'])
                ->with('milestoneReports')
                ->get();
        } else {
            $projects = Project::where('pi_id', $user->id)
                ->whereIn('status', ['Active', 'Approved'])
                ->with('milestoneReports')
                ->get();
        }

        return view('progress.index', compact('projects'));
    }

    public function show($projectId)
    {
        $user = Auth::user();
        $project = Project::where('project_id', $projectId)
            ->with(['milestoneReports', 'pi', 'members.user'])
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
            'milestone_name'         => 'required|string|max:255',
            'progress_percentage'    => 'required|numeric|min:0|max:100',
            'summary_text'           => 'required|string',
            'deliverable_document_url' => 'nullable|string|max:500',
        ]);

        MilestoneReport::create([
            'project_id'              => $projectId,
            'milestone_name'          => $request->milestone_name,
            'progress_percentage'     => $request->progress_percentage,
            'summary_text'            => $request->summary_text,
            'deliverable_document_url'=> $request->deliverable_document_url,
            'status'                  => 'Submitted',
        ]);

        return back()->with('success', 'Progress report submitted successfully.');
    }

    public function update(Request $request, $reportId)
    {
        $report = MilestoneReport::findOrFail($reportId);
        $project = Project::where('project_id', $report->project_id)->firstOrFail();
        $user = Auth::user();

        if ($user->role === 'dh' && (int) $project->dept_id !== (int) $user->dept_id) {
            abort(403, 'You can only review progress reports for projects within your department.');
        }

        $request->validate([
            'coordinator_feedback' => 'required|string',
            'status'               => 'required|in:Coordinator_Audited,Approved,Needs_Revision',
        ]);

        $report->update([
            'coordinator_feedback' => $request->coordinator_feedback,
            'status'               => $request->status,
        ]);

        return back()->with('success', 'Progress report review updated.');
    }
}
