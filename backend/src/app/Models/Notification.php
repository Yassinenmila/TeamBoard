<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'message',
        'lu',
        'notifiable_type',
        'notifiable_id',
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function notifiable(){
        return $this->morphTo();
    }

}
