<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\BudgetRequest;
use App\Models\Project;
use App\Services\BudgetWorkflowEngine;

class BudgetController extends Controller
{
    protected $budgetEngine;

    public function __construct(BudgetWorkflowEngine $budgetEngine)
    {
        $this->budgetEngine = $budgetEngine;
    }

    public function approveProjectBudget(Request $request, $projectId)
    {
        $request->validate([
            'approved_budget' => 'required|numeric|min:0',
        ]);

        $project = Project::findOrFail($projectId);

        $tier = $this->budgetEngine->getApprovalTier($request->approved_budget);
        $userRole = Auth::user()->role;

        // Enforce SDD Dual-Threshold Security Rule
        if ($tier === 'RCSC_VP' && !in_array($userRole, ['rcsc', 'vparttcs', 'admin'])) {
            return back()->withErrors(['error' => 'Proposals with budget >= 500,000 ETB require RCSC / Vice President ratification.']);
        }

        if ($tier === 'Dean' && !in_array($userRole, ['dean', 'vparttcs', 'admin'])) {
            return back()->withErrors(['error' => 'Proposals with budget < 500,000 ETB require College Dean approval.']);
        }

        $project->update([
            'approved_budget' => $request->approved_budget,
            'status' => 'Approved',
            'current_stage' => 3,
            'approved_at' => now(),
        ]);

        // Create initial tranche request
        $this->budgetEngine->createTrancheRequest($project, 1, $request->approved_budget * 0.40); // 40% initial tranche

        return back()->with('success', "Project budget of {$request->approved_budget} ETB approved via {$tier} governance tier.");
    }
}

