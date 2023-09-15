<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class RewardFactory extends Factory
{

    public function definition()
    {
        return [
            'coupon' => $this->faker->text(25),
            'image' => $this->faker->imageUrl(),
            'description' => $this->faker->text(100),
            'brand' => $this->faker->text(25)
        ];
    }
}
