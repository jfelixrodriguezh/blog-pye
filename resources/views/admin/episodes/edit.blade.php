@extends('layouts.app')

@section('title', 'Editar episodio')

@section('content')
    <div class="mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb small">
                <li class="breadcrumb-item"><a href="{{ route('admin.episodes.index') }}">Episodios</a></li>
                <li class="breadcrumb-item active" aria-current="page">Editar</li>
            </ol>
        </nav>
    </div>

    <livewire:admin.episodes.form :episode="$episode" />
@endsection
