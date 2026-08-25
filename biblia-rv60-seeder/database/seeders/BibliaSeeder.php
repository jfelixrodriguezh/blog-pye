<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BibliaSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('seeders/data/biblia');
        $now = now();

        $testamentos = collect(json_decode(file_get_contents("$path/testamentos.json"), true))
            ->map(fn ($t) => $t + ['created_at' => $now, 'updated_at' => $now]);
        DB::table('testamentos')->insert($testamentos->all());

        $libros = collect(json_decode(file_get_contents("$path/libros.json"), true))
            ->map(fn ($l) => $l + ['created_at' => $now, 'updated_at' => $now]);
        DB::table('libros')->insert($libros->all());

        $versiculos = json_decode(file_get_contents("$path/versiculos.json"), true);

        collect($versiculos)->chunk(1000)->each(function ($chunk) {
            DB::table('versiculos')->insert($chunk->all());
        });

        $this->command->info(count($versiculos).' versículos importados ('.count($libros).' libros).');
    }
}
