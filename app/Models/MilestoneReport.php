<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MilestoneReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'milestone_name',
        'progress_percentage',
        'summary_text',
        'deliverable_file_path',
        'deliverable_link_url',
        'deliverable_document_url',
        'status',
        'coordinator_feedback'
    ];

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id', 'project_id');
    }
}

