<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;

class Utilisateur extends Authenticatable implements JWTSubject
{
    use Notifiable;

 
    protected $table = 'utilisateurs';
    
    protected $primaryKey = 'Utilisateur_id';
    

    public $incrementing = false;
    protected $keyType = 'string';

    
    public $timestamps = false; 

    protected $fillable = [
        'Utilisateur_id', 'nom', 'prenom', 'email', 'motDePasse', 'telephone', 'role'
    ];

    protected $hidden = [
        'motDePasse',
    ];


    public $authPasswordName = 'motDePasse';

    protected function casts(): array
    {
        return [
            'motDePasse' => 'hashed',
        ];
    }
    public function getAuthPassword()
    {
        return $this->motDePasse;
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return ['role' => $this->role];
    }
}