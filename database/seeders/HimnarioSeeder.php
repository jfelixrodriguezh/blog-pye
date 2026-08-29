<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class HimnarioSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('seeders/data/himnario');
        $now = now();

        // Himnarios y Tonos son tablas nuevas: preservamos los IDs originales.
        $himnarios = collect(json_decode(file_get_contents("$path/himnarios.json"), true))
            ->map(fn ($h) => $h + ['created_at' => $now, 'updated_at' => $now]);
        DB::table('himnarios')->insert($himnarios->all());

        $tonos = collect(json_decode(file_get_contents("$path/tonos.json"), true))
            ->map(fn ($t) => $t + ['created_at' => $now, 'updated_at' => $now]);
        DB::table('tonos')->insert($tonos->all());

        // Categorias y Autores de himnos van a tablas COMPARTIDAS que ya tienen
        // datos del blog/podcasts, así que dejamos que la base asigne IDs nuevos
        // y guardamos un mapa "id original del sqlite -> id real nuevo".
        $categoriasHimnos = json_decode(file_get_contents("$path/categorias_himnos.json"), true);
        $mapaCategorias = [];
        foreach ($categoriasHimnos as $cat) {
            $id = DB::table('categories')->insertGetId([
                'name' => $cat['nombre'],
                'slug' => Str::slug($cat['nombre']),
                'description' => null,
                'aplica_posts' => false,
                'aplica_podcasts' => false,
                'aplica_himnos' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
            $mapaCategorias[$cat['id_original']] = $id;
        }

        $autoresHimnos = json_decode(file_get_contents("$path/autores_himnos.json"), true);
        $mapaAutores = [];
        foreach ($autoresHimnos as $autor) {
            $id = DB::table('autors')->insertGetId([
                'name' => $autor['nombre'],
                'description' => null,
                'photo' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
            $mapaAutores[$autor['id_original']] = $id;
        }

        // Himnos: resolvemos los mapas anteriores.
        $himnos = json_decode(file_get_contents("$path/himnos.json"), true);
        $himnosParaInsertar = collect($himnos)->map(function ($h) use ($mapaCategorias, $mapaAutores, $now) {
            return [
                'id' => $h['id'],
                'himnario_id' => $h['himnario_id'],
                'autor_id' => $h['autor_id_original'] ? ($mapaAutores[$h['autor_id_original']] ?? null) : null,
                'category_id' => $h['category_id_original'] ? ($mapaCategorias[$h['category_id_original']] ?? null) : null,
                'tono_id' => $h['tono_id'],
                'titulo' => $h['titulo'],
                'numero' => $h['numero'],
                'referencia' => $h['referencia'],
                'es_local' => $h['es_local'],
                'audio_url' => $h['audio_url'],
                'partitura' => $h['partitura'],
                'status' => $h['status'],
                'published_at' => $h['published_at'],
                'created_at' => $now,
                'updated_at' => $now,
            ];
        });

        $himnosParaInsertar->chunk(200)->each(function ($chunk) {
            DB::table('himnos')->insert($chunk->all());
        });

        // Estrofas: el himno_id ya coincide directo con los IDs preservados arriba.
        $estrofas = json_decode(file_get_contents("$path/estrofas.json"), true);
        collect($estrofas)->chunk(500)->each(function ($chunk) {
            DB::table('estrofas')->insert($chunk->all());
        });

        $this->command->info(count($himnos).' himnos y '.count($estrofas).' estrofas importados.');
    }
}
