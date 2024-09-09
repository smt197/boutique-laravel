<?php

namespace App\Policies;

use App\Models\Dette;
use App\Models\User;

class DettePolicy
{
    /**
     * Vérifie si l'utilisateur peut afficher la liste des articles.
     */
    public function viewAny(User $user)
    {
        return $user->role->nomRole === 'BOUTIQUIER' ;
    }

    /**
     * Vérifie si l'utilisateur peut afficher un article spécifique.
     */
    public function view(User $user, Dette $article)
    {
        return $user->role->nomRole === 'BOUTIQUIER';
    }

    /**
     * Vérifie si l'utilisateur peut créer un article.
     */
    public function create(User $user)
    {
        return $user->role->nomRole === 'BOUTIQUIER';
    }

}

