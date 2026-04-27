<?php

namespace Database\Factories;

use App\Models\Invitation;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Reunion;
/**
 * @extends Factory<Invitation>
 */
class InvitationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'reunion_id' => Reunion::inRandomOrder()->first()?->id ?? Reunion::factory(),
            'user_id' => User::inRandomOrder()->first()?->id ?? User::factory(),
            'status' => $this->faker->randomElement([
                'pending'
            ]),
        ];
    }
}
