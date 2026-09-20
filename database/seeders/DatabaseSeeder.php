<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\College;
use App\Models\Department;
use App\Models\ThematicArea;
use App\Models\User;
use App\Models\Project;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            RbacSeeder::class,
        ]);

        // Use firstOrCreate to avoid duplicates on re-seed
        $c1 = College::firstOrCreate(['code' => 'CET'], ['name' => 'College of Engineering & Technology']);
        $c2 = College::firstOrCreate(['code' => 'CANR'], ['name' => 'College of Agriculture & Natural Resources']);
        $c3 = College::firstOrCreate(['code' => 'CBE'], ['name' => 'College of Business & Economics']);

        $d1 = Department::firstOrCreate(['code' => 'CS'], ['college_id' => $c1->id, 'name' => 'Computer Science & IT']);
        $d2 = Department::firstOrCreate(['code' => 'WRE'], ['college_id' => $c1->id, 'name' => 'Water Resource Engineering']);
        $d3 = Department::firstOrCreate(['code' => 'PS'], ['college_id' => $c2->id, 'name' => 'Plant Science']);

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

        // 3. Seed Users for 11 System Roles
        $pass = Hash::make('GMU@Demo1');

        $admin = User::firstOrCreate(['email' => 'admin@gmu.edu.et'], ['staff_id' => 'GMU-ADM-01', 'name' => 'System Administrator', 'password' => $pass, 'role' => 'admin']);
        $pi = User::firstOrCreate(['email' => 'pi@gmu.edu.et'], ['staff_id' => 'GMU-STAFF-101', 'name' => 'Dr. Abebe Bikila (PI)', 'password' => $pass, 'role' => 'pi', 'dept_id' => $d1->id]);
        $tm = User::firstOrCreate(['email' => 'tm@gmu.edu.et'], ['staff_id' => 'GMU-STAFF-102', 'name' => 'Tigist Assefa (Co-Researcher)', 'password' => $pass, 'role' => 'tm', 'dept_id' => $d1->id]);
        $reviewer = User::firstOrCreate(['email' => 'reviewer@gmu.edu.et'], ['staff_id' => 'GMU-EXAM-201', 'name' => 'Prof. Kebede Tassew (Reviewer)', 'password' => $pass, 'role' => 'reviewer', 'dept_id' => $d2->id]);
        $dh = User::firstOrCreate(['email' => 'dh@gmu.edu.et'], ['staff_id' => 'GMU-DH-301', 'name' => 'Dr. Chala Gemechu (Dept Head)', 'password' => $pass, 'role' => 'dh', 'dept_id' => $d1->id]);
        $coordinator = User::firstOrCreate(['email' => 'coordinator@gmu.edu.et'], ['staff_id' => 'GMU-COORD-401', 'name' => 'Alemayehu Worku (Research Coordinator)', 'password' => $pass, 'role' => 'coordinator', 'dept_id' => $d1->id]);
        $dean = User::firstOrCreate(['email' => 'dean@gmu.edu.et'], ['staff_id' => 'GMU-DEAN-501', 'name' => 'Dr. Mesfin Haile (College Dean)', 'password' => $pass, 'role' => 'dean', 'dept_id' => $d1->id]);
        $irerc = User::firstOrCreate(['email' => 'irerc@gmu.edu.et'], ['staff_id' => 'GMU-ETHIC-601', 'name' => 'Dr. Sara Mohammed (IRERC Chair)', 'password' => $pass, 'role' => 'irerc', 'dept_id' => $d3->id]);
        $vparttcs = User::firstOrCreate(['email' => 'vp@gmu.edu.et'], ['staff_id' => 'GMU-VP-701', 'name' => 'Prof. Kassahun Zewdie (Vice President)', 'password' => $pass, 'role' => 'vparttcs']);
        $rcsc = User::firstOrCreate(['email' => 'rcsc@gmu.edu.et'], ['staff_id' => 'GMU-PRES-801', 'name' => 'University President / RCSC Chair', 'password' => $pass, 'role' => 'rcsc']);
        $finance = User::firstOrCreate(['email' => 'finance@gmu.edu.et'], ['staff_id' => 'GMU-FIN-901', 'name' => 'Finance Office (Budget Disbursement)', 'password' => $pass, 'role' => 'finance']);

        User::each(function ($user) {
            \App\Services\RbacService::syncUserRole($user);
        });

        // 4. Seed Sample Projects (only if none exist)
        if (Project::count() === 0) {
            Project::create([
                'title' => 'Development of IoT-Based Early Flood Warning System for Anywaa and Nuer Zones',
                'abstract_text' => 'This project aims to deploy solar-powered sensor nodes along Baro River tributaries to provide real-time water level data and SMS warnings to agricultural communities.',
                'thematic_id' => $t2->id,
                'pi_id' => $pi->id,
                'dept_id' => $d1->id,
                'requested_budget' => 450000.00,
                'status' => 'Draft',
                'current_stage' => 0,
                'ethical_cleared' => false,
            ]);

            Project::create([
                'title' => 'High-Yield Flood-Tolerant Rice Seed Propagation in Gambella Lowlands',
                'abstract_text' => 'Evaluation and propagation of NERICA rice varieties across trial plots in Lare and Gambella Zuria districts.',
                'thematic_id' => $t1->id,
                'pi_id' => $pi->id,
                'dept_id' => $d1->id,
                'requested_budget' => 1200000.00,
                'status' => 'Draft',
                'current_stage' => 0,
                'ethical_cleared' => false,
            ]);
        }
    }
}
