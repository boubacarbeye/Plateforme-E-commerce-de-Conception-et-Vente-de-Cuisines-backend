<?php

// app/Policies/ProjetPolicy.php

namespace App\Policies;

use App\Models\ProjetCuisine;
use App\Models\Utilisateur;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProjetPolicy
{
    use HandlesAuthorization;

    // Un client ne peut voir que ses projets (l'admin voit tout)
    public function view(Utilisateur $user, ProjetCuisine $projet): bool
    {
        return $user->role === 'admin' || $user->id === $projet->client_id;
    }

    // Un client ne peut modifier que ses propres projets
    public function update(Utilisateur $user, ProjetCuisine $projet): bool
    {
        return $user->role === 'admin' || $user->id === $projet->client_id;
    }

    // Un client ne peut demander un devis/rappel que pour ses propres projets
    public function demanderAction(Utilisateur $user, ProjetCuisine $projet): bool
    {
        return $user->role === 'admin' || $user->id === $projet->client_id;
    }
}
