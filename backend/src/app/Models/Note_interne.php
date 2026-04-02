<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Note_interne extends Model
{
    use HasFactory;

    protected $fillable=[
        'ddemande_id',
        'contenu',
    ];
}
