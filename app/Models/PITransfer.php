<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PITransfer extends Model
{
    use HasFactory;

    protected $table = 'pi_transfers';

    protected $fillable = [
        'project_id',
        'old_pi_id',
        'new_pi_id',
        'reason',
        'consent_document_url',
        'status',
        'approved_by'
    ];

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id', 'project_id');
    }

    public function oldPi()
    {
        return $this->belongsTo(User::class, 'old_pi_id');
    }

    public function newPi()
    {
        return $this->belongsTo(User::class, 'new_pi_id');
    }
}

