<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view_users');
    }

    public function view(User $user, User $model): bool
    {
        return $user->id === $model->id || $user->hasPermissionTo('view_users');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create_users');
    }

    public function update(User $user, User $model): bool
    {
        return $user->id === $model->id || $user->hasPermissionTo('edit_users');
    }

    public function delete(User $user, User $model): bool
    {
        return $user->id !== $model->id && $user->hasPermissionTo('delete_users');
    }

    public function assignRoles(User $user): bool
    {
        return $user->hasPermissionTo('manage_roles');
    }
}