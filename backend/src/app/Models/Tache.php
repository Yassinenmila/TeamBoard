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
        'priorite',
        'date_limite',
    ];
}
