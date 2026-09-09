<?php

use Illuminate\Support\Facades\Route;
use App\Models\Post;
use App\Models\Category;
use App\Models\Tag;
use App\Models\Libro;
use App\Models\Testamento;
use App\Models\Episode;
use App\Models\Himno;
use App\Models\Himnario;
use App\Models\Podcast;
use App\Models\Autor;
use Illuminate\Support\Facades\Storage;

Route::view('/', 'home')->name('home');
Route::get('/post/{post:slug}', function (Post $post) {
    abort_unless($post->status === 'published', 404);

    $post->load(['autor', 'category', 'tags']);

    return view('post-show', [
        'post' => $post,
        'related' => Post::where('status', 'published')
            ->where('id', '!=', $post->id)
            ->where('category_id', $post->category_id)
            ->latest('published_at')
            ->take(2)
            ->get(),
        'previous' => Post::where('status', 'published')
            ->where('published_at', '<', $post->published_at)
            ->orderByDesc('published_at')
            ->first(),
        'next' => Post::where('status', 'published')
            ->where('published_at', '>', $post->published_at)
            ->orderBy('published_at')
            ->first(),
    ]);
})->name('post.show');

Route::get('/buscar', function () {
    return view('search');
})->name('search');


Route::get('/biblia', function () {
    return redirect()->route('biblia.leer', ['libro' => 'genesis', 'capitulo' => 1]);
})->name('biblia.index');

Route::get('/biblia/{libro}/{capitulo}', function (Libro $libro, int $capitulo) {
    $versiculos = $libro->versiculos()->where('capitulo', $capitulo)->orderBy('numero')->get();

    abort_if($versiculos->isEmpty(), 404);

    $totalCapitulos = $libro->versiculos()->max('capitulo');

    if ($capitulo > 1) {
        $anterior = ['libro' => $libro, 'capitulo' => $capitulo - 1];
    } else {
        $libroAnterior = Libro::where('orden', '<', $libro->orden)->orderByDesc('orden')->first();
        $anterior = $libroAnterior
            ? ['libro' => $libroAnterior, 'capitulo' => $libroAnterior->versiculos()->max('capitulo')]
            : null;
    }

    if ($capitulo < $totalCapitulos) {
        $siguiente = ['libro' => $libro, 'capitulo' => $capitulo + 1];
    } else {
        $libroSiguiente = Libro::where('orden', '>', $libro->orden)->orderBy('orden')->first();
        $siguiente = $libroSiguiente ? ['libro' => $libroSiguiente, 'capitulo' => 1] : null;
    }

    return view('biblia.leer', [
        'libro' => $libro,
        'capitulo' => $capitulo,
        'versiculos' => $versiculos,
        'totalCapitulos' => $totalCapitulos,
        'anterior' => $anterior,
        'siguiente' => $siguiente,
        'testamentos' => Testamento::with('libros')->orderBy('orden')->get(),
    ]);
})->name('biblia.leer');

Route::get('/biblia/buscar', function () {
    return view('biblia.buscar');
})->name('biblia.buscar');

Route::get('/podcasts', function () {
    return view('podcasts.index');
})->name('podcasts.index');

Route::get('/podcasts/{episode}', function (Episode $episode) {
    abort_unless($episode->status === 'published', 404);

    $episode->load(['podcast', 'autor', 'categories', 'tags']);

    return view('podcasts.show', [
        'episode' => $episode,
        'related' => Episode::where('status', 'published')
            ->where('id', '!=', $episode->id)
            ->where('podcast_id', $episode->podcast_id)
            ->latest('published_at')
            ->take(2)
            ->get(),
        'previous' => Episode::where('status', 'published')
            ->where('podcast_id', $episode->podcast_id)
            ->where('published_at', '<', $episode->published_at)
            ->orderByDesc('published_at')
            ->first(),
        'next' => Episode::where('status', 'published')
            ->where('podcast_id', $episode->podcast_id)
            ->where('published_at', '>', $episode->published_at)
            ->orderBy('published_at')
            ->first(),
    ]);
})->name('podcasts.show');

Route::get('/himnos', function () {
    return view('himnos.index');
})->name('himnos.index');

Route::get('/audio/himno/{himno}', function (Himno $himno) {
    abort_unless($himno->es_local, 404);
    abort_unless($himno->audio_url, 404);

    $path = \Illuminate\Support\Facades\Storage::disk('public')->path($himno->audio_url);

    abort_unless(file_exists($path), 404);

    return response()->file($path);
})->name('audio.himno');

Route::get('/himnos/{himnario}/{numero}', function (Himnario $himnario, int $numero) {
    $himno = Himno::where('himnario_id', $himnario->id)
        ->where('numero', $numero)
        ->firstOrFail();

    $himno->load(['himnario', 'autor', 'category', 'estrofas']);

    return view('himnos.show', ['himno' => $himno]);
})->name('himnos.show');

Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::post('/logout', function () {
    \Illuminate\Support\Facades\Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect()->route('login');
})->name('logout');

Route::get('/audio/episodio/{episode}', function (Episode $episode) {
    abort_unless($episode->es_local, 404);

    $path = Storage::disk('public')->path($episode->audio_url);

    abort_unless(file_exists($path), 404);

    return response()->file($path);
})->name('audio.episode');


Route::prefix('admin')->name('admin.')->middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('admin.dashboard', [
            'stats' => [
                'posts' => [
                    'total' => Post::count(),
                    'published' => Post::where('status', 'published')->count(),
                ],
                'podcasts' => Podcast::count(),
                'episodes' => [
                    'total' => Episode::count(),
                    'published' => Episode::where('status', 'published')->count(),
                ],
                'himnos' => [
                    'total' => Himno::count(),
                    'published' => Himno::where('status', 'published')->count(),
                ],
                'autores' => Autor::count(),
                'categorias' => Category::count(),
            ],
            'recentPosts' => Post::with('autor')->latest()->take(5)->get(),
            'recentEpisodes' => Episode::with(['podcast', 'autor'])->latest()->take(5)->get(),
        ]);
    })->name('dashboard');

    Route::middleware('permission:manage users')->group(function () {
        Route::view('/usuarios', 'admin.users.index')->name('users.index');
    });

    Route::view('/roles', 'admin.roles.index')->name('roles.index');
    Route::view('/roles/crear', 'admin.roles.create')->name('roles.create');
    Route::get('/roles/{role}/editar', function (\Spatie\Permission\Models\Role $role) {
        return view('admin.roles.edit', ['role' => $role]);
    })->name('roles.edit');

    Route::middleware('permission:manage posts')->group(function () {
        Route::view('/posts', 'admin.posts.index')->name('posts.index');
        Route::view('/posts/crear', 'admin.posts.create')->name('posts.create');
        Route::get('/posts/{post}/editar', function (Post $post) {
            return view('admin.posts.edit', ['post' => $post]);
        })->name('posts.edit');
    });

    Route::middleware('permission:manage podcasts')->group(function () {
        Route::view('/podcasts', 'admin.podcasts.index')->name('podcasts.index');
        Route::view('/episodes', 'admin.episodes.index')->name('episodes.index');
        Route::view('/episodes/crear', 'admin.episodes.create')->name('episodes.create');
        Route::get('/episodes/{episode}/editar', function (Episode $episode) {
            return view('admin.episodes.edit', ['episode' => $episode]);
        })->name('episodes.edit');
    });

    Route::middleware('permission:manage himnos')->group(function () {
        Route::view('/himnarios', 'admin.himnarios.index')->name('himnarios.index');
        Route::view('/tonos', 'admin.tonos.index')->name('tonos.index');
        Route::view('/himnos', 'admin.himnos.index')->name('himnos.index');
        Route::view('/himnos/crear', 'admin.himnos.create')->name('himnos.create');
        Route::get('/himnos/{himno}/editar', function (Himno $himno) {
            return view('admin.himnos.edit', ['himno' => $himno]);
        })->name('himnos.edit');
    });

    Route::middleware('permission:manage taxonomies')->group(function () {
        Route::view('/autors', 'admin.autors.index')->name('autors.index');
        Route::view('/categories', 'admin.categories.index')->name('categories.index');
        Route::view('/tags', 'admin.tags.index')->name('tags.index');
    });
});


