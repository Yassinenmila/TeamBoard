<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;


#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens,HasFactory, Notifiable;

    protected $fillable= [
        'name',
        'email',
        'password',
        'role',
    ];

    protected $hidden =[
        'password',
        'remember_token'
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function demandes(){
        return $this->hasMany(Demande::class);
    }

    public function presences(){
        return $this->hasMany(Presence::class);
    }

    public function reunions(){
        return $this->belongsToMany(Reunion::class, 'invitations', 'user_id', 'reunion_id')
                ->withPivot('statut')
                ->withTimestamps();
    }

    public function notifications(){
        return $this->hasMany(Notification::class);
    }

    public function tachesCrees(){
        return $this->hasMany(Tache::class,'created_by');
    }

    public function tachesAssignes(){
        return $this->hasMany(Tache::class,'affectations')->withPivot('status')->withTimestamps();
    }

    public function livrables(){
        return $this->hasMany(Livrable::class);
    }

    public function commentaires(){
        return $this->hasMany(Commentaire::class);
    }

    public function annonces(){
        return $this->hasMany(Annonce::class);
    }
}
