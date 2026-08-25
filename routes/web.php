<?php

use Illuminate\Support\Facades\Route;
use App\Models\Post;
use App\Models\Category;
use App\Models\Tag;
use App\Models\Libro;
use App\Models\Testamento;

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













Route::prefix('admin')->name('admin.')->group(function () {
    Route::view('/posts', 'admin.posts.index')->name('posts.index');
    Route::view('/posts/crear', 'admin.posts.create')->name('posts.create');
    Route::get('/posts/{post}/editar', function (Post $post) {
        return view('admin.posts.edit', ['post' => $post]);
    })->name('posts.edit');
    Route::view('/autors', 'admin.autors.index')->name('autors.index');
    Route::view('/categories', 'admin.categories.index')->name('categories.index');
    Route::view('/tags', 'admin.tags.index')->name('tags.index');


});


