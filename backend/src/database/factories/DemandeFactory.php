<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Demande;
use App\Models\User;

class DemandeFactory extends Factory
{
    protected $model = Demande::class;

    public function definition(): array
    {
        return [
            'type' => $this->faker->randomElement([
                'conge',
                'support',
                'inscription',
                'autre'
            ]),

            'description' => $this->faker->paragraph(),

            'status' => $this->faker->randomElement([
                'pending',
                'accepted',
                'rejected'
            ]),
            'date' => $this->faker->dateTimeBetween('now', '+1 month')->format('Y-m-d'),

            'user_id' => User::factory(),
        ];
    }

    /**
     * States
     */

    public function pending()
    {
        return $this->state(fn () => [
            'status' => 'pending',
        ]);
    }

    public function accepted()
    {
        return $this->state(fn () => [
            'status' => 'accepted',
        ]);
    }

    public function rejected()
    {
        return $this->state(fn () => [
            'status' => 'rejected',
        ]);
    }
}
