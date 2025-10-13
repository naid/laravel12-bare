<?php

namespace App\Policies;

use App\Models\Client;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ClientPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // All authenticated users can view the clients list
        // But they'll only see clients they have access to
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Client $client): bool
    {
        // Admins can view all clients
        if ($user->isAdmin()) {
            return true;
        }

        // Check if user has access to this specific client
        return $user->canAccessClient($client);
    }

    /**
     * Determine whether the user can select/set a client globally.
     */
    public function select(User $user, Client $client): bool|Response
    {
        // Admins can select any client
        if ($user->isAdmin()) {
            return true;
        }

        // Check if user has access to this specific client
        if ($user->canAccessClient($client)) {
            return true;
        }

        return Response::deny('You do not have permission to access this client.');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Only admins and managers can create clients
        return $user->isAdmin() || $user->isManager();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Client $client): bool
    {
        // Admins can update any client
        if ($user->isAdmin()) {
            return true;
        }

        // Managers can update clients they have 'write' or 'admin' access to
        if ($user->isManager()) {
            $pivot = $user->clients()->where('client_id', $client->id)->first();
            return $pivot && in_array($pivot->pivot->access_level, ['write', 'admin']);
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Client $client): bool
    {
        // Only admins can delete clients
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Client $client): bool
    {
        // Only admins can restore clients
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Client $client): bool
    {
        // Only admins can permanently delete clients
        return $user->isAdmin();
    }
}
