<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Livrable extends Model
{
    use HasFactory;

    protected $fillable =[
        'tache_id',
        'user_id',
        'type',
        'status',
        'url',
    ];

    public function tache(){
        return $this->belongsTo(Tache::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function commentaires(){
        return $this->morphMany(Commentaire::class,'commentable');
    }

    public function notifications(){
        return $this->morphMany(Notification::class,'notifiable');
    }
}
