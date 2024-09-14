<?php

namespace App\Policies;

use App\Models\User;

class DetteArchivePolicy
{
    /**
     * Vérifie si l'utilisateur peut afficher la liste des dettes archivées.
     */
    public function viewAnys(User $user)
    {
        return $user->role->nomRole === 'ADMIN';
    }

    /**
     * Vérifie si l'utilisateur peut afficher une dette archivée spécifique.
     */
    public function view(User $user, $dette)
    {
        return $user->role->nomRole === 'ADMIN';
    }

    /**
     * Vérifie si l'utilisateur peut enregistrer des dettes archivées.
     */
    public function create(User $user)
    {
        return $user->role->nomRole === 'ADMIN';
    }
}
