<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Profile>
 */
class ProfileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $id = random_int(1, 300);

        return [
            'avatar' => "https://picsum.photos/id/{$id}/760/380.webp",
            'bio' => $this->faker->text()
        ];
    }
}
