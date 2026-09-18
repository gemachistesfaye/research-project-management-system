<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IRERCClearance extends Model
{
    use HasFactory;

    protected $table = 'irerc_clearances';

    protected $fillable = [
        'project_id',
        'risk_level',
        'status',
        'clearance_code',
        'committee_notes',
        'issued_at'
    ];

    protected $casts = [
        'issued_at' => 'datetime',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id', 'project_id');
    }
}

