<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Project;
use App\Models\ThematicArea;
use App\Models\Evaluation;
use App\Services\BlindReviewService;
use App\Services\BudgetWorkflowEngine;
use App\Services\LifecycleChangeService;
use Exception;

class RPMSWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RbacSeeder::class);
    }

    public function test_double_blind_peer_review_masking()
    {
        $pi = User::factory()->create(['role' => 'pi', 'name' => 'Dr. Abebe']);
        $reviewer = User::factory()->create(['role' => 'reviewer', 'name' => 'Prof. Kebede']);
        $thematic = ThematicArea::create(['title' => 'Agri Tech']);

        $project = Project::create([
            'title' => 'Test Project',
            'abstract_text' => 'Sample abstract',
            'thematic_id' => $thematic->id,
            'pi_id' => $pi->id,
            'requested_budget' => 300000.00,
        ]);

        $service = new BlindReviewService();
        $anonymized = $service->getAnonymizedProposal($project);

        $this->assertTrue($anonymized['is_masked']);
        $this->assertEquals('Test Project', $anonymized['title']);
        $this->assertArrayNotHasKey('pi_name', $anonymized);
    }

    public function test_dual_threshold_financial_governance_routing()
    {
        $engine = new BudgetWorkflowEngine();

        // Under 500,000 ETB -> Dean Tier
        $this->assertEquals('Dean', $engine->getApprovalTier(450000.00));

        // Equal to or Over 500,000 ETB -> RCSC / Vice President Tier
        $this->assertEquals('RCSC_VP', $engine->getApprovalTier(500000.00));
        $this->assertEquals('RCSC_VP', $engine->getApprovalTier(1200000.00));
    }

    public function test_time_extension_cap_limit_of_three()
    {
        $pi = User::factory()->create(['role' => 'pi']);
        $thematic = ThematicArea::create(['title' => 'Tech']);

        $project = Project::create([
            'title' => 'Extension Test',
            'abstract_text' => 'Abstract',
            'thematic_id' => $thematic->id,
            'pi_id' => $pi->id,
            'requested_budget' => 200000.00,
        ]);

        $service = new LifecycleChangeService();

        // 1st Extension -> Coordinator
        $ext1 = $service->requestExtension($project, 'Reason 1');
        $this->assertEquals(1, $ext1->extension_number);
        $this->assertEquals('Coordinator', $ext1->approver_role);

        // 2nd Extension -> Coordinator
        $ext2 = $service->requestExtension($project, 'Reason 2');
        $this->assertEquals(2, $ext2->extension_number);
        $this->assertEquals('Coordinator', $ext2->approver_role);

        // 3rd Extension -> RCSC
        $ext3 = $service->requestExtension($project, 'Reason 3');
        $this->assertEquals(3, $ext3->extension_number);
        $this->assertEquals('RCSC', $ext3->approver_role);

        // 4th Extension -> Must fail with Exception
        $this->expectException(Exception::class);
        $service->requestExtension($project, 'Reason 4');
    }

    public function test_contract_signing_pi_and_vp_lifecycle()
    {
        $pi = User::factory()->create(['role' => 'pi']);
        $vp = User::factory()->create(['role' => 'vparttcs']);
        $thematic = ThematicArea::create(['title' => 'Health & Technology']);

        $project = Project::create([
            'title' => 'Malaria Detection AI',
            'abstract_text' => 'AI based diagnostics',
            'thematic_id' => $thematic->id,
            'pi_id' => $pi->id,
            'requested_budget' => 450000.00,
            'status' => 'Approved',
        ]);

        // 1. VP tries to sign before PI -> Must fail
        $response = $this->actingAs($vp)->post(route('contracts.sign-vp', $project->project_id));
        $response->assertSessionHas('error', 'PI must sign first before VP can sign.');
        $project->refresh();
        $this->assertNull($project->vp_signature_date);

        // 2. PI signs successfully
        $response = $this->actingAs($pi)->post(route('contracts.sign-pi', $project->project_id));
        $response->assertSessionHas('success', 'PI signature recorded successfully.');
        $project->refresh();
        $this->assertNotNull($project->pi_signature_date);

        // 3. VP signs successfully
        $response = $this->actingAs($vp)->post(route('contracts.sign-vp', $project->project_id));
        $response->assertSessionHas('success', 'VP signature recorded. Project is now Active and Tranche 1 (30% Advance) is queued for finance disbursement.');
        $project->refresh();
        $this->assertNotNull($project->vp_signature_date);
        $this->assertNotNull($project->contract_signed_at);
        $this->assertEquals('Active', $project->status);
    }

    public function test_completion_certificate_issuance_and_download()
    {
        $pi = User::factory()->create(['role' => 'pi']);
        \App\Services\RbacService::syncUserRole($pi);

        $coordinator = User::factory()->create(['role' => 'coordinator']);
        \App\Services\RbacService::syncUserRole($coordinator);

        $thematic = ThematicArea::create(['title' => 'Renewable Energy']);

        $project = Project::create([
            'title' => 'Solar Microgrid Gambella',
            'abstract_text' => 'Renewable solar implementation',
            'thematic_id' => $thematic->id,
            'pi_id' => $pi->id,
            'requested_budget' => 400000.00,
            'status' => 'Completed',
        ]);

        // Coordinator issues completion certificate
        $response = $this->actingAs($coordinator)->post(route('certificates.store'), [
            'project_id' => $project->project_id,
            'type' => 'Completion',
            'issued_to_name' => 'Dr. Abebe Bikila',
        ]);
        $response->assertSessionHas('success', 'Certificate issued successfully.');

        $cert = \App\Models\Certificate::where('project_id', $project->project_id)->first();
        $this->assertNotNull($cert);
        $this->assertStringStartsWith('GMU-CERT-', $cert->certificate_code);

        // PI downloads certificate PDF
        $downloadResponse = $this->actingAs($pi)->get(route('certificates.download', $cert->id));
        $downloadResponse->assertStatus(200);
    }

    public function test_irerc_ethics_review_and_clearance_lifecycle()
    {
        $coordinator = User::factory()->create(['role' => 'coordinator']);
        \App\Services\RbacService::syncUserRole($coordinator);

        $irercOfficer = User::factory()->create(['role' => 'irerc']);
        \App\Services\RbacService::syncUserRole($irercOfficer);

        $thematic = ThematicArea::create(['title' => 'Public Health & Epidemiology']);

        $project = Project::create([
            'title' => 'Clinical Study on Malaria Vector Resistance in Gambella',
            'abstract_text' => 'Clinical diagnostic study involving patient blood samples',
            'thematic_id' => $thematic->id,
            'pi_id' => User::factory()->create(['role' => 'pi'])->id,
            'requested_budget' => 350000.00,
            'status' => 'UnderReview',
        ]);

        // 1. Coordinator routes project to IRERC
        $response = $this->actingAs($coordinator)->post(route('projects.create-irerc-clearance', $project->project_id));
        $response->assertSessionHas('success', 'IRERC ethics clearance created successfully.');

        $clearance = \App\Models\IRERCClearance::where('project_id', $project->project_id)->first();
        $this->assertNotNull($clearance);
        $this->assertEquals('Pending', $clearance->status);

        // 2. IRERC committee reviews and approves with Low Risk
        $irercResponse = $this->actingAs($irercOfficer)->post(route('irerc.decision', $clearance->id), [
            'decision' => 'Approved',
            'risk_level' => 'Low',
            'conditions' => 'Informed consent protocol approved. Data anonymization required.',
        ]);
        $irercResponse->assertSessionHas('success');

        $clearance->refresh();
        $this->assertEquals('Approved', $clearance->status);
        $this->assertStringStartsWith('IRERC-', $clearance->clearance_code);

        $project->refresh();
        $this->assertTrue((bool)$project->ethical_cleared);
    }

    public function test_milestone_approval_triggers_tranche_2_and_tranche_3_generation()
    {
        $pi = User::factory()->create(['role' => 'pi']);
        \App\Services\RbacService::syncUserRole($pi);

        $coordinator = User::factory()->create(['role' => 'coordinator']);
        \App\Services\RbacService::syncUserRole($coordinator);

        $thematic = ThematicArea::create(['title' => 'Agronomy']);

        $project = Project::create([
            'title' => 'Soil Nutrition Analysis Gambella',
            'abstract_text' => 'Soil quality assessment',
            'thematic_id' => $thematic->id,
            'pi_id' => $pi->id,
            'requested_budget' => 600000.00,
            'approved_budget' => 600000.00,
            'status' => 'Active',
        ]);

        // 1. PI submits 50% milestone report
        $report1 = \App\Models\MilestoneReport::create([
            'project_id' => $project->project_id,
            'milestone_name' => 'Mid-term Lab Analysis',
            'progress_percentage' => 50,
            'summary_text' => '50% of soil samples collected and analyzed in lab.',
            'status' => 'Submitted',
        ]);

        // Coordinator approves the report
        $response = $this->actingAs($coordinator)->put(route('progress.update', $report1->id), [
            'coordinator_feedback' => 'Satisfactory lab results verified.',
            'status' => 'Approved',
        ]);
        $response->assertSessionHas('success');

        // Verify Tranche 2 (40% = 240,000 ETB) was auto-generated
        $tranche2 = \App\Models\BudgetRequest::where('project_id', $project->project_id)
            ->where('milestone_phase', 'Tranche 2')
            ->first();
        $this->assertNotNull($tranche2);
        $this->assertEquals(240000.00, (float)$tranche2->approved_amount);
        $this->assertEquals('Approved', $tranche2->status);
        $this->assertEquals('RCSC_VP', $tranche2->approval_tier);

        // 2. PI submits 100% final milestone report
        $report2 = \App\Models\MilestoneReport::create([
            'project_id' => $project->project_id,
            'milestone_name' => 'Final Project Publication & Report',
            'progress_percentage' => 100,
            'summary_text' => 'Final field work completed and published.',
            'status' => 'Submitted',
        ]);

        // Coordinator approves the 100% report
        $response2 = $this->actingAs($coordinator)->put(route('progress.update', $report2->id), [
            'coordinator_feedback' => 'Final deliverables fully accepted.',
            'status' => 'Approved',
        ]);
        $response2->assertSessionHas('success');

        // Verify Tranche 3 (30% = 180,000 ETB) was auto-generated
        $tranche3 = \App\Models\BudgetRequest::where('project_id', $project->project_id)
            ->where('milestone_phase', 'Tranche 3')
            ->first();
        $this->assertNotNull($tranche3);
        $this->assertEquals(180000.00, (float)$tranche3->approved_amount);
        $this->assertEquals('Approved', $tranche3->status);
    }

    public function test_analytics_csv_and_pdf_exports()
    {
        $vp = User::factory()->create(['role' => 'vparttcs']);
        \App\Services\RbacService::syncUserRole($vp);

        $thematic = ThematicArea::create(['title' => 'Hydrology & Water Security']);
        Project::create([
            'title' => 'Baro River Watershed Study',
            'abstract_text' => 'Study on seasonal flood management',
            'thematic_id' => $thematic->id,
            'pi_id' => $vp->id,
            'requested_budget' => 700000.00,
            'status' => 'Active',
        ]);

        // 1. Test CSV Export
        $csvResponse = $this->actingAs($vp)->get(route('analytics.export.csv'));
        $csvResponse->assertStatus(200);
        $csvResponse->assertHeader('Content-Type', 'text/csv; charset=UTF-8');

        // 2. Test PDF Export
        $pdfResponse = $this->actingAs($vp)->get(route('analytics.export.pdf'));
        $pdfResponse->assertStatus(200);
        $pdfResponse->assertHeader('content-type', 'application/pdf');
    }

    public function test_finance_disbursement_lifecycle_and_voucher_exports()
    {
        $financeOfficer = User::factory()->create(['role' => 'finance']);
        \App\Services\RbacService::syncUserRole($financeOfficer);

        $pi = User::factory()->create(['role' => 'pi']);
        \App\Services\RbacService::syncUserRole($pi);

        $thematic = ThematicArea::create(['title' => 'Renewable Energy']);
        $project = Project::create([
            'title' => 'Solar Microgrid Gambella Campus',
            'abstract_text' => 'Deployment of off-grid solar panels',
            'thematic_id' => $thematic->id,
            'pi_id' => $pi->id,
            'requested_budget' => 500000.00,
            'approved_budget' => 500000.00,
            'status' => 'Active',
        ]);

        $tranche1 = \App\Models\BudgetRequest::create([
            'project_id' => $project->project_id,
            'milestone_phase' => 'Tranche 1',
            'requested_amount' => 150000.00,
            'approved_amount' => 150000.00,
            'approval_tier' => 'RCSC_VP',
            'status' => 'Approved',
            'approved_by' => $financeOfficer->id,
        ]);

        // 1. Visit disbursement page
        $pageResponse = $this->actingAs($financeOfficer)->get(route('finance.disbursement'));
        $pageResponse->assertStatus(200);
        $pageResponse->assertSee('Solar Microgrid Gambella Campus');
        $this->assertNotNull($tranche1);
        $this->assertEquals(150000.00, (float)$tranche1->approved_amount);
        $this->assertEquals('Approved', $tranche1->status);

        // 2. Process disbursement release
        $processResponse = $this->actingAs($financeOfficer)->post(route('finance.process-disbursement', $tranche1->request_id), [
            'payment_method' => 'Bank Transfer',
            'notes' => 'CBE Transfer Ref #GMU-TEST-2026',
        ]);
        $processResponse->assertSessionHas('success');

        $tranche1->refresh();
        $this->assertEquals('Released', $tranche1->status);
        $this->assertEquals('Bank Transfer', $tranche1->payment_method);
        $this->assertEquals('CBE Transfer Ref #GMU-TEST-2026', $tranche1->notes);
        $this->assertNotNull($tranche1->disbursed_at);

        // 3. Test Voucher PDF Download
        $voucherPdf = $this->actingAs($financeOfficer)->get(route('finance.voucher', $tranche1->request_id));
        $voucherPdf->assertStatus(200);
        $voucherPdf->assertHeader('content-type', 'application/pdf');

        // 4. Test Voucher In-Browser View
        $voucherView = $this->actingAs($financeOfficer)->get(route('finance.voucher.view', $tranche1->request_id));
        $voucherView->assertStatus(200);
        $voucherView->assertHeader('content-type', 'application/pdf');

        // 5. Test Finance Audit CSV Export
        $csvExport = $this->actingAs($financeOfficer)->get(route('finance.export.csv'));
        $csvExport->assertStatus(200);
        $csvExport->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
    }

    public function test_admin_user_management_lifecycle()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create([
            'role' => 'pi',
            'name' => 'Original Name',
            'email' => 'original@gmu.edu.et',
            'status' => 'active',
            'staff_id' => 'GMU-TEST-001',
        ]);

        // 1. Update user details & role
        $updateResponse = $this->actingAs($admin)->put(route('admin.users.update', $user->id), [
            'staff_id' => 'GMU-TEST-001-MOD',
            'name' => 'Updated Name',
            'email' => 'updated@gmu.edu.et',
            'role' => 'dh',
            'status' => 'active',
        ]);
        $updateResponse->assertSessionHas('success');

        $user->refresh();
        $this->assertEquals('Updated Name', $user->name);
        $this->assertEquals('updated@gmu.edu.et', $user->email);
        $this->assertEquals('dh', $user->role);
        $this->assertEquals('GMU-TEST-001-MOD', $user->staff_id);

        // 2. Toggle status to inactive
        $toggleResponse = $this->actingAs($admin)->post(route('admin.users.toggle-status', $user->id));
        $toggleResponse->assertSessionHas('success');

        $user->refresh();
        $this->assertEquals('inactive', $user->status);

        // Verify deactivated user cannot log in
        $this->post('/logout');
        $loginAttempt = $this->post('/login', [
            'email' => 'updated@gmu.edu.et',
            'password' => 'password',
        ]);
        $loginAttempt->assertSessionHasErrors(['email']);
        $this->assertGuest();

        // 3. Reactivate user
        $reactivateResponse = $this->actingAs($admin)->post(route('admin.users.toggle-status', $user->id));
        $reactivateResponse->assertSessionHas('success');

        $user->refresh();
        $this->assertEquals('active', $user->status);

        // 4. Delete user (with no active projects)
        $deleteResponse = $this->actingAs($admin)->delete(route('admin.users.destroy', $user->id));
        $deleteResponse->assertSessionHas('success');
        $this->assertDatabaseMissing('users', ['id' => $user->id]);

        // 5. Self-deactivation protection
        $selfToggle = $this->actingAs($admin)->post(route('admin.users.toggle-status', $admin->id));
        $selfToggle->assertSessionHas('error');
    }
}

