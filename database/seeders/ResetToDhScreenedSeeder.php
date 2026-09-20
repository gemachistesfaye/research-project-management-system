<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Project;
use App\Models\Evaluation;
use App\Models\IRERCClearance;
use App\Models\BudgetRequest;

class ResetToDhScreenedSeeder extends Seeder
{
    public function run()
    {
        $pids = [6, 7];
        Evaluation::whereIn('project_id', $pids)->delete();
        IRERCClearance::whereIn('project_id', $pids)->delete();
        BudgetRequest::whereIn('project_id', $pids)->delete();

        Project::whereIn('project_id', $pids)->update([
            'status' => 'DH_Screened',
            'current_stage' => 1,
            'ethical_cleared' => 0,
            'pi_signature_date' => null,
            'vp_signature_date' => null,
            'contract_signed_at' => null,
            'activated_at' => null,
            'approved_at' => null,
            'completed_at' => null,
            'feedback' => null,
        ]);
    }
}
