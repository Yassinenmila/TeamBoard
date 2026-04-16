<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use app\Models\User;
use app\Models\Reunion;

class Invitation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'reunion_id',
        'status',
    ];

    public function reunion()
    {
        return $this->belongsTo(Reunion::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
