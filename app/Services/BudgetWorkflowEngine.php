<?php

namespace App\Services;

use App\Models\BudgetRequest;
use App\Models\Project;

class BudgetWorkflowEngine
{
    const THRESHOLD_DEAN_CAP = 500000.00; // 500,000 ETB

    /**
     * Determine whether budget approval requires College Dean or RCSC/Vice President.
     */
    public function getApprovalTier($amount)
    {
        return ($amount >= self::THRESHOLD_DEAN_CAP) ? 'RCSC_VP' : 'Dean';
    }

    /**
     * Process a budget request and route to correct approval tier.
     */
    public function createTrancheRequest(Project $project, $phase, $amount)
    {
        $tier = $this->getApprovalTier($amount);

        return BudgetRequest::create([
            'project_id' => $project->project_id,
            'milestone_phase' => $phase,
            'requested_amount' => $amount,
            'approval_tier' => $tier,
            'status' => 'Pending',
        ]);
    }
}

