<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Post::factory(25)
        ->recycle(\App\Models\Autor::all())
        ->recycle(\App\Models\Category::all())
        ->create()
        ->each(function (\App\Models\Post $post) {
            $post->tags()->attach(
                \App\Models\Tag::inRandomOrder()->take(rand(2, 4))->pluck('id')
            );
        });
    }
}
