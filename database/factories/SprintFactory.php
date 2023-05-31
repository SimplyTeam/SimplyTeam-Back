<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Sprint>
 */
class SprintFactory extends Factory
{
    public function definition()
    {
        return [
            'name' => $this->faker->sentence(3),
            'begin_date' => $this->faker->date(),
            'end_date' => $this->faker->date(),
            'closing_date' => $this->faker->date(),
        ];
    }
}
