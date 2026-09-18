<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Cache;

class RbacService
{
    private static ?array $rolePermissionsCache = null;

    public static function getPermissionsForRole(string $roleName): array
    {
        $cacheKey = "rbac_role_{$roleName}_permissions";

        return Cache::remember($cacheKey, 3600, function () use ($roleName) {
            $role = \App\Models\Role::where('name', $roleName)
                ->with('permissions')
                ->first();

            return $role ? $role->permissions->pluck('name')->toArray() : [];
        });
    }

    public static function userHasPermission(User $user, string $permission): bool
    {
        $permissions = static::getPermissionsForRole($user->role);

        return in_array($permission, $permissions);
    }

    public static function userHasAnyPermission(User $user, array $permissions): bool
    {
        $userPermissions = static::getPermissionsForRole($user->role);

        return count(array_intersect($permissions, $userPermissions)) > 0;
    }

    public static function getAllPermissions(): array
    {
        return Cache::remember('rbac_all_permissions', 3600, function () {
            return \App\Models\Permission::pluck('name')->toArray();
        });
    }

    public static function getAllRoles(): array
    {
        return Cache::remember('rbac_all_roles', 3600, function () {
            return \App\Models\Role::with('permissions')->get();
        });
    }

    public static function syncUserRole(User $user): void
    {
        $role = \App\Models\Role::where('name', $user->role)->first();

        if ($role) {
            $user->update(['role_id' => $role->id]);
            Cache::forget("rbac_role_{$user->role}_permissions");
        }
    }

    public static function clearCache(): void
    {
        Cache::forget('rbac_all_permissions');
        Cache::forget('rbac_all_roles');

        $roles = ['pi', 'tm', 'reviewer', 'dh', 'coordinator', 'dean', 'irerc', 'vparttcs', 'rcsc', 'finance', 'admin'];
        foreach ($roles as $role) {
            Cache::forget("rbac_role_{$role}_permissions");
        }
    }
}
