<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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

    public function invitations(){
        return $this->belongsToMany(User::class,'invitations')->withPivot('status')->withTimestamps();
    }
}
