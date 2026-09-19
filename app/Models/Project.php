<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $primaryKey = 'project_id';

    protected $fillable = [
        'title',
        'abstract_text',
        'thematic_id',
        'pi_id',
        'dept_id',
        'requested_budget',
        'approved_budget',
        'status',
        'current_stage',
        'ethical_cleared',
        'proposal_document_url',
        'contract_signed_at',
        'pi_signature_date',
        'vp_signature_date',
        'feedback',
        'dh_screened_at',
        'under_review_at',
        'approved_at',
        'activated_at',
        'completed_at',
        'cancelled_at',
        'cancelled_by_pi',
        'cancellation_reason',
        'admin_cancel_notes',
    ];

    protected $casts = [
        'ethical_cleared' => 'boolean',
        'contract_signed_at' => 'datetime',
        'pi_signature_date' => 'datetime',
        'vp_signature_date' => 'datetime',
        'dh_screened_at' => 'datetime',
        'under_review_at' => 'datetime',
        'approved_at' => 'datetime',
        'activated_at' => 'datetime',
        'completed_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    public function pi()
    {
        return $this->belongsTo(User::class, 'pi_id');
    }

    public function thematicArea()
    {
        return $this->belongsTo(ThematicArea::class, 'thematic_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'dept_id');
    }

    public function members()
    {
        return $this->hasMany(ProjectMember::class, 'project_id', 'project_id');
    }

    public function evaluations()
    {
        return $this->hasMany(Evaluation::class, 'project_id', 'project_id');
    }

    public function irercClearance()
    {
        return $this->hasOne(IRERCClearance::class, 'project_id', 'project_id');
    }

    public function budgetRequests()
    {
        return $this->hasMany(BudgetRequest::class, 'project_id', 'project_id');
    }

    public function budgetAmendments()
    {
        return $this->hasMany(BudgetAmendment::class, 'project_id', 'project_id');
    }

    public function milestoneReports()
    {
        return $this->hasMany(MilestoneReport::class, 'project_id', 'project_id');
    }

    public function extensions()
    {
        return $this->hasMany(ProjectExtension::class, 'project_id', 'project_id');
    }

    public function piTransfers()
    {
        return $this->hasMany(PITransfer::class, 'project_id', 'project_id');
    }

    public function termination()
    {
        return $this->hasOne(ProjectTermination::class, 'project_id', 'project_id');
    }

    public function certificates()
    {
        return $this->hasMany(Certificate::class, 'project_id', 'project_id');
    }
}

