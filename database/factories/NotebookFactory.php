<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;


class NotebookFactory extends Factory
{

    public function definition(): array
    {
        return [
            'full_name' => $this->faker->name(),
            'phone' => $this->faker->phoneNumber(),
            'email' => $this->faker->unique()->safeEmail(),
            'birth_date' => $this->faker->date(),
            'company' => $this->faker->company(),
        ];
    }
}
