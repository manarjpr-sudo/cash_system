<?php

namespace App\Policies;

use App\Models\Operation;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class OperationPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return $user->hasPermission('manage_operations');
    }

    public function view(User $user, Operation $operation)
    {
        return $user->hasPermission('manage_operations');
    }

    public function create(User $user)
    {
        return $user->hasPermission('manage_operations');
    }

    public function update(User $user, Operation $operation)
    {
        return $user->hasPermission('manage_operations') && $operation->status === 'pending';
    }

    public function delete(User $user, Operation $operation)
    {
        return $user->hasPermission('manage_operations') && $operation->status === 'pending';
    }
}