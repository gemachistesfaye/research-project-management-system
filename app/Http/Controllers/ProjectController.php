<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Project;
use App\Models\ProjectMember;
use App\Models\ThematicArea;
use App\Models\Evaluation;
use App\Models\User;
use App\Models\Department;
use App\Services\BlindReviewService;

class ProjectController extends Controller
{
    protected $blindReviewService;

    public function __construct(BlindReviewService $blindReviewService)
    {
        $this->blindReviewService = $blindReviewService;
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Project::with(['pi', 'thematicArea', 'department']);

        if ($user->role === 'pi') {
            $query->where('pi_id', $user->id);
        } elseif ($user->role === 'tm') {
            $teamProjectIds = ProjectMember::where('user_id', $user->id)->pluck('project_id')->toArray();
            $query->whereIn('project_id', $teamProjectIds);
        } elseif ($user->role === 'reviewer') {
            $assignedProjectIds = Evaluation::where('examiner_id', $user->id)->pluck('project_id')->toArray();
            $query->whereIn('project_id', $assignedProjectIds);
        } elseif ($user->role === 'dh') {
            $query->where('projects.dept_id', $user->dept_id)
                  ->where('status', '!=', 'Draft');
        } elseif (in_array($user->role, ['coordinator', 'dean'])) {
            $collegeId = Department::where('id', $user->dept_id)->value('college_id');
            $collegeDeptIds = Department::where('college_id', $collegeId)->pluck('id')->toArray();
            $query->whereIn('projects.dept_id', $collegeDeptIds)
                  ->where('status', '!=', 'Draft');
        } else {
            $query->where('status', '!=', 'Draft');
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('title', 'LIKE', "%{$search}%");
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('thematic_area')) {
            $query->where('thematic_id', $request->input('thematic_area'));
        }

        $thematicAreas = \App\Models\ThematicArea::where('is_active', true)->get();

        $projects = $query->latest()->paginate(10)->withQueryString();

        return view('projects.index', compact('projects', 'thematicAreas'));
    }

    public function submit($id)
    {
        $project = Project::findOrFail($id);
        $user = Auth::user();

        if ($user->role !== 'pi' || (int) $project->pi_id !== (int) $user->id) {
            abort(403, 'Only the PI can submit their own draft proposal.');
        }

        if (!in_array($project->status, ['Draft', 'Withdrawn', 'Returned', 'Rejected'])) {
            return back()->with('error', 'Only draft, withdrawn, returned, or rejected proposals can be submitted.');
        }

        $project->update(['status' => 'Submitted', 'current_stage' => 1, 'dh_screened_at' => null, 'under_review_at' => null, 'approved_at' => null, 'activated_at' => null, 'completed_at' => null]);

        return back()->with('success', 'Proposal submitted for Department Head screening.');
    }

    public function create()
    {
        $thematicAreas = ThematicArea::where('is_active', true)->get();
        return view('projects.create', compact('thematicAreas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'             => 'required|string|max:500',
            'abstract_text'     => 'required|string',
            'thematic_id'       => 'required|exists:thematic_areas,id',
            'requested_budget'  => 'required|numeric|min:0',
            'proposal_document' => 'required|file|mimes:pdf,doc,docx|max:10240',
        ]);

        $user = Auth::user();

        // Handle PDF upload (SDD SCR-03, RSK-02)
        $docPath = null;
        if ($request->hasFile('proposal_document')) {
            $docPath = $request->file('proposal_document')
                ->store('proposals', 'public');
        }

        $project = Project::create([
            'title'                 => $request->title,
            'abstract_text'         => $request->abstract_text,
            'thematic_id'           => $request->thematic_id,
            'pi_id'                 => $user->id,
            'dept_id'               => $user->dept_id,
            'requested_budget'      => $request->requested_budget,
            'status'                => 'Draft',
            'current_stage'         => 0,
            'proposal_document_url' => $docPath,
        ]);

        return redirect()->route('projects.show', $project->project_id)
            ->with('success', 'Research proposal saved as draft. You can submit it when ready.');
    }

    public function storeDraft(Request $request)
    {
        $request->validate([
            'title'             => 'required|string|max:500',
            'abstract_text'     => 'required|string',
            'thematic_id'       => 'required|exists:thematic_areas,id',
            'requested_budget'  => 'required|numeric|min:0',
            'proposal_document' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
        ]);

        $user = Auth::user();

        $docPath = null;
        if ($request->hasFile('proposal_document')) {
            $docPath = $request->file('proposal_document')
                ->store('proposals', 'public');
        }

        $project = Project::create([
            'title'                 => $request->title,
            'abstract_text'         => $request->abstract_text,
            'thematic_id'           => $request->thematic_id,
            'pi_id'                 => $user->id,
            'dept_id'               => $user->dept_id,
            'requested_budget'      => $request->requested_budget,
            'status'                => 'Draft',
            'current_stage'         => 0,
            'proposal_document_url' => $docPath,
        ]);

        return redirect()->route('projects.show', $project->project_id)
            ->with('success', 'Draft saved successfully. You can edit or submit it later.');
    }

    public function edit($id)
    {
        $project = Project::findOrFail($id);
        $user = Auth::user();

        if ($user->role !== 'pi' || (int) $project->pi_id !== (int) $user->id) {
            abort(403, 'You can only edit your own projects.');
        }

        if (!in_array($project->status, ['Draft', 'Withdrawn', 'Returned'])) {
            return back()->with('error', 'Only draft, withdrawn, or returned proposals can be edited.');
        }

        $thematicAreas = \App\Models\ThematicArea::all();

        return view('projects.edit', compact('project', 'thematicAreas'));
    }

    public function update(Request $request, $id)
    {
        $project = Project::findOrFail($id);
        $user = Auth::user();

        if ($user->role !== 'pi' || (int) $project->pi_id !== (int) $user->id) {
            abort(403, 'You can only edit your own projects.');
        }

        if (!in_array($project->status, ['Draft', 'Withdrawn', 'Returned', 'Rejected'])) {
            return back()->with('error', 'Only draft, withdrawn, returned, or rejected proposals can be edited.');
        }

        $request->validate([
            'title'             => 'required|string|max:500',
            'abstract_text'     => 'required|string',
            'thematic_id'       => 'required|exists:thematic_areas,id',
            'requested_budget'  => 'required|numeric|min:0',
            'proposal_document' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
        ]);

        $data = [
            'title'            => $request->title,
            'abstract_text'    => $request->abstract_text,
            'thematic_id'      => $request->thematic_id,
            'requested_budget' => $request->requested_budget,
        ];

        if ($request->hasFile('proposal_document')) {
            $docPath = $request->file('proposal_document')
                ->store('proposals', 'public');
            $data['proposal_document_url'] = $docPath;
        }

        $project->update($data);

        return redirect()->route('projects.show', $project->project_id)
            ->with('success', 'Proposal updated successfully.');
    }

    public function destroy($id)
    {
        $project = Project::findOrFail($id);
        $user = Auth::user();

        if ($user->role !== 'pi' || (int) $project->pi_id !== (int) $user->id) {
            abort(403, 'You can only delete your own projects.');
        }

        if (!in_array($project->status, ['Draft', 'Withdrawn', 'Returned', 'Rejected'])) {
            return back()->with('error', 'Only draft, withdrawn, returned, or rejected proposals can be deleted.');
        }

        $pid = $project->project_id;

        DB::statement('PRAGMA foreign_keys = OFF');
        DB::table('procurement_requests')->where('project_id', $pid)->delete();
        DB::table('project_members')->where('project_id', $pid)->delete();
        DB::table('evaluations')->where('project_id', $pid)->delete();
        DB::table('irerc_clearances')->where('project_id', $pid)->delete();
        DB::table('budget_requests')->where('project_id', $pid)->delete();
        DB::table('budget_amendments')->where('project_id', $pid)->delete();
        DB::table('milestone_reports')->where('project_id', $pid)->delete();
        DB::table('project_extensions')->where('project_id', $pid)->delete();
        DB::table('pi_transfers')->where('project_id', $pid)->delete();
        DB::table('project_terminations')->where('project_id', $pid)->delete();
        DB::table('certificates')->where('project_id', $pid)->delete();
        $project->delete();
        DB::statement('PRAGMA foreign_keys = ON');

        return redirect()->route('projects.index')->with('success', 'Draft proposal deleted successfully.');
    }

    public function markComplete($id)
    {
        $project = Project::findOrFail($id);
        $user = Auth::user();

        if (!in_array($user->role, ['coordinator', 'admin'])) {
            abort(403, 'Only coordinators can mark projects as complete.');
        }

        if ($project->status !== 'Active') {
            return back()->with('error', 'Only active projects can be marked as complete.');
        }

        $project->status = 'Completed';
        $project->completed_at = now();
        $project->save();

        return back()->with('success', 'Project marked as completed. A certificate can now be issued.');
    }

    public function show($id)
    {
        $project = Project::with(['pi', 'thematicArea', 'department', 'members.user', 'evaluations', 'budgetRequests', 'extensions'])->findOrFail($id);
        $user = Auth::user();
        $anonymized = null;

        if ($user->role === 'pi' && (int) $project->pi_id !== (int) $user->id) {
            abort(403, 'You can only view your own projects.');
        }

        if ($user->role === 'reviewer') {
            $isAssigned = $project->evaluations()->where('examiner_id', $user->id)->exists();
            if (!$isAssigned) {
                abort(403, 'You can only view proposals assigned to you for review.');
            }
            $anonymized = $this->blindReviewService->getAnonymizedProposal($project);
        }

        $reviewers = \App\Models\User::where('role', 'reviewer')->get();

        return view('projects.show', compact('project', 'anonymized', 'reviewers'));
    }

    public function assignReviewer(Request $request, $id)
    {
        $request->validate([
            'examiner_id' => 'required|exists:users,id',
        ]);

        $project = Project::findOrFail($id);

        if (!in_array($project->status, ['DH_Screened', 'UnderReview'])) {
            return back()->with('error', 'This project has not been screened by the Department Head yet.');
        }

        $existing = Evaluation::where('project_id', $project->project_id)
            ->where('examiner_id', $request->examiner_id)
            ->exists();
        if ($existing) {
            return back()->with('error', 'This reviewer is already assigned to this project.');
        }

        Evaluation::create([
            'project_id' => $project->project_id,
            'examiner_id' => $request->examiner_id,
            'score' => 0.00,
            'decision' => 'Pending',
            'is_blind_masked' => true,
        ]);

        if ($project->status === 'DH_Screened') {
            $project->update(['status' => 'UnderReview', 'current_stage' => 2, 'under_review_at' => now()]);
        }

        return back()->with('success', 'Blind peer examiner assigned successfully.');
    }

    public function cancel($id)
    {
        $project = Project::findOrFail($id);
        $user = Auth::user();

        if ((int) $project->pi_id !== (int) $user->id) {
            abort(403, 'You can only withdraw your own projects.');
        }

        if (!in_array($project->status, ['Draft', 'Returned', 'Submitted', 'DH_Screened', 'UnderReview'])) {
            return back()->with('error', 'This project cannot be withdrawn at this stage.');
        }

        $project->update([
            'status' => 'Withdrawn',
            'cancelled_at' => now(),
            'cancelled_by_pi' => true,
        ]);

        return redirect()->route('projects.index')->with('success', 'Project withdrawn successfully.');
    }

    public function requestCancel(Request $request, $id)
    {
        $project = Project::findOrFail($id);
        $user = Auth::user();

        if ((int) $project->pi_id !== (int) $user->id) {
            abort(403, 'You can only withdraw your own projects.');
        }

        if (!in_array($project->status, ['Approved', 'Active'])) {
            return back()->with('error', 'This project cannot be withdrawn at this stage.');
        }

        $request->validate([
            'reason' => 'required|string|min:10',
        ]);

        $project->update([
            'status' => 'PendingCancellation',
            'cancellation_reason' => $request->reason,
            'cancelled_by_pi' => true,
        ]);

        return back()->with('success', 'Withdrawal request submitted. Awaiting admin approval.');
    }

    public function approveCancel($id)
    {
        $project = Project::findOrFail($id);
        $user = Auth::user();

        if (!in_array($user->role, ['admin', 'dean', 'rcsc', 'coordinator'])) {
            abort(403, 'You do not have permission to approve withdrawals.');
        }

        if ($project->status !== 'PendingCancellation') {
            return back()->with('error', 'This project is not pending withdrawal.');
        }

        $project->update([
            'status' => 'Withdrawn',
            'cancelled_at' => now(),
        ]);

        return back()->with('success', 'Project withdrawal approved.');
    }

    public function rejectCancel(Request $request, $id)
    {
        $project = Project::findOrFail($id);
        $user = Auth::user();

        if (!in_array($user->role, ['admin', 'dean', 'rcsc', 'coordinator'])) {
            abort(403, 'You do not have permission to reject withdrawals.');
        }

        if ($project->status !== 'PendingCancellation') {
            return back()->with('error', 'This project is not pending withdrawal.');
        }

        $previousStatus = $project->approved_at ? 'Approved' : 'UnderReview';

        $project->update([
            'status' => $previousStatus,
            'admin_cancel_notes' => $request->notes ?? null,
        ]);

        return back()->with('success', 'Withdrawal request rejected. Project restored.');
    }
}

