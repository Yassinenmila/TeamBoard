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
        'created_by'
    ];

    public function createur(){
        return $this->belongsTo(User::class,'created_by');
    }

    public function utilisateurs(){
        return $this->belongsToMany(User::class,'affectations')->withPivot('status')->withTimestamps();
    }

    public function commentaires(){
        return $this->morphMany(Commentaire::class,'commentable');
    }

    public function notifications(){
        return $this->morphMany(Notification::class,'notifiable');
    }
}
