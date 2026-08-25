@extends('layouts.app')
@section('title', 'Editar categoría')
@section('content')
    <h1 class="h2 fw-bold mb-4">Editar categoría</h1>
    <livewire:admin.categories.form :category="$category" />
@endsection
