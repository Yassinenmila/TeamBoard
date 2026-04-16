<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Validation\Rules\In;

class Reunion extends Model
{
    use HasFactory;

    protected $fillable=[
        'user_id',
        'titre',
        'description',
        'date',
        'heure',
        'lieu',
    ];

    public function creator(){
        return $this->belongsTo(User::class,'user_id');
    }

    public function invitations(){
        return $this->hasMany(Invitation::class,'reunion_id');
    }

    public function notifications(){
        return $this->morphMany(Notification::class,'notifiable');
    }


}
