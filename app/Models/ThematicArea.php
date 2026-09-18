<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ThematicArea extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'category', 'description', 'is_active'];

    public function projects()
    {
        return $this->hasMany(Project::class, 'thematic_id');
    }
}

