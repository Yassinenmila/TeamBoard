<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;
use App\Models\Notification;

class Reunion extends Model
{
    use HasFactory;

    protected $fillable=[
        'user_id',
        'titre',
        'description',
        'date',
        'lieu',
    ];

    public function creator(){
        return $this->belongsTo(User::class,'created_by');
    }

    public function participants(){
        return $this->belongsToMany(User::class,'invitations')->withPivot('status')->withTimestamps();
    }

    public function notifications(){
        return $this->hasMany(Notification::class,'notifiable');
    }


}
