<?php

namespace App\Services;

use App\Models\Project;
use App\Models\ProjectExtension;
use App\Models\ProjectTermination;
use Exception;

class LifecycleChangeService
{
    /**
     * Enforce strict extension limits (maximum 3 extensions).
     * 1st & 2nd extensions -> Approved by Coordinator.
     * 3rd extension -> Approved by RCSC.
     */
    public function requestExtension(Project $project, $reason, $months = 6)
    {
        $existingCount = $project->extensions()->count();

        if ($existingCount >= 3) {
            throw new Exception("Maximum limit of 3 project time extensions reached.");
        }

        $nextNumber = $existingCount + 1;
        $approverRole = ($nextNumber === 3) ? 'RCSC' : 'Coordinator';

        return ProjectExtension::create([
            'project_id' => $project->project_id,
            'extension_number' => $nextNumber,
            'requested_months' => min($months, 6),
            'reason' => $reason,
            'approver_role' => $approverRole,
            'status' => 'Pending'
        ]);
    }

    /**
     * Calculate financial refund for gracefully terminated project.
     */
    public function terminateProject(Project $project, $reason, $verifiedValue)
    {
        $totalDisbursed = $project->budgetRequests()
            ->where('status', 'Released')
            ->sum('approved_amount');

        $refundDue = max(0, $totalDisbursed - $verifiedValue);

        $termination = ProjectTermination::create([
            'project_id' => $project->project_id,
            'reason' => $reason,
            'total_disbursed' => $totalDisbursed,
            'verified_deliverables_value' => $verifiedValue,
            'refund_due' => $refundDue,
            'status' => 'Pending'
        ]);

        $project->update(['status' => 'Terminated']);

        return $termination;
    }
}

