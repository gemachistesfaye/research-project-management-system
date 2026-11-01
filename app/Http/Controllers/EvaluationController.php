<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Evaluation;
use App\Models\Project;

class EvaluationController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $assigned = Evaluation::where('examiner_id', $user->id)
            ->with('project.pi')
            ->get();

        $pendingProposal  = $assigned->where('evaluation_type', 'Proposal')->where('decision', 'Pending');
        $pendingProgress  = $assigned->where('evaluation_type', 'Progress')->where('decision', 'Pending');
        $pendingFinal     = $assigned->where('evaluation_type', 'FinalDefence')->where('decision', 'Pending');
        $completed        = $assigned->where('decision', '!=', 'Pending');

        // Legacy compat: items without evaluation_type are treated as Proposal
        $legacyPending = $assigned->filter(fn($e) => empty($e->evaluation_type))->where('decision', 'Pending');
        $pendingProposal = $pendingProposal->merge($legacyPending);

        $pending = $assigned->where('decision', 'Pending');

        return view('evaluations.index', compact(
            'pending', 'pendingProposal', 'pendingProgress', 'pendingFinal', 'completed'
        ));
    }

    public function show($evalId)
    {
        $evaluation = Evaluation::where('eval_id', $evalId)
            ->where('examiner_id', Auth::id())
            ->with('project.pi')
            ->firstOrFail();

        return view('evaluations.show', compact('evaluation'));
    }

    public function submitScore(Request $request, $evalId)
    {
        $evaluation = Evaluation::where('eval_id', $evalId)
            ->where('examiner_id', Auth::id())
            ->firstOrFail();

        if ($evaluation->decision !== 'Pending') {
            return back()->with('error', 'This evaluation has already been submitted and cannot be re-submitted.');
        }

        $type = $evaluation->evaluation_type ?? 'Proposal';

        if ($type === 'FinalDefence') {
            $request->validate([
                'score'             => 'nullable|numeric|min:0|max:100',
                'final_decision'    => 'required|in:Accepted,AcceptedWithMinorModification,Rejected',
                'revision_comments' => 'nullable|string',
                'comments'          => 'nullable|string',
            ]);

            $evaluation->update([
                'score'             => $request->score,
                'decision'          => $request->final_decision === 'Rejected' ? 'Rejected' : 'Accepted',
                'final_decision'    => $request->final_decision,
                'revision_comments' => $request->revision_comments,
                'comments'          => $request->comments,
                'evaluated_at'      => now(),
            ]);

        } elseif ($type === 'Progress') {
            $request->validate([
                'score'    => 'nullable|numeric|min:0|max:100',
                'decision' => 'required|in:Satisfactory,NeedsImprovement,Unsatisfactory,Pending',
                'comments' => 'required|string|min:10',
            ]);

            $evaluation->update([
                'score'        => $request->score,
                'decision'     => $request->decision,
                'comments'     => $request->comments,
                'evaluated_at' => now(),
            ]);

        } else {
            // Proposal type evaluation
            $request->validate([
                'score'    => 'required|numeric|min:0|max:100',
                'decision' => 'required|in:Accepted,AcceptedWithMinorMods,AcceptedWithMajorMods,Rejected,Pending',
                'comments' => 'nullable|string',
            ]);

            $evaluation->update([
                'score'        => $request->score,
                'decision'     => $request->decision,
                'comments'     => $request->comments,
                'evaluated_at' => now(),
            ]);

            \App\Services\AuditService::log('EVALUATION_SCORED', 'Evaluation', $evaluation->eval_id,
                "Blind peer review scored ({$request->score}/100 - {$request->decision}) for Project #{$evaluation->project_id}");

            $project = $evaluation->project;
            $allEvaluated = $project->evaluations()
                ->whereIn('evaluation_type', ['Proposal'])
                ->where('decision', 'Pending')->count() === 0
                && $project->evaluations()->whereNull('evaluation_type')->where('decision', 'Pending')->count() === 0;

            if ($allEvaluated) {
                $hasRejection = $project->evaluations()->where('decision', 'Rejected')->exists();
                $hasMajorMods = $project->evaluations()->where('decision', 'AcceptedWithMajorMods')->exists();

                if ($hasRejection) {
                    $comments = $project->evaluations()->where('decision', 'Rejected')->pluck('comments')->implode('; ');
                    $project->update(['status' => 'Rejected', 'feedback' => $comments]);
                    \Illuminate\Support\Facades\DB::table('irerc_clearances')
                        ->where('project_id', $project->project_id)->delete();
                } elseif ($hasMajorMods) {
                    $comments = $project->evaluations()->where('decision', 'AcceptedWithMajorMods')->pluck('comments')->implode('; ');
                    $project->update(['status' => 'Returned', 'feedback' => 'Major modifications required: ' . $comments]);
                } else {
                    $feedback = $project->evaluations()
                        ->whereIn('decision', ['Accepted', 'AcceptedWithMinorMods'])
                        ->pluck('comments')->implode('; ');
                    $targetStatus = ($project->requested_budget >= 500000.00) ? 'RCSC_Review' : 'Dean_Review';
                    $approvalTier = ($project->requested_budget >= 500000.00) ? 'RCSC_VP' : 'Dean';
                    $project->update(['status' => $targetStatus, 'feedback' => $feedback ?: $project->feedback]);
                    \App\Models\BudgetRequest::firstOrCreate(
                        ['project_id' => $project->project_id, 'milestone_phase' => 1, 'approval_tier' => $approvalTier],
                        ['requested_amount' => $project->requested_budget, 'status' => 'Pending']
                    );
                }
            }

            return back()->with('success', 'Evaluation rubric score and technical critique submitted securely under blind review protocol.');
        }

        \App\Services\AuditService::log('EVALUATION_SUBMITTED', 'Evaluation', $evaluation->eval_id,
            "Examiner submitted {$type} evaluation (decision: {$evaluation->decision}) for Project #{$evaluation->project_id}");

        return back()->with('success', 'Evaluation submitted successfully.');
    }
}
