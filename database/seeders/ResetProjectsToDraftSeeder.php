<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Project;
use App\Models\Department;
use App\Models\ThematicArea;
use App\Models\Evaluation;
use App\Models\IRERCClearance;
use App\Models\MilestoneReport;
use App\Models\BudgetRequest;
use App\Models\BudgetAmendment;
use App\Models\ProcurementRequest;
use App\Models\ProjectExtension;
use App\Models\ProjectTermination;
use App\Models\PITransfer;
use App\Models\ProjectMember;
use App\Models\Certificate;

class ResetProjectsToDraftSeeder extends Seeder
{
    public function run()
    {
        // 1. Disable foreign key checks for clean truncation / deletion
        DB::statement('PRAGMA foreign_keys = OFF;');

        // 2. Clear all project-related transactions and records
        Certificate::truncate();
        BudgetAmendment::truncate();
        ProcurementRequest::truncate();
        BudgetRequest::truncate();
        MilestoneReport::truncate();
        Evaluation::truncate();
        IRERCClearance::truncate();
        ProjectExtension::truncate();
        ProjectTermination::truncate();
        PITransfer::truncate();
        ProjectMember::truncate();
        Project::truncate();

        DB::statement('PRAGMA foreign_keys = ON;');

        // 3. Find PI user and department
        $pi = User::where('email', 'pi@gmu.edu.et')->first() ?? User::where('role', 'pi')->first();
        if (!$pi) {
            throw new \Exception('PI user not found. Please run DatabaseSeeder first.');
        }

        $dept = Department::where('code', 'CS')->first() ?? Department::first();
        
        $thematics = ThematicArea::all();
        if ($thematics->count() < 3) {
            $t1 = ThematicArea::firstOrCreate(
                ['title' => 'Climate Resilience & Agricultural Productivity in Gambella Region'],
                ['category' => 'Agriculture & Environment', 'description' => 'Research focusing on flood resistance and modern farming techniques.']
            );
            $t2 = ThematicArea::firstOrCreate(
                ['title' => 'Digital Infrastructure & Smart Governance Solutions'],
                ['category' => 'Technology Transfer', 'description' => 'E-governance and localized intranet management systems for regional development.']
            );
            $t3 = ThematicArea::firstOrCreate(
                ['title' => 'Community Health & Infectious Disease Control'],
                ['category' => 'Community Service', 'description' => 'Public health interventions targeting tropical and seasonal diseases.']
            );
            $thematics = ThematicArea::all();
        }

        $tAgriculture = $thematics->firstWhere('category', 'Agriculture & Environment') ?? $thematics[0];
        $tTech = $thematics->firstWhere('category', 'Technology Transfer') ?? $thematics[1] ?? $thematics[0];
        $tHealth = $thematics->firstWhere('category', 'Community Service') ?? $thematics[2] ?? $thematics[0];

        // 4. Create EXACTLY 4 fresh projects in Draft status for PI
        Project::create([
            'title' => 'Development of IoT-Based Early Flood Warning System for Anywaa and Nuer Lowland Zones',
            'abstract_text' => 'This research deploys solar-powered telemetry sensor nodes along Baro and Akobo river tributaries to transmit real-time water stage metrics and SMS alerts to vulnerable agrarian communities.',
            'thematic_id' => $tTech->id,
            'pi_id' => $pi->id,
            'dept_id' => $dept ? $dept->id : null,
            'requested_budget' => 480000.00,
            'status' => 'Draft',
            'current_stage' => 1,
            'ethical_cleared' => false,
        ]);

        Project::create([
            'title' => 'Sustainable Agro-Forestry and Soil Carbon Sequestration Modeling in Gambella National Park Buffer Zones',
            'abstract_text' => 'An empirical investigation into indigenous agro-forestry practices, measuring soil organic carbon dynamics and evaluating community-led reforestation strategies across protected buffer corridors.',
            'thematic_id' => $tAgriculture->id,
            'pi_id' => $pi->id,
            'dept_id' => $dept ? $dept->id : null,
            'requested_budget' => 750000.00,
            'status' => 'Draft',
            'current_stage' => 1,
            'ethical_cleared' => false,
        ]);

        Project::create([
            'title' => 'Epidemiological Surveillance and Vector-Borne Disease Mapping Using GIS in Itang Special Woreda',
            'abstract_text' => 'Spatial-temporal modeling of seasonal malaria and neglected tropical disease transmission patterns in riparian ecosystems using high-resolution satellite imagery and localized health post telemetry.',
            'thematic_id' => $tHealth->id,
            'pi_id' => $pi->id,
            'dept_id' => $dept ? $dept->id : null,
            'requested_budget' => 395000.00,
            'status' => 'Draft',
            'current_stage' => 1,
            'ethical_cleared' => false,
        ]);

        Project::create([
            'title' => 'Solar-Powered Off-Grid Cold Chain Network for Indigenous Fishery Post-Harvest Preservation in Openo Basin',
            'abstract_text' => 'Design, prototyping, and community field testing of decentralized solar thermal cooling units to minimize post-harvest fish spoilage along Baro-Akobo river landing sites.',
            'thematic_id' => $tTech->id,
            'pi_id' => $pi->id,
            'dept_id' => $dept ? $dept->id : null,
            'requested_budget' => 920000.00,
            'status' => 'Draft',
            'current_stage' => 1,
            'ethical_cleared' => false,
        ]);
    }
}

