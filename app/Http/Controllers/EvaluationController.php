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

        $pending = $assigned->where('decision', 'Pending');
        $completed = $assigned->where('decision', '!=', 'Pending');

        return view('evaluations.index', compact('pending', 'completed'));
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
        $request->validate([
            'score' => 'required|numeric|min:0|max:100',
            'decision' => 'required|in:Accepted,AcceptedWithMinorMods,AcceptedWithMajorMods,Rejected',
            'comments' => 'required|string',
        ]);

        $evaluation = Evaluation::where('eval_id', $evalId)
            ->where('examiner_id', Auth::id())
            ->firstOrFail();

        if ($evaluation->decision !== 'Pending') {
            return back()->with('error', 'This evaluation has already been submitted and cannot be re-submitted.');
        }

        $evaluation->update([
            'score' => $request->score,
            'decision' => $request->decision,
            'comments' => $request->comments,
            'evaluated_at' => now(),
        ]);

        $project = $evaluation->project;
        $allEvaluated = $project->evaluations()->where('decision', 'Pending')->count() === 0;

        if ($allEvaluated) {
            $hasRejection = $project->evaluations()->where('decision', 'Rejected')->exists();
            $hasMajorMods = $project->evaluations()->where('decision', 'AcceptedWithMajorMods')->exists();

            if ($hasRejection) {
                $comments = $project->evaluations()
                    ->where('decision', 'Rejected')
                    ->pluck('comments')
                    ->implode('; ');
                $project->update([
                    'status' => 'Rejected',
                    'feedback' => $comments,
                ]);
            } elseif ($hasMajorMods) {
                $comments = $project->evaluations()
                    ->where('decision', 'AcceptedWithMajorMods')
                    ->pluck('comments')
                    ->implode('; ');
                $project->update([
                    'status' => 'Returned',
                    'feedback' => 'Major modifications required: ' . $comments,
                ]);
            } else {
                $feedback = $project->evaluations()
                    ->whereIn('decision', ['Accepted', 'AcceptedWithMinorMods'])
                    ->pluck('comments')
                    ->implode('; ');

                // Dual-Threshold Financial Routing: < 500k ETB -> Dean_Review, >= 500k ETB -> RCSC_Review
                $targetStatus = ($project->requested_budget >= 500000.00) ? 'RCSC_Review' : 'Dean_Review';

                $project->update([
                    'status' => $targetStatus,
                    'feedback' => $feedback ?: $project->feedback,
                ]);
            }
        }

        return back()->with('success', 'Evaluation rubric score and technical critique submitted securely under blind review protocol.');
    }
}

