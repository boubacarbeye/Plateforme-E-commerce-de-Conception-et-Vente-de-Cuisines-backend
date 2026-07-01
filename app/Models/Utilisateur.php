<?php
// app/Models/Utilisateur.php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;

class Utilisateur extends Authenticatable implements JWTSubject
{
    use HasUuids, Notifiable;

    protected $fillable = ['nom', 'prenom', 'email', 'telephone', 'password', 'role'];

    // Hachage automatique en bcrypt à l'assignation
    public function setPasswordAttribute($value): void
    {
        $this->attributes['password'] = bcrypt($value);
    }

    // Méthodes requises par JWTSubject
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [
            'role' => $this->role,
            'nom' => $this->prenom . ' ' . $this->nom,
        ];
    }

    public function projets() {
        return $this->hasMany(ProjetCuisine::class, 'client_id');
    }
}