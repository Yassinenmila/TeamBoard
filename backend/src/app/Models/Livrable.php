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
        'url',
    ];
}
