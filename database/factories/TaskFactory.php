<?php

namespace Database\Factories;

use App\Models\Sprint;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'label' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'estimated_timestamp' => $this->faker->numberBetween(1, 10),
            'realized_timestamp' => $this->faker->numberBetween(1, 10),
            'deadline' => $this->faker->dateTimeBetween('now', '+1 week'),
            'is_finish' => $this->faker->boolean,
            'priority_id' => $this->faker->numberBetween(1, 3),
            'status_id' => $this->faker->numberBetween(1, 3),
        ];
    }
}
