<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectTermination extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'reason',
        'total_disbursed',
        'verified_deliverables_value',
        'refund_due',
        'status'
    ];

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id', 'project_id');
    }
}

