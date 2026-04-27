<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Tache;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Annonce;
use App\Models\Demande;
use App\Models\Invitation;
use App\Models\Reunion;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->count(10)->create();
        Tache::factory()->count(10)->create();
        Annonce::factory()->count(10)->create();
        Demande::factory()->count(10)->create();
        Invitation::factory()->count(10)->create();
        Reunion::factory()->count(10)->create();
    }
}
