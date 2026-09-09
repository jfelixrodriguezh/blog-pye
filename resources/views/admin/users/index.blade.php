@extends('layouts.app')

@section('title', 'Usuarios')

@section('content')
    <div class="mb-4">
        <h1 class="h2 fw-bold mb-1">Usuarios</h1>
        <p class="text-muted mb-0">Gestiona quién puede entrar al panel y con qué rol.</p>
    </div>

    <livewire:admin.users.index />
@endsection
