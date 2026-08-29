@extends('layouts.app')

@section('title', 'Podcasts')

@section('content')
    <div class="mb-4">
        <h1 class="h2 fw-bold mb-1">Podcasts</h1>
        <p class="text-muted mb-0">Gestiona los programas de podcast.</p>
    </div>

    <livewire:admin.podcasts.index />
@endsection
