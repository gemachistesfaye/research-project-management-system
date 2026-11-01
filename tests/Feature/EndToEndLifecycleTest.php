<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Project;
use App\Models\ThematicArea;
use App\Models\College;
use App\Models\Department;
use App\Models\Evaluation;
use App\Models\Certificate;
use App\Models\BudgetRequest;
use App\Models\MilestoneReport;
use App\Services\RbacService;

class EndToEndLifecycleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
        $this->seed(\Database\Seeders\RbacSeeder::class);
    }

    public function test_complete_end_to_end_project_lifecycle_from_submit_to_certificate(): void
    {
        // -------------------------------------------------------------
        // 0. Setup College, Department, Thematic Area, and Role Users
        // -------------------------------------------------------------
        $college = College::create(['name' => 'College of Computing & Informatics', 'code' => 'CCI']);
        $department = Department::create(['name' => 'Software Engineering', 'code' => 'SE', 'college_id' => $college->id]);
        $thematic = ThematicArea::create(['title' => 'Digital Health & AI']);

        $pi = User::factory()->create(['role' => 'pi', 'name' => 'Dr. Gemachis Tesfaye', 'dept_id' => $department->id]);
        RbacService::syncUserRole($pi);

        $dh = User::factory()->create(['role' => 'dh', 'name' => 'Department Head Kebede', 'dept_id' => $department->id]);
        RbacService::syncUserRole($dh);

        $coordinator = User::factory()->create(['role' => 'coordinator', 'name' => 'Coordinator Almaz']);
        RbacService::syncUserRole($coordinator);

        $reviewer1 = User::factory()->create(['role' => 'reviewer', 'name' => 'Reviewer One']);
        RbacService::syncUserRole($reviewer1);

        $reviewer2 = User::factory()->create(['role' => 'reviewer', 'name' => 'Reviewer Two']);
        RbacService::syncUserRole($reviewer2);

        $dean = User::factory()->create(['role' => 'dean', 'name' => 'Dean Chala']);
        RbacService::syncUserRole($dean);

        $vp = User::factory()->create(['role' => 'vparttcs', 'name' => 'VP Academic & Research']);
        RbacService::syncUserRole($vp);

        $finance = User::factory()->create(['role' => 'finance', 'name' => 'Finance Director']);
        RbacService::syncUserRole($finance);

        // -------------------------------------------------------------
        // STEP 1: PI creates and submits proposal
        // -------------------------------------------------------------
        $project = Project::create([
            'title' => 'Smart Healthcare Diagnosis through Machine Learning',
            'abstract_text' => 'A decentralized ML approach to medical image evaluation.',
            'thematic_id' => $thematic->id,
            'college_id' => $college->id,
            'dept_id' => $department->id,
            'pi_id' => $pi->id,
            'requested_budget' => 450000.00,
            'status' => 'Draft',
        ]);

        $this->assertEquals('Draft', $project->status);

        // PI Submits proposal
        $submitRes = $this->actingAs($pi)->post(route('projects.submit', $project->project_id));
        $submitRes->assertSessionHas('success');
        $project->refresh();
        $this->assertEquals('Submitted', $project->status);

        // -------------------------------------------------------------
        // STEP 2: Department Head (DH) screens the proposal
        // -------------------------------------------------------------
        $dhDecisionRes = $this->actingAs($dh)->post(route('dh.screening.decision', $project->project_id), [
            'decision' => 'approve',
            'comments' => 'Proposal is aligned with department research strategy.',
        ]);
        $dhDecisionRes->assertSessionHas('success');
        $project->refresh();
        $this->assertEquals('DH_Screened', $project->status);

        // -------------------------------------------------------------
        // STEP 3: Research Coordinator assigns Double-Blind Reviewers
        // -------------------------------------------------------------
        $assign1Res = $this->actingAs($coordinator)->post(route('projects.assign-reviewer', $project->project_id), [
            'examiner_id' => $reviewer1->id,
            'evaluation_type' => 'Proposal',
        ]);
        $assign1Res->assertSessionHas('success');

        $assign2Res = $this->actingAs($coordinator)->post(route('projects.assign-reviewer', $project->project_id), [
            'examiner_id' => $reviewer2->id,
            'evaluation_type' => 'Proposal',
        ]);
        $assign2Res->assertSessionHas('success');

        $project->refresh();
        $this->assertEquals('UnderReview', $project->status);

        // -------------------------------------------------------------
        // STEP 4: Reviewers evaluate masked proposal using rubrics
        // -------------------------------------------------------------
        $eval1 = Evaluation::where('project_id', $project->project_id)->where('examiner_id', $reviewer1->id)->first();
        $this->assertNotNull($eval1);

        $eval1Res = $this->actingAs($reviewer1)->post(route('evaluations.submit', $eval1->eval_id), [
            'score' => 90,
            'decision' => 'Accepted',
            'comments' => 'Exceptional technical methodology and well-formulated objectives.',
        ]);
        $eval1Res->assertSessionHas('success');

        $eval2 = Evaluation::where('project_id', $project->project_id)->where('examiner_id', $reviewer2->id)->first();
        $this->assertNotNull($eval2);

        $eval2Res = $this->actingAs($reviewer2)->post(route('evaluations.submit', $eval2->eval_id), [
            'score' => 88,
            'decision' => 'Accepted',
            'comments' => 'Strong institutional feasibility and robust expected impact.',
        ]);
        $eval2Res->assertSessionHas('success');

        // EvaluationController automatically routes project to Dean_Review and queues BudgetRequest
        $project->refresh();
        $this->assertEquals('Dean_Review', $project->status);

        $initialBudgetReq = BudgetRequest::where('project_id', $project->project_id)
            ->where('approval_tier', 'Dean')
            ->where('status', 'Pending')
            ->first();
        $this->assertNotNull($initialBudgetReq);

        // -------------------------------------------------------------
        // STEP 5: Dual-Threshold Approval (< 500k ETB -> College Dean)
        // -------------------------------------------------------------
        $deanRes = $this->actingAs($dean)->post(route('dean.decision', $initialBudgetReq->request_id), [
            'decision' => 'Approved',
            'approved_amount' => 450000.00,
            'comments' => 'Approved under College Dean threshold authority.',
        ]);
        $deanRes->assertSessionHas('success');
        $project->refresh();
        $this->assertEquals('Approved', $project->status);
        $this->assertEquals(450000.00, (float)$project->approved_budget);

        // -------------------------------------------------------------
        // STEP 6: Contract Signing (PI first, then VP)
        // -------------------------------------------------------------
        $piSignRes = $this->actingAs($pi)->post(route('contracts.sign-pi', $project->project_id));
        $piSignRes->assertSessionHas('success');

        $vpSignRes = $this->actingAs($vp)->post(route('contracts.sign-vp', $project->project_id));
        $vpSignRes->assertSessionHas('success');

        $project->refresh();
        $this->assertEquals('Active', $project->status);

        // Tranche 1 (30% = 135,000 ETB) should exist
        $tranche1 = BudgetRequest::where('project_id', $project->project_id)
            ->where('milestone_phase', 'Tranche 1')
            ->first();
        $this->assertNotNull($tranche1);
        $this->assertEquals(135000.00, (float)$tranche1->requested_amount);

        // -------------------------------------------------------------
        // STEP 7: Milestones & Tranches 2 and 3
        // -------------------------------------------------------------
        // PI submits 50% Milestone Report
        $report1 = MilestoneReport::create([
            'project_id' => $project->project_id,
            'milestone_name' => 'Mid-term Dataset Collection & Pipeline',
            'progress_percentage' => 50,
            'summary_text' => 'Dataset collected and pre-processing completed.',
            'status' => 'Submitted',
        ]);

        // Coordinator approves Milestone 1
        $coordApprove1 = $this->actingAs($coordinator)->put(route('progress.update', $report1->id), [
            'status' => 'Approved',
            'coordinator_feedback' => 'Verified dataset and progress milestones.',
        ]);
        $coordApprove1->assertSessionHas('success');

        // Tranche 2 (40% = 180,000 ETB) should be generated
        $tranche2 = BudgetRequest::where('project_id', $project->project_id)
            ->where('milestone_phase', 'Tranche 2')
            ->first();
        $this->assertNotNull($tranche2);
        $this->assertEquals(180000.00, (float)$tranche2->requested_amount);

        // PI submits 100% Final Report
        $report2 = MilestoneReport::create([
            'project_id' => $project->project_id,
            'milestone_name' => 'Final Implementation & Paper Publication',
            'progress_percentage' => 100,
            'summary_text' => 'Model trained, tested, and manuscript submitted.',
            'status' => 'Submitted',
        ]);

        // Coordinator approves Final Milestone
        $coordApprove2 = $this->actingAs($coordinator)->put(route('progress.update', $report2->id), [
            'status' => 'Approved',
            'coordinator_feedback' => 'Final project deliverables verified.',
        ]);
        $coordApprove2->assertSessionHas('success');

        // Tranche 3 (30% = 135,000 ETB) should be generated
        $tranche3 = BudgetRequest::where('project_id', $project->project_id)
            ->where('milestone_phase', 'Tranche 3')
            ->first();
        $this->assertNotNull($tranche3);
        $this->assertEquals(135000.00, (float)$tranche3->requested_amount);

        // -------------------------------------------------------------
        // STEP 8: Mark Complete
        // -------------------------------------------------------------
        $completeRes = $this->actingAs($coordinator)->post(route('projects.complete', $project->project_id));
        $completeRes->assertSessionHas('success');
        $project->refresh();
        $this->assertEquals('Completed', $project->status);

        // -------------------------------------------------------------
        // STEP 9: Certificate Generation & Download
        // -------------------------------------------------------------
        $certRes = $this->actingAs($coordinator)->post(route('certificates.store'), [
            'project_id' => $project->project_id,
            'type' => 'Completion',
            'issued_to_name' => 'Dr. Gemachis Tesfaye',
        ]);
        $certRes->assertSessionHas('success');

        $cert = Certificate::where('project_id', $project->project_id)->first();
        $this->assertNotNull($cert);
        $this->assertEquals('Completion', $cert->type);
        $this->assertStringStartsWith('UNI-CERT-', $cert->certificate_code);

        // View Certificate
        $viewRes = $this->actingAs($pi)->get(route('certificates.view', $cert->id));
        $viewRes->assertStatus(200);

        // Download Certificate PDF
        $downloadRes = $this->actingAs($pi)->get(route('certificates.download', $cert->id));
        $downloadRes->assertStatus(200);
        $this->assertStringContainsString('application/pdf', $downloadRes->headers->get('content-type'));
    }
}
