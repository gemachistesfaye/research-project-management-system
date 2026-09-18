<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'staff_id',
        'name',
        'email',
        'password',
        'role',
        'role_id',
        'dept_id',
        'status',
        'last_login_at',
        'avatar_path',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at'     => 'datetime',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class, 'dept_id');
    }

    public function projectsAsPi()
    {
        return $this->hasMany(Project::class, 'pi_id', 'id');
    }

    public function evaluations()
    {
        return $this->hasMany(Evaluation::class, 'examiner_id', 'id');
    }

    public function assignedRole()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function hasRole($role)
    {
        return $this->role === $role;
    }

    public function hasPermission($permissionName)
    {
        if (!$this->assignedRole) {
            return false;
        }

        return $this->assignedRole->permissions->contains('name', $permissionName);
    }

    public function hasAnyPermission(array $permissionNames)
    {
        if (!$this->assignedRole) {
            return false;
        }

        return $this->assignedRole->permissions->whereIn('name', $permissionNames)->isNotEmpty();
    }

    public function getAllPermissionNames()
    {
        if (!$this->assignedRole) {
            return collect();
        }

        return $this->assignedRole->permissions->pluck('name');
    }
}
