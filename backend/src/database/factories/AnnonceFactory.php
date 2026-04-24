<?php

namespace Database\Factories;

use App\Models\Annonce;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

/**
 * @extends Factory<Annonce>
 */
class AnnonceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'titre' => $this->faker->sentence(4),
            'contenu' => $this->faker->paragraph(3),

            'type' => $this->faker->randomElement([
                'urgent',
                'general'
            ]),

            'user_id' => User::factory(),
        ];
    }
}
