<?php

namespace Database\Factories;

use App\Models\Post;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Post>
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
        $title = fake()->sentence(6);
        $status = fake()->randomElement(['draft', 'published', 'published', 'published', 'archived']);

        return [
            'autor_id' => \App\Models\Autor::factory(),
            'title' => rtrim($title, '.'),
            'category_id' => \App\Models\Category::factory(),
            'slug' => \Illuminate\Support\Str::slug($title) . '-' . fake()->unique()->numberBetween(1000, 9999),
            'content' => fake()->paragraphs(6, true),
            'summary' => fake()->sentence(20),
            'status' => $status,
            'post_image' => null,
            'meta_title' => null,
            'meta_description' => null,
            'published_at' => $status === 'published' ? fake()->dateTimeBetween('-1 year', 'now') : null,
        ];
    }
}
