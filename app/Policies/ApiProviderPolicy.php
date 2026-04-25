<?php

namespace App\Policies;

use App\Models\ApiProvider;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ApiProviderPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view-api-provider');
    }

    public function view(User $user, ApiProvider $apiProvider): bool
    {
        return $user->hasPermissionTo('view-api-provider');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create-api-provider');
    }

    public function update(User $user, ApiProvider $apiProvider): bool
    {
        return $user->hasPermissionTo('edit-api-provider');
    }

    public function delete(User $user, ApiProvider $apiProvider): bool
    {
        return $user->hasPermissionTo('delete-api-provider');
    }
}
