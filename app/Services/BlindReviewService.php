<?php

namespace App\Services;

use App\Models\Evaluation;
use App\Models\Project;

class BlindReviewService
{
    /**
     * Return an anonymized view of a project proposal for assigned reviewers.
     * Strips author name, department, staff ID, and personal identifiers.
     */
    public function getAnonymizedProposal(Project $project)
    {
        return [
            'project_id' => $project->project_id,
            'title' => $project->title,
            'abstract_text' => $project->abstract_text,
            'thematic_area' => $project->thematicArea ? $project->thematicArea->title : 'N/A',
            'requested_budget' => $project->requested_budget,
            'proposal_document_url' => $project->proposal_document_url,
            'is_masked' => true,
        ];
    }

    /**
     * Return anonymized feedback/score for the PI.
     * Strips examiner's name and staff ID.
     */
    public function getAnonymizedEvaluation(Evaluation $evaluation)
    {
        return [
            'eval_id' => $evaluation->eval_id,
            'score' => $evaluation->score,
            'decision' => $evaluation->decision,
            'comments' => $evaluation->comments,
            'evaluated_at' => $evaluation->evaluated_at ? $evaluation->evaluated_at->toDateTimeString() : null,
            'examiner_alias' => 'Peer Reviewer #' . substr(md5($evaluation->examiner_id), 0, 4),
        ];
    }
}

