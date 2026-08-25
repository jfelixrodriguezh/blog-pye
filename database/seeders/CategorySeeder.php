<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        collect(['Tecnología', 'Viajes', 'Salud', 'Estilo de vida', 'Fotografía'])->each(function ($name) {
            \App\Models\Category::create([
                'name' => $name,
                'slug' => \Illuminate\Support\Str::slug($name),
            ]);
        });
    }
}
