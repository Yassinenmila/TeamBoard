<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Demande extends Model
{
    use HasFactory;

    protected $fillable =[
        'type',
        'description',
        'status',
    ];

    public function commentaires(){
        return $this->morphMany(Commentaire::class,'commentable');
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function notifications(){
        return $this->morphMany(Notification::class,'notifiable');
    }
}
