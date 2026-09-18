<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BudgetRequest extends Model
{
    use HasFactory;

    protected $primaryKey = 'request_id';

    protected $fillable = [
        'project_id',
        'milestone_phase',
        'requested_amount',
        'approved_amount',
        'approval_tier',
        'status',
        'approved_by',
        'disbursed_at',
        'payment_method',
        'notes',
    ];

    protected $casts = [
        'disbursed_at' => 'datetime',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id', 'project_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}

