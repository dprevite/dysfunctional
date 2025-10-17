<?php

namespace Database\Factories;

use App\Models\Variable;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Variable>
 */
class VariableFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'path' => fake()->slug(2) . '/' . fake()->slug(2),
            'type' => fake()->randomElement(['function', 'runtime']),
            'name' => strtoupper(fake()->word()) . '_' . strtoupper(fake()->word()),
            'value' => fake()->uuid(),
            'is_secret' => fake()->boolean(),
        ];
    }

    /**
     * Indicate that the variable is a secret.
     */
    public function secret(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_secret' => true,
        ]);
    }

    /**
     * Indicate that the variable is not a secret.
     */
    public function plainText(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_secret' => false,
        ]);
    }
}
