<?php

namespace Database\Factories;

use App\Models\Model;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Tache;

/**
 * @extends Factory<Model>
 */
class AffectationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'tache_id' => Tache::inRandomOrder()->first()?->id ?? Tache::factory(),
            'user_id' => User::inRandomOrder()->first()?->id ?? User::factory(),
            'status' => $this->faker->randomElement([
                'up_comming',
                'in_progress',
                'completed'
            ]),
        ];
    }
}
