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
            ['title' => 'Climate Resilience & Agricultural Productivity in Institution Region'],
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
        $pass = Hash::make('UNI@Demo1');

        $admin = User::firstOrCreate(['email' => 'admin@institution.org'], ['staff_id' => 'UNI-ADM-01', 'name' => 'System Administrator', 'password' => $pass, 'role' => 'admin']);
        $pi = User::firstOrCreate(['email' => 'pi@institution.org'], ['staff_id' => 'UNI-STAFF-101', 'name' => 'Dr. Abebe Bikila', 'password' => $pass, 'role' => 'pi', 'dept_id' => $d1->id]);
        $tm = User::firstOrCreate(['email' => 'tm@institution.org'], ['staff_id' => 'UNI-STAFF-102', 'name' => 'Tigist Assefa', 'password' => $pass, 'role' => 'tm', 'dept_id' => $d1->id]);
        $reviewer = User::firstOrCreate(['email' => 'reviewer@institution.org'], ['staff_id' => 'UNI-EXAM-201', 'name' => 'Prof. Kebede Tassew', 'password' => $pass, 'role' => 'reviewer', 'dept_id' => $d2->id]);
        $dh = User::firstOrCreate(['email' => 'dh@institution.org'], ['staff_id' => 'UNI-DH-301', 'name' => 'Dr. Chala Gemechu', 'password' => $pass, 'role' => 'dh', 'dept_id' => $d1->id]);
        $coordinator = User::firstOrCreate(['email' => 'coordinator@institution.org'], ['staff_id' => 'UNI-COORD-401', 'name' => 'Alemayehu Worku', 'password' => $pass, 'role' => 'coordinator', 'dept_id' => $d1->id]);
        $dean = User::firstOrCreate(['email' => 'dean@institution.org'], ['staff_id' => 'UNI-DEAN-501', 'name' => 'Dr. Mesfin Haile', 'password' => $pass, 'role' => 'dean', 'dept_id' => $d1->id]);
        $irerc = User::firstOrCreate(['email' => 'irerc@institution.org'], ['staff_id' => 'UNI-ETHIC-601', 'name' => 'Dr. Sara Mohammed', 'password' => $pass, 'role' => 'irerc', 'dept_id' => $d3->id]);
        $vparttcs = User::firstOrCreate(['email' => 'vp@institution.org'], ['staff_id' => 'UNI-VP-701', 'name' => 'Prof. Kassahun Zewdie', 'password' => $pass, 'role' => 'vparttcs']);
        $rcsc = User::firstOrCreate(['email' => 'rcsc@institution.org'], ['staff_id' => 'UNI-PRES-801', 'name' => 'Institution President', 'password' => $pass, 'role' => 'rcsc']);
        $finance = User::firstOrCreate(['email' => 'finance@institution.org'], ['staff_id' => 'UNI-FIN-901', 'name' => 'Finance Office', 'password' => $pass, 'role' => 'finance']);

        User::each(function ($user) {
            \App\Services\RbacService::syncUserRole($user);
        });

        // 4. Seed Sample Projects (only if none exist)
        if (Project::count() === 0) {
            Project::create([
                'title' => 'Development of IoT-Based Early Flood Warning System for Anywaa and Nuer Lowland Zones',
                'abstract_text' => 'This research deploys solar-powered telemetry sensor nodes along Baro and Akobo river tributaries to transmit real-time water stage metrics and SMS alerts to vulnerable agrarian communities.',
                'thematic_id' => $t2->id,
                'pi_id' => $pi->id,
                'dept_id' => $d1->id,
                'requested_budget' => 480000.00,
                'status' => 'Draft',
                'current_stage' => 1,
                'ethical_cleared' => false,
            ]);

            Project::create([
                'title' => 'Sustainable Agro-Forestry and Soil Carbon Sequestration Modeling in Institution National Park Buffer Zones',
                'abstract_text' => 'An empirical investigation into indigenous agro-forestry practices, measuring soil organic carbon dynamics and evaluating community-led reforestation strategies across protected buffer corridors.',
                'thematic_id' => $t1->id,
                'pi_id' => $pi->id,
                'dept_id' => $d1->id,
                'requested_budget' => 750000.00,
                'status' => 'Draft',
                'current_stage' => 1,
                'ethical_cleared' => false,
            ]);

            Project::create([
                'title' => 'Epidemiological Surveillance and Vector-Borne Disease Mapping Using GIS in Itang Special Woreda',
                'abstract_text' => 'Spatial-temporal modeling of seasonal malaria and neglected tropical disease transmission patterns in riparian ecosystems using high-resolution satellite imagery and localized health post telemetry.',
                'thematic_id' => $t3->id,
                'pi_id' => $pi->id,
                'dept_id' => $d1->id,
                'requested_budget' => 395000.00,
                'status' => 'Draft',
                'current_stage' => 1,
                'ethical_cleared' => false,
            ]);

            Project::create([
                'title' => 'Solar-Powered Off-Grid Cold Chain Network for Indigenous Fishery Post-Harvest Preservation in Openo Basin',
                'abstract_text' => 'Design, prototyping, and community field testing of decentralized solar thermal cooling units to minimize post-harvest fish spoilage along Baro-Akobo river landing sites.',
                'thematic_id' => $t2->id,
                'pi_id' => $pi->id,
                'dept_id' => $d1->id,
                'requested_budget' => 920000.00,
                'status' => 'Draft',
                'current_stage' => 1,
                'ethical_cleared' => false,
            ]);
        }

        // 5. Seed Initial System Audit Logs (if empty)
        if (\App\Models\AuditLog::count() === 0) {
            \App\Services\AuditService::log('SYSTEM_INIT', 'System', null, 'RPMS Institutional Governance Database Initialized with RBAC v3.0 matrix', $admin->id);
            \App\Services\AuditService::log('USER_SEED', 'User', $admin->id, 'System Administrator account provisioned (UNI-ADM-01)', $admin->id);
            \App\Services\AuditService::log('USER_SEED', 'User', $pi->id, 'Academic PI account provisioned (UNI-STAFF-101)', $admin->id);
            \App\Services\AuditService::log('THEMATIC_SETUP', 'ThematicArea', $t1->id, 'Thematic Area: Climate Resilience & Agriculture established', $admin->id);
        }
    }
}
