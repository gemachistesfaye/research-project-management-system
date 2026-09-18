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
}

