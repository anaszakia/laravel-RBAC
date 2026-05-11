<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    protected $fillable = ['name', 'email', 'password', 'avatar', 'phone', 'address'];

    protected $hidden = ['password', 'remember_token'];

    // Primary role (one-to-many via role_id column)
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    // Multiple roles (many-to-many via user_role pivot table)
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_role');
    }

    // Cek apakah user punya role tertentu (by slug)
    public function hasRole(string $slug): bool
    {
        return $this->roles->contains('slug', $slug);
    }

    public function hasPermission(string $slug): bool
    {
        // Cache agar tidak query berulang dalam satu request
        return $this->roles
            ->flatMap(fn($role) => $role->permissions)
            ->contains('slug', $slug);
    }

    public function hasAnyPermission(array $slugs): bool
    {
        return $this->roles
            ->flatMap(fn($role) => $role->permissions)
            ->whereIn('slug', $slugs)
            ->isNotEmpty();
    }

    // Helper: ambil nama primary role
    public function getRoleNameAttribute(): string
    {
        return $this->role?->name ?? $this->roles->first()?->name ?? 'No Role';
    }

    public function getAvatarUrlAttribute(): string
    {
        return minio_avatar($this->avatar, $this->name);
    }
}