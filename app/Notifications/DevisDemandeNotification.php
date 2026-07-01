<?php

namespace App\Notifications;

use App\Models\ProjetCuisine;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class DevisDemandeNotification extends Notification
{
    use Queueable;

    public function __construct(public ProjetCuisine $projet, public string $typeDemande) {}

    public function via($notifiable): array { return ['database']; }

    public function toArray($notifiable): array
    {
        return [
            'projet_id' => $this->projet->id,
            'client_nom' => $this->projet->client->nom ?? 'Visiteur',
            'message' => "Nouvelle demande de {$this->typeDemande} pour le projet #{$this->projet->id}",
            'url' => "/admin/projets/{$this->projet->id}"
        ];
    }
}
