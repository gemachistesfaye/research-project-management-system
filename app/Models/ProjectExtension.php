<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectExtension extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'extension_number',
        'requested_months',
        'reason',
        'approver_role',
        'status',
        'approved_by'
    ];

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id', 'project_id');
    }
}

