<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = $this->faker->sentence();
        $id = random_int(1, 300);
        $paragraphs = $this->faker->paragraphs(random_int(1, 20));
        $body = array_map(function (string $paragraph) {
            $el = $this->faker->randomElement(['p', 'p', 'h2', 'p', 'p', 'p', 'h3', 'p', 'p', 'blockquote', 'p', 'p', 'p', 'p', 'p', 'p']);
            return "<$el>{$paragraph}</$el>";
        }, $paragraphs);

        return [
            'user_id' => User::inRandomOrder()->first(),
            'topic_id' => User::inRandomOrder()->first(),
            'title' => $title,
            'slug' => Str::slug($title),
            'body' => implode("", $body),
            'preview' => $paragraphs[0],
            'featured_image' => $this->faker->randomElement(["https://picsum.photos/id/{$id}/760/380.webp", null]),
        ];
    }
}
