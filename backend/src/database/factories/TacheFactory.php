<?php

namespace Database\Factories;

use App\Models\Tache;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

class TacheFactory extends Factory
{
    protected $model = Tache::class;

    public function definition(): array
    {
        return [
            'titre' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(2),
            'status' => $this->faker->randomElement([
                'en cours'
            ]),
            'date_limite' => $this->faker->optional()->dateTimeBetween('now', '+1 month'),
            'created_by' => User::factory(),
        ];
    }
}
