<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BudgetAmendment extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'delta_amount',
        'justification',
        'proforma_document_url',
        'status',
        'approved_by'
    ];

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id', 'project_id');
    }
}

