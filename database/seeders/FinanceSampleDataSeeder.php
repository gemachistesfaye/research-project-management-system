<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Project;
use App\Models\BudgetRequest;

class FinanceSampleDataSeeder extends Seeder
{
    public function run()
    {
        $p1 = Project::find(1);
        if ($p1) {
            $p1->update([
                'status' => 'Active',
                'approved_budget' => 480000.00,
                'activated_at' => now()->subMonths(2),
                'pi_signature_date' => now()->subMonths(2),
                'vp_signature_date' => now()->subMonths(2),
                'contract_signed_at' => now()->subMonths(2),
            ]);

            // Tranche 1 (Released)
            BudgetRequest::firstOrCreate(
                ['project_id' => $p1->project_id, 'milestone_phase' => 'Tranche 1'],
                [
                    'requested_amount' => 144000.00,
                    'approved_amount'  => 144000.00,
                    'approval_tier'    => 'Dean',
                    'status'           => 'Released',
                    'payment_method'   => 'Bank Transfer',
                    'disbursed_at'     => now()->subMonths(2),
                    'notes'            => 'CBE Transfer Ref #CBE-GMU-2026-8819 / Advance Payment',
                    'approved_by'      => 9,
                ]
            );

            // Tranche 2 (Pending approval/disbursement)
            BudgetRequest::firstOrCreate(
                ['project_id' => $p1->project_id, 'milestone_phase' => 'Tranche 2'],
                [
                    'requested_amount' => 192000.00,
                    'approved_amount'  => 192000.00,
                    'approval_tier'    => 'Dean',
                    'status'           => 'Approved',
                    'approved_by'      => 6,
                ]
            );
        }

        $p2 = Project::find(2);
        if ($p2) {
            $p2->update([
                'status' => 'Active',
                'approved_budget' => 750000.00,
                'activated_at' => now()->subMonths(1),
                'pi_signature_date' => now()->subMonths(1),
                'vp_signature_date' => now()->subMonths(1),
                'contract_signed_at' => now()->subMonths(1),
            ]);

            // Tranche 1 (Released)
            BudgetRequest::firstOrCreate(
                ['project_id' => $p2->project_id, 'milestone_phase' => 'Tranche 1'],
                [
                    'requested_amount' => 225000.00,
                    'approved_amount'  => 225000.00,
                    'approval_tier'    => 'RCSC_VP',
                    'status'           => 'Released',
                    'payment_method'   => 'Check',
                    'disbursed_at'     => now()->subDays(15),
                    'notes'            => 'Commercial Bank of Ethiopia Check #GMU-CHK-009241',
                    'approved_by'      => 9,
                ]
            );
        }
    }
}

