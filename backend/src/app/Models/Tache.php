<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tache extends Model
{
    use HasFactory;

    protected $fillable=[
        'titre',
        'description',
        'status',
        'date_limite',
    ];

    public function creator(){
        return $this->belongsTo(User::class,'created_by');
    }

    public function livrables(){
        return $this->hasMany(Livrable::class);
    }

    public function utilisateurs(){
        return $this->belongsToMany(User::class,'affectations')->withPivot('status')->withTimestamps();
    }

    public function commentaires(){
        return $this->morphMany(Commentaire::class,'commentable');
    }
}
