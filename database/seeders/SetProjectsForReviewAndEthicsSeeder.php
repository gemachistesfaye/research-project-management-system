<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Project;
use App\Models\Evaluation;
use App\Models\IRERCClearance;
use App\Models\BudgetRequest;

class SetProjectsForReviewAndEthicsSeeder extends Seeder
{
    public function run()
    {
        $pass = Hash::make('GMU@Demo1');

        // Ensure we have reviewer 1 and reviewer 2
        $rev1 = User::firstOrCreate(
            ['email' => 'reviewer@gmu.edu.et'],
            ['staff_id' => 'GMU-EXAM-201', 'name' => 'Prof. Kebede Tassew (Reviewer)', 'password' => $pass, 'role' => 'reviewer', 'dept_id' => 2]
        );
        \App\Services\RbacService::syncUserRole($rev1);

        $rev2 = User::firstOrCreate(
            ['email' => 'reviewer2@gmu.edu.et'],
            ['staff_id' => 'GMU-EXAM-202', 'name' => 'Dr. Berhanu Nega (Reviewer 2)', 'password' => $pass, 'role' => 'reviewer', 'dept_id' => 3]
        );
        \App\Services\RbacService::syncUserRole($rev2);

        $pids = [6, 7];

        // Clean existing records
        Evaluation::whereIn('project_id', $pids)->delete();
        IRERCClearance::whereIn('project_id', $pids)->delete();
        BudgetRequest::whereIn('project_id', $pids)->delete();

        // Reset projects to UnderReview
        Project::whereIn('project_id', $pids)->update([
            'status' => 'UnderReview',
            'current_stage' => 2,
            'under_review_at' => now(),
            'dh_screened_at' => now()->subDay(),
            'ethical_cleared' => 0,
            'pi_signature_date' => null,
            'vp_signature_date' => null,
            'contract_signed_at' => null,
            'activated_at' => null,
            'approved_at' => null,
            'completed_at' => null,
            'feedback' => null,
        ]);

        // Assign both reviewers to Project 6 and Project 7
        foreach ($pids as $pid) {
            Evaluation::create([
                'project_id' => $pid,
                'examiner_id' => $rev1->id,
                'score' => 0.00,
                'decision' => 'Pending',
                'is_blind_masked' => true,
            ]);

            Evaluation::create([
                'project_id' => $pid,
                'examiner_id' => $rev2->id,
                'score' => 0.00,
                'decision' => 'Pending',
                'is_blind_masked' => true,
            ]);

            // Route to Ethics (IRERC) in Pending status
            IRERCClearance::create([
                'project_id' => $pid,
                'status' => 'Pending',
                'risk_level' => 'Low',
                'committee_notes' => 'Informed consent protocol review and environmental safety screening pending.',
            ]);
        }
    }
}

