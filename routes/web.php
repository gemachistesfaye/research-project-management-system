<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\EvaluationController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\LifecycleController;
use App\Http\Controllers\GovernanceController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProgressReportController;
use App\Http\Controllers\TeamMemberController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('login');
});

// Authentication
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Authenticated System Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile (all authenticated users)
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'changePassword'])->name('profile.change-password');

    // Demo Role Switcher (accessible to all authenticated users for prototype demo)
    Route::get('/switch-role/{role}', function ($role) {
        $user = \App\Models\User::where('role', $role)->first();
        if ($user) {
            \Illuminate\Support\Facades\Auth::login($user);
            return redirect()->route('dashboard')->with('success', 'Switched demo view to role: ' . strtoupper($role));
        }
        return back()->with('error', 'Role user not found.');
    })->name('switch-role');

    // PI-only Project Actions (must be declared before wildcard /projects/{id})
    Route::middleware(['role:pi', 'permission:create_project'])->group(function () {
        Route::get('/projects/create', [ProjectController::class, 'create'])->name('projects.create');
        Route::post('/projects', [ProjectController::class, 'store'])->name('projects.store');
        Route::post('/projects/draft', [ProjectController::class, 'storeDraft'])->name('projects.store-draft');
        Route::post('/projects/{id}/submit', [ProjectController::class, 'submit'])->name('projects.submit');
    });

    Route::middleware(['permission:request_extension'])->group(function () {
        Route::post('/projects/{id}/request-extension', [LifecycleController::class, 'requestExtension'])->name('projects.request-extension');
    });

    Route::middleware(['permission:request_amendment'])->group(function () {
        Route::post('/projects/{id}/request-amendment', [LifecycleController::class, 'requestAmendment'])->name('projects.request-amendment');
    });

    Route::middleware(['permission:request_termination'])->group(function () {
        Route::post('/projects/{id}/terminate', [LifecycleController::class, 'terminate'])->name('projects.terminate');
        Route::post('/projects/{id}/complete', [ProjectController::class, 'markComplete'])->name('projects.complete');
    });

    // Projects (all authenticated users can view)
    Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');
    Route::get('/projects/{id}', [ProjectController::class, 'show'])->whereNumber('id')->name('projects.show');

    // Project Edit (PI: Draft only)
    Route::get('/projects/{id}/edit', [ProjectController::class, 'edit'])->whereNumber('id')->name('projects.edit');
    Route::put('/projects/{id}', [ProjectController::class, 'update'])->whereNumber('id')->name('projects.update');

    // Project Delete (PI: Draft only)
    Route::delete('/projects/{id}', [ProjectController::class, 'destroy'])->whereNumber('id')->name('projects.destroy');

    // Project Cancel (PI: simple cancel for Draft/Submitted/DH_Screened/UnderReview)
    Route::post('/projects/{id}/cancel', [ProjectController::class, 'cancel'])->name('projects.cancel');

    // Project Request Cancel (PI: request cancel for Approved/Active, needs admin approval)
    Route::post('/projects/{id}/request-cancel', [ProjectController::class, 'requestCancel'])->name('projects.request-cancel');

    // Admin/Dean/RCSC/Coordinator: Approve/Reject Cancellation
    Route::post('/projects/{id}/approve-cancel', [ProjectController::class, 'approveCancel'])->name('projects.approve-cancel');
    Route::post('/projects/{id}/reject-cancel', [ProjectController::class, 'rejectCancel'])->name('projects.reject-cancel');

    // Coordinator-only: Assign Reviewers
    Route::middleware(['role:coordinator', 'permission:assign_reviewer'])->group(function () {
        Route::post('/projects/{id}/assign-reviewer', [ProjectController::class, 'assignReviewer'])->name('projects.assign-reviewer');
    });

    // Coordinator-only: Create IRERC Clearance for eligible projects
    Route::middleware(['role:coordinator', 'permission:assign_reviewer'])->group(function () {
        Route::post('/projects/{id}/create-irerc-clearance', [GovernanceController::class, 'createIrercClearance'])->name('projects.create-irerc-clearance');
    });

    // Reviewer-only: Peer Review Evaluation
    Route::middleware(['role:reviewer', 'permission:submit_evaluation'])->group(function () {
        Route::get('/evaluations', [EvaluationController::class, 'index'])->name('evaluations.index');
        Route::get('/evaluations/{evalId}', [EvaluationController::class, 'show'])->name('evaluations.show');
        Route::post('/evaluations/{evalId}/submit', [EvaluationController::class, 'submitScore'])->name('evaluations.submit');
    });

    // SCR-09: Progress Reports
    Route::get('/progress', [ProgressReportController::class, 'index'])->name('progress.index');
    Route::get('/progress/{projectId}', [ProgressReportController::class, 'show'])->name('progress.show');
    Route::middleware(['permission:submit_progress_report'])->group(function () {
        Route::post('/progress/{projectId}', [ProgressReportController::class, 'store'])->name('progress.store');
    });
    Route::middleware(['role:coordinator,admin', 'permission:review_progress_report'])->group(function () {
        Route::put('/progress/report/{reportId}', [ProgressReportController::class, 'update'])->name('progress.update');
    });

    // SCR-23: Team Member Management
    Route::middleware(['permission:manage_team'])->group(function () {
        Route::get('/projects/{projectId}/team', [TeamMemberController::class, 'index'])->name('team.index');
        Route::post('/projects/{projectId}/team', [TeamMemberController::class, 'store'])->name('team.store');
        Route::put('/team/{memberId}', [TeamMemberController::class, 'update'])->name('team.update');
        Route::delete('/team/{memberId}', [TeamMemberController::class, 'destroy'])->name('team.destroy');
    });

    // SCR-08: Contract Signing (PI and VP sign; Coordinator and Admin can view)
    Route::middleware(['role:pi,vparttcs,coordinator,admin'])->group(function () {
        Route::get('/contracts/{id}/sign', function ($id) {
            $project = \App\Models\Project::findOrFail($id);
            $user = \Auth::user();
            if ($user->role === 'pi' && (int) $project->pi_id !== (int) $user->id) {
                abort(403, 'You can only view contracts for your own projects.');
            }
            $pi_signed = !empty($project->pi_signature_date);
            $vp_signed = !empty($project->vp_signature_date);
            $pi_signed_date = $project->pi_signature_date;
            $vp_signed_date = $project->vp_signature_date;
            return view('contracts.sign', compact('project', 'pi_signed', 'vp_signed', 'pi_signed_date', 'vp_signed_date'));
        })->name('contracts.show');
        Route::get('/contracts/{id}/download', function ($id) {
            $project = \App\Models\Project::with(['pi', 'thematicArea', 'department'])->findOrFail($id);
            $user = \Auth::user();
            if ($user->role === 'pi' && (int) $project->pi_id !== (int) $user->id) {
                abort(403, 'You can only download contracts for your own projects.');
            }
            if (class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
                $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('contracts.pdf', compact('project'));
                return $pdf->download('Contract-Project-' . $project->project_id . '.pdf');
            }
            return redirect()->route('contracts.show', $id);
        })->name('contracts.download');
        Route::post('/contracts/{id}/sign-pi', function ($id) {
            $project = \App\Models\Project::with('irercClearance')->findOrFail($id);
            if ((int) \Auth::id() !== (int) $project->pi_id && \Auth::user()->role !== 'admin') {
                return back()->with('error', 'Only the PI can sign as PI.');
            }
            if ($project->status !== 'Approved') {
                return back()->with('error', 'Project must be approved before signing contracts.');
            }
            if ($project->irercClearance && $project->irercClearance->status !== 'Approved') {
                return back()->with('error', 'Project is undergoing ethics review (IRERC). Contract cannot be signed until ethics clearance is Approved.');
            }
            $project->update(['pi_signature_date' => now()]);
            return back()->with('success', 'PI signature recorded successfully.');
        })->name('contracts.sign-pi');
        Route::post('/contracts/{id}/sign-vp', function ($id) {
            $project = \App\Models\Project::with('irercClearance')->findOrFail($id);
            if (!in_array(\Auth::user()->role, ['vparttcs', 'admin'])) {
                return back()->with('error', 'Only the Vice President (ARTTCS) or System Admin can sign this contract as VP.');
            }
            if ($project->status !== 'Approved') {
                return back()->with('error', 'Project budget must be ratified and approved before VP contract authorization.');
            }
            if (!$project->pi_signature_date) {
                return back()->with('error', 'PI must sign first before VP can sign.');
            }
            if ($project->irercClearance && $project->irercClearance->status !== 'Approved') {
                return back()->with('error', 'Ethics clearance is required before VP contract authorization.');
            }
            $project->update([
                'vp_signature_date' => now(),
                'contract_signed_at' => now(),
                'status' => 'Active',
                'activated_at' => now(),
            ]);

            // Auto-queue Tranche 1 (30% Advance) for Finance Disbursement
            $existingTranche1 = \App\Models\BudgetRequest::where('project_id', $project->project_id)
                ->where('milestone_phase', 'Tranche 1')
                ->first();
            if (!$existingTranche1) {
                $approvedBudget = $project->approved_budget ?: $project->requested_budget;
                $tranche1Amount = round($approvedBudget * 0.30, 2);
                $tier = ($approvedBudget >= 500000) ? 'RCSC_VP' : 'Dean';

                \App\Models\BudgetRequest::create([
                    'project_id' => $project->project_id,
                    'milestone_phase' => 'Tranche 1',
                    'requested_amount' => $tranche1Amount,
                    'approved_amount' => $tranche1Amount,
                    'approval_tier' => $tier,
                    'status' => 'Approved',
                    'approved_by' => \Auth::id(),
                ]);
            }

            return back()->with('success', 'VP signature recorded. Project is now Active and Tranche 1 (30% Advance) is queued for finance disbursement.');
        })->name('contracts.sign-vp');
    });

    // SCR-14: Finance Disbursement (Finance only)
    Route::middleware(['role:finance', 'permission:process_disbursement'])->group(function () {
        Route::get('/finance/disbursement', function () {
            // Ensure all Active projects have Tranche 1 queued if not already present
            $activeProjects = \App\Models\Project::where('status', 'Active')->with('milestoneReports')->get();
            foreach ($activeProjects as $p) {
                $approvedBudget = $p->approved_budget ?: $p->requested_budget;
                $tier = ($approvedBudget >= 500000) ? 'RCSC_VP' : 'Dean';

                $hasTranche1 = \App\Models\BudgetRequest::where('project_id', $p->project_id)
                    ->where('milestone_phase', 'Tranche 1')
                    ->exists();
                if (!$hasTranche1) {
                    $tranche1Amount = round($approvedBudget * 0.30, 2);
                    \App\Models\BudgetRequest::create([
                        'project_id' => $p->project_id,
                        'milestone_phase' => 'Tranche 1',
                        'requested_amount' => $tranche1Amount,
                        'approved_amount' => $tranche1Amount,
                        'approval_tier' => $tier,
                        'status' => 'Approved',
                        'approved_by' => \Auth::id() ?? $p->pi_id,
                    ]);
                }

                // Check if project has an approved milestone report and Tranche 2 is not yet created
                $hasApprovedMilestone = $p->milestoneReports->whereIn('status', ['Approved', 'Coordinator_Audited'])->count() > 0;
                $hasTranche2 = \App\Models\BudgetRequest::where('project_id', $p->project_id)
                    ->where('milestone_phase', 'Tranche 2')
                    ->exists();

                if ($hasApprovedMilestone && !$hasTranche2) {
                    $tranche2Amount = round($approvedBudget * 0.40, 2);
                    \App\Models\BudgetRequest::create([
                        'project_id' => $p->project_id,
                        'milestone_phase' => 'Tranche 2',
                        'requested_amount' => $tranche2Amount,
                        'approved_amount' => $tranche2Amount,
                        'approval_tier' => $tier,
                        'status' => 'Approved',
                        'approved_by' => \Auth::id() ?? $p->pi_id,
                    ]);
                }
            }

            $pendingRequests = \App\Models\BudgetRequest::where('status', 'Approved')
                ->where('milestone_phase', 'like', 'Tranche%')
                ->whereHas('project', function ($q) {
                    $q->whereIn('status', ['Active', 'Approved', 'Completed']);
                })
                ->with(['project.pi', 'project.thematicArea', 'project.department'])
                ->get();
            $disbursedHistory = \App\Models\BudgetRequest::where('status', 'Released')
                ->with(['project.pi', 'project.thematicArea', 'project.department'])
                ->latest()
                ->get();
            return view('finance.disbursement', compact('pendingRequests', 'disbursedHistory'));
        })->name('finance.disbursement');
        Route::post('/finance/disbursement/{id}/process', function ($id) {
            request()->validate([
                'payment_method' => 'required|in:Cash,Check,Bank Transfer',
                'notes' => 'nullable|string',
            ]);

            $req = \App\Models\BudgetRequest::findOrFail($id);

            if ($req->status !== 'Approved') {
                return back()->with('error', 'This budget request has already been processed or is not eligible for disbursement.');
            }

            $project = \App\Models\Project::where('project_id', $req->project_id)->first();
            if (!$project) {
                return back()->with('error', 'Associated project not found.');
            }

            $eligibleStatuses = ['UnderReview', 'Dean_Review', 'Approved', 'Active', 'Completed'];
            if (!in_array($project->status, $eligibleStatuses)) {
                return back()->with('error', 'This project is not eligible for disbursement at its current status.');
            }

            $amount = $req->approved_amount;
            if ($req->approved_amount && $amount > $req->approved_amount) {
                return back()->with('error', 'Disbursement amount cannot exceed the approved amount of ETB ' . number_format($req->approved_amount, 2) . '.');
            }

            $req->update([
                'status' => 'Released',
                'disbursed_at' => now(),
                'payment_method' => request('payment_method'),
                'notes' => request('notes'),
            ]);

            return back()->with('success', 'Disbursement of ETB ' . number_format($amount, 2) . ' processed successfully.');
        })->name('finance.process-disbursement');
    });

    // SCR-20: Procurement Tracker
    Route::middleware(['permission:submit_procurement|manage_procurement'])->group(function () {
        Route::get('/procurement', function () {
            $user = \Auth::user();
            if ($user->role === 'coordinator') {
                $requests = \App\Models\ProcurementRequest::with('project')->latest()->get();
                $activeProjects = \App\Models\Project::whereIn('status', ['Active', 'Approved'])->get();
            } else {
                $requests = \App\Models\ProcurementRequest::whereHas('project', function ($q) use ($user) {
                    $q->where('pi_id', $user->id);
                })->with('project')->latest()->get();
                $activeProjects = \App\Models\Project::where('pi_id', $user->id)->whereIn('status', ['Active', 'Approved'])->get();
            }

            $categoryFilter = request('category');
            if ($categoryFilter && $categoryFilter !== 'All') {
                $requests = $requests->filter(fn($r) => $r->category === $categoryFilter);
            }

            $budgetInfo = $activeProjects->map(function ($project) {
                $totalSpent = $project->procurementRequests()->whereIn('status', ['Approved', 'Purchased'])->sum('estimated_cost');
                $pendingSpend = $project->procurementRequests()->where('status', 'Pending')->sum('estimated_cost');
                return [
                    'project_id' => $project->project_id,
                    'title' => $project->title,
                    'approved_budget' => $project->approved_budget ?? 0,
                    'total_spent' => $totalSpent,
                    'pending_spend' => $pendingSpend,
                    'remaining' => ($project->approved_budget ?? 0) - $totalSpent,
                    'remaining_after_pending' => ($project->approved_budget ?? 0) - $totalSpent - $pendingSpend,
                ];
            });

            return view('procurement.index', compact('requests', 'activeProjects', 'budgetInfo'));
        })->name('procurement.index');
    });
    Route::middleware(['role:pi', 'permission:submit_procurement'])->group(function () {
        Route::post('/procurement', function () {
            request()->validate([
                'item_name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'category' => 'required|in:Equipment,Reagents,Consumables,Other',
                'estimated_cost' => 'required|numeric|min:0',
                'project_id' => 'required|exists:projects,project_id',
            ]);
            $project = \App\Models\Project::where('project_id', request('project_id'))->first();
            if ((int) $project->pi_id !== (int) \Auth::id()) {
                return back()->with('error', 'You can only submit procurement requests for your own projects.');
            }
            \App\Models\ProcurementRequest::create(array_merge(request()->all(), ['status' => 'Pending']));
            return back()->with('success', 'Purchase request submitted successfully.');
        })->name('procurement.store');
    });
    Route::middleware(['role:coordinator', 'permission:manage_procurement'])->group(function () {
        Route::post('/procurement/{id}/approve', function ($id) {
            \App\Models\ProcurementRequest::findOrFail($id)->update(['status' => 'Approved']);
            return back()->with('success', 'Purchase request approved.');
        })->name('procurement.approve');
        Route::post('/procurement/{id}/reject', function ($id) {
            \App\Models\ProcurementRequest::findOrFail($id)->update(['status' => 'Rejected']);
            return back()->with('success', 'Purchase request rejected.');
        })->name('procurement.reject');
    });

    // SCR-22: PI Transfer
    Route::middleware(['permission:request_pi_transfer|approve_pi_transfer'])->group(function () {
        Route::get('/pitransfer', function () {
            $user = \Auth::user();
            $myProjects = \App\Models\Project::where('pi_id', $user->id)->whereIn('status', ['Active', 'Approved'])->get();
            $eligibleUsers = \App\Models\User::where('id', '!=', $user->id)->whereIn('role', ['pi', 'tm'])->get();
            $pendingTransfers = \App\Models\PITransfer::where('status', 'Pending')->with('project', 'oldPi', 'newPi')->latest()->get();
            $completedTransfers = \App\Models\PITransfer::where('status', '!=', 'Pending')->with('project', 'oldPi', 'newPi')->latest()->get();
            return view('pitransfer.index', compact('myProjects', 'eligibleUsers', 'pendingTransfers', 'completedTransfers'));
        })->name('pitransfer.index');
    });
    Route::middleware(['role:pi', 'permission:request_pi_transfer'])->group(function () {
        Route::post('/pitransfer', function () {
            request()->validate([
                'project_id' => 'required|exists:projects,project_id',
                'new_pi_id' => 'required|exists:users,id',
                'reason' => 'required|string',
                'consent_document' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
            ]);
            $project = \App\Models\Project::where('project_id', request('project_id'))->first();
            if ((int) $project->pi_id !== (int) \Auth::id()) {
                return back()->with('error', 'You can only request PI transfer for your own projects.');
            }
            $docUrl = null;
            if (request()->hasFile('consent_document')) {
                $docUrl = request()->file('consent_document')->store('transfers', 'public');
            }
            \App\Models\PITransfer::create([
                'project_id' => request('project_id'),
                'old_pi_id' => \Auth::id(),
                'new_pi_id' => request('new_pi_id'),
                'reason' => request('reason'),
                'consent_document_url' => $docUrl,
                'status' => 'Pending',
            ]);
            return back()->with('success', 'PI transfer request submitted for approval.');
        })->name('pitransfer.store');
    });
    Route::middleware(['role:coordinator,vparttcs', 'permission:approve_pi_transfer'])->group(function () {
        Route::post('/pitransfer/{id}/approve', function ($id) {
            $transfer = \App\Models\PITransfer::findOrFail($id);
            $transfer->update(['status' => 'Approved', 'approved_by' => \Auth::id()]);
            \App\Models\Project::where('project_id', $transfer->project_id)->update(['pi_id' => $transfer->new_pi_id]);
            return back()->with('success', 'PI transfer approved and project updated.');
        })->name('pitransfer.approve');
        Route::post('/pitransfer/{id}/reject', function ($id) {
            \App\Models\PITransfer::findOrFail($id)->update(['status' => 'Rejected']);
            return back()->with('success', 'PI transfer request rejected.');
        })->name('pitransfer.reject');
    });

    // SCR-10: Extensions & Amendments
    Route::get('/extensions', function () {
        return view('extensions.index');
    })->name('extensions.index');
    Route::middleware(['role:coordinator,vparttcs,rcsc', 'permission:approve_extensions'])->group(function () {
        Route::post('/extensions/{id}/approve', function ($id) {
            $ext = \App\Models\ProjectExtension::findOrFail($id);
            $ext->update(['status' => 'Approved', 'approved_by' => \Auth::id()]);
            return back()->with('success', 'Extension approved.');
        })->name('extensions.approve');
        Route::post('/extensions/{id}/reject', function ($id) {
            $ext = \App\Models\ProjectExtension::findOrFail($id);
            $ext->update(['status' => 'Rejected']);
            return back()->with('success', 'Extension rejected.');
        })->name('extensions.reject');
    });
    Route::middleware(['role:coordinator,vparttcs,rcsc', 'permission:approve_amendments'])->group(function () {
        Route::post('/amendments/{id}/approve', function ($id) {
            $amend = \App\Models\BudgetAmendment::findOrFail($id);
            $amend->update(['status' => 'Approved', 'approved_by' => \Auth::id()]);
            return back()->with('success', 'Budget amendment approved.');
        })->name('amendments.approve');
        Route::post('/amendments/{id}/reject', function ($id) {
            $amend = \App\Models\BudgetAmendment::findOrFail($id);
            $amend->update(['status' => 'Rejected']);
            return back()->with('success', 'Budget amendment rejected.');
        })->name('amendments.reject');
    });

    // SCR-11: Termination & Refund
    Route::get('/termination', function () {
        return view('termination.index');
    })->name('termination.index');
    Route::middleware(['role:coordinator,admin', 'permission:approve_termination'])->group(function () {
        Route::post('/termination/{id}/approve', function ($id) {
            $term = \App\Models\ProjectTermination::findOrFail($id);
            $term->update(['status' => 'Approved']);
            return back()->with('success', 'Termination approved.');
        })->name('termination.approve');
        Route::post('/termination/{id}/reject', function ($id) {
            $term = \App\Models\ProjectTermination::findOrFail($id);
            $term->update(['status' => 'Rejected']);
            return back()->with('success', 'Termination rejected.');
        })->name('termination.reject');
    });

    // Budget Approvals (Dean, RCSC, VP)
    Route::middleware(['role:dean,rcsc,vparttcs', 'permission:approve_budget'])->group(function () {
        Route::post('/projects/{id}/approve-budget', [BudgetController::class, 'approveProjectBudget'])->name('projects.approve-budget');
    });

    // Role-Specific Governance Pages
    Route::middleware(['role:dh', 'permission:screen_proposals'])->group(function () {
        Route::get('/dh/screening', [GovernanceController::class, 'dhScreening'])->name('dh.screening');
        Route::post('/dh/screening/{id}/decision', [GovernanceController::class, 'dhScreeningDecision'])->name('dh.screening.decision');
    });

    Route::middleware(['role:coordinator,admin', 'permission:manage_certificates'])->group(function () {
        Route::get('/certificates', [GovernanceController::class, 'certificates'])->name('certificates');
        Route::post('/certificates', [GovernanceController::class, 'storeCertificate'])->name('certificates.store');
    });

    Route::middleware(['role:coordinator,pi,vparttcs,admin'])->group(function () {
        Route::get('/certificates/{id}/download', [GovernanceController::class, 'downloadCertificate'])->name('certificates.download');
    });

    Route::middleware(['role:coordinator', 'permission:view_projects'])->group(function () {
        Route::get('/coordinator/hub', [GovernanceController::class, 'coordinatorHub'])->name('coordinator.hub');
    });

    Route::middleware(['role:dean', 'permission:approve_budget'])->group(function () {
        Route::get('/dean/approvals', [GovernanceController::class, 'deanApprovals'])->name('dean.approvals');
        Route::post('/dean/approvals/{id}/decision', [GovernanceController::class, 'deanDecision'])->name('dean.decision');
    });

    Route::middleware(['role:irerc', 'permission:ethics_review'])->group(function () {
        Route::get('/irerc/panel', [GovernanceController::class, 'irercPanel'])->name('irerc.panel');
        Route::post('/irerc/panel/{id}/decision', [GovernanceController::class, 'irercDecision'])->name('irerc.decision');
    });

    Route::middleware(['role:rcsc,vparttcs', 'permission:view_rcsc_portal'])->group(function () {
        Route::get('/rcsc/portal', [GovernanceController::class, 'rcscPortal'])->name('rcsc.portal');
    });

    Route::middleware(['role:rcsc,vparttcs', 'permission:approve_budget'])->group(function () {
        Route::post('/rcsc/portal/{id}/decision', [GovernanceController::class, 'rcscDecision'])->name('rcsc.decision');
    });

    Route::middleware(['role:rcsc,vparttcs', 'permission:view_analytics'])->group(function () {
        Route::get('/analytics', [GovernanceController::class, 'analytics'])->name('analytics');
    });

    // Admin-only Pages
    Route::middleware(['role:admin', 'permission:manage_users'])->group(function () {
        Route::get('/admin/users', [AdminController::class, 'users'])->name('admin.users');
        Route::post('/admin/users', [AdminController::class, 'storeUser'])->name('admin.users.store');
        Route::post('/admin/users/{id}/reset-password', [AdminController::class, 'resetPassword'])->name('admin.users.reset-password');
    });

    Route::middleware(['role:admin', 'permission:view_audit_logs'])->group(function () {
        Route::get('/admin/audit-logs', [AdminController::class, 'auditLogs'])->name('admin.audit-logs');
    });

    Route::middleware(['role:admin', 'permission:manage_thematic_areas'])->group(function () {
        Route::get('/admin/thematic-areas', [AdminController::class, 'thematicAreas'])->name('admin.thematic-areas');
        Route::post('/admin/thematic-areas', [AdminController::class, 'storeThematicArea'])->name('admin.thematic-areas.store');
        Route::put('/admin/thematic-areas/{id}', [AdminController::class, 'updateThematicArea'])->name('admin.thematic-areas.update');
        Route::delete('/admin/thematic-areas/{id}', [AdminController::class, 'destroyThematicArea'])->name('admin.thematic-areas.destroy');
    });

    Route::middleware(['role:admin', 'permission:hrms_sync'])->group(function () {
        Route::get('/admin/hrms-sync', [AdminController::class, 'hrmsSync'])->name('admin.hrms-sync');
    });

    // Admin: Department & College Management
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/admin/departments', [AdminController::class, 'departments'])->name('admin.departments');
        Route::post('/admin/departments', [AdminController::class, 'storeDepartment'])->name('admin.departments.store');
        Route::put('/admin/departments/{id}', [AdminController::class, 'updateDepartment'])->name('admin.departments.update');
        Route::delete('/admin/departments/{id}', [AdminController::class, 'destroyDepartment'])->name('admin.departments.destroy');
        Route::get('/admin/colleges', [AdminController::class, 'colleges'])->name('admin.colleges');
        Route::post('/admin/colleges', [AdminController::class, 'storeCollege'])->name('admin.colleges.store');
        Route::put('/admin/colleges/{id}', [AdminController::class, 'updateCollege'])->name('admin.colleges.update');
        Route::delete('/admin/colleges/{id}', [AdminController::class, 'destroyCollege'])->name('admin.colleges.destroy');
    });
});
