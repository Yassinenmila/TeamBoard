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
use App\Models\Tache;
use App\Models\Demande;
use App\Models\Presence;
use App\Models\Reunion;
use App\Models\Notification;
use App\Models\Commentaire;
use App\Models\Annonce;
use Illuminate\Validation\Rules\In;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens,HasFactory, Notifiable;

    protected $fillable= [
        'first_name',
        'last_name',
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

    public function reunionsCreees(){
        return $this->hasMany(Reunion::class,'user_id');
    }

    public function demandes(){
        return $this->hasMany(Demande::class);
    }

    public function presences(){
        return $this->hasMany(Presence::class);
    }

    public function invitations(){
        return $this->hasMany(Invitation::class);
    }

    public function reunions(){
        return $this->belongsToMany(Reunion::class, 'invitations', 'user_id', 'reunion_id')
                    ->withPivot('status')
                    ->withTimestamps();
    }

    public function notifications(){
        return $this->hasMany(Notification::class);
    }

    public function tachesCrees(){
        return $this->hasMany(Tache::class,'created_by');
    }

    public function tachesAssignes(){
        return $this->belongsToMany(Tache::class, 'affectations', 'user_id', 'tache_id')->withPivot('status')->withTimestamps();
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
