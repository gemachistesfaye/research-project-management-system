<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Evaluation extends Model
{
    use HasFactory;

    protected $primaryKey = 'eval_id';

    protected $fillable = [
        'project_id',
        'examiner_id',
        'score',
        'decision',
        'comments',
        'is_blind_masked',
        'evaluated_at'
    ];

    protected $casts = [
        'is_blind_masked' => 'boolean',
        'evaluated_at' => 'datetime',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id', 'project_id');
    }

    public function examiner()
    {
        return $this->belongsTo(User::class, 'examiner_id');
    }
}

