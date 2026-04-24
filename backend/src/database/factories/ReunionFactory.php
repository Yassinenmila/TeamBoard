<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Reunion;
use App\Models\User;

class ReunionFactory extends Factory
{
    protected $model = Reunion::class;

    public function definition(): array
    {
        return [
            'titre' => $this->faker->sentence(3),

            'description' => $this->faker->optional()->paragraph(),

            'date' => $this->faker->dateTimeBetween('now', '+1 month')->format('Y-m-d'),

            'heure' => $this->faker->time('H:i'),

            'lieu' => $this->faker->randomElement([
                'Salle A',
                'Salle B',
                'Salle réunion 1',
                'En ligne',
                'Bureau principal'
            ]),

            'user_id' => User::factory(),
        ];
    }

    /**
     * States utiles
     */

    public function aujourdHui()
    {
        return $this->state(fn () => [
            'date' => now()->format('Y-m-d'),
        ]);
    }

    public function enLigne()
    {
        return $this->state(fn () => [
            'lieu' => 'En ligne',
        ]);
    }
}
